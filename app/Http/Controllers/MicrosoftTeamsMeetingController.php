<?php

namespace App\Http\Controllers;

use Exception;

class MicrosoftTeamsMeetingController extends Controller
{
    /**
     * Get an application-only (client credentials) access token for Microsoft Graph.
     */
    public static function createToken()
    {
        $tenantId     = get_settings('teams_tenant_id');
        $clientId     = get_settings('teams_client_id');
        $clientSecret = get_settings('teams_client_secret');

        if (!$tenantId || !$clientId || !$clientSecret) {
            return null;
        }

        $tokenUrl = 'https://login.microsoftonline.com/' . rawurlencode($tenantId) . '/oauth2/v2.0/token';

        $postFields = http_build_query([
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'scope'         => 'https://graph.microsoft.com/.default',
            'grant_type'    => 'client_credentials',
        ]);

        try {
            $ch = curl_init($tokenUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $decoded = json_decode($response, true);

            if ($httpCode == 200 && isset($decoded['access_token'])) {
                return $decoded['access_token'];
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get User Object ID (GUID) from Microsoft Graph using User Principal Name (Email).
     */
    private static function getUserIdByEmail($email, $token)
    {
        $url = 'https://graph.microsoft.com/v1.0/users/' . rawurlencode($email) . '?$select=id';
        $response = self::request('GET', $url, $token);
        $data = json_decode($response, true);

        return $data['id'] ?? null;
    }

    /**
     * Create a Microsoft Teams online meeting on behalf of the configured organizer.
     */
    public static function createMeeting($topic, $start_time, $end_time = null)
    {
        $organizer = get_settings('teams_organizer_email');
        $token     = self::createToken();

        if (!$token) {
            return json_encode(['error' => ['message' => 'Failed to generate Microsoft Teams access token. Please check the tenant/client credentials.']]);
        }

        if (!$organizer) {
            return json_encode(['error' => ['message' => 'Microsoft Teams organizer email is not configured.']]);
        }

        // جلب الـ GUID الفعلي للمستخدم باستخدام البريد الإلكتروني لتجنب خطأ الـ GUID
        $userId = self::getUserIdByEmail($organizer, $token);
        if (!$userId) {
            return json_encode(['error' => ['message' => 'Could not find a valid Microsoft user ID (GUID) for the organizer email: ' . $organizer]]);
        }

        $startTimestamp = is_numeric($start_time) ? (int) $start_time : strtotime($start_time);
        $endTimestamp   = $end_time
            ? (is_numeric($end_time) ? (int) $end_time : strtotime($end_time))
            : ($startTimestamp + 3600);

        $meetingData = [
            'subject'       => $topic,
            'startDateTime' => gmdate('Y-m-d\TH:i:s\Z', $startTimestamp),
            'endDateTime'   => gmdate('Y-m-d\TH:i:s\Z', $endTimestamp),
        ];

        // استخدام الـ GUID المستخرج مباشرة في الرابط
        $graphEndpoint = 'https://graph.microsoft.com/v1.0/users/' . $userId . '/onlineMeetings';

        return self::request('POST', $graphEndpoint, $token, $meetingData);
    }

    /**
     * Update the subject / schedule of an existing Teams online meeting.
     */
    public static function updateMeeting($topic, $start_time, $meetingId, $end_time = null)
    {
        $organizer = get_settings('teams_organizer_email');
        $token     = self::createToken();

        if (!$token || !$organizer) {
            return json_encode(['error' => ['message' => 'Failed to authenticate with Microsoft Teams.']]);
        }

        $userId = self::getUserIdByEmail($organizer, $token);
        if (!$userId) {
            return json_encode(['error' => ['message' => 'Could not find a valid Microsoft user ID (GUID) for the organizer email.']]);
        }

        $startTimestamp = is_numeric($start_time) ? (int) $start_time : strtotime($start_time);

        $meetingData = [
            'subject'       => $topic,
            'startDateTime' => gmdate('Y-m-d\TH:i:s\Z', $startTimestamp),
        ];

        if ($end_time) {
            $endTimestamp                     = is_numeric($end_time) ? (int) $end_time : strtotime($end_time);
            $meetingData['endDateTime']  = gmdate('Y-m-d\TH:i:s\Z', $endTimestamp);
        }

        $graphEndpoint = 'https://graph.microsoft.com/v1.0/users/' . $userId . '/onlineMeetings/' . rawurlencode($meetingId);

        return self::request('PATCH', $graphEndpoint, $token, $meetingData);
    }

    /**
     * Delete/cancel a Teams online meeting.
     */
    public static function deleteMeeting($meetingId)
    {
        $organizer = get_settings('teams_organizer_email');
        $token     = self::createToken();

        if (!$token || !$organizer || !$meetingId) {
            return null;
        }

        $userId = self::getUserIdByEmail($organizer, $token);
        if (!$userId) {
            return null;
        }

        $graphEndpoint = 'https://graph.microsoft.com/v1.0/users/' . $userId . '/onlineMeetings/' . rawurlencode($meetingId);

        return self::request('DELETE', $graphEndpoint, $token);
    }

    protected static function request($method, $url, $token, $body = null)
    {
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        $curlopt_customrequest = $method;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $curlopt_customrequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function config()
    {
        return [
            'tenantId'       => get_settings('teams_tenant_id'),
            'clientId'       => get_settings('teams_client_id'),
            'clientSecret'   => get_settings('teams_client_secret'),
            'organizerEmail' => get_settings('teams_organizer_email'),
        ];
    }
}
