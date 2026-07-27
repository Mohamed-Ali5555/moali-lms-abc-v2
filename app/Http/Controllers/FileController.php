<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Support\Facades\File;

class FileController extends Controller
{
    public function get_file(Request $request)
    {
        // Block download managers and direct access
        $userAgent = $request->header('User-Agent', '');
        $referer = $request->header('Referer', '');
        
        // Block common download manager user agents
        $downloadManagers = [
            'idm', 'internet download manager', 'download', 'downloader', 
            'jdownloader', 'free download manager', 'fdm', 'orbit downloader', 
            'flashget', 'wget', 'curl', 'aria2', 'axel', 'uget', 'persepolis'
        ];
        
        $userAgentLower = strtolower($userAgent);
        foreach ($downloadManagers as $manager) {
            if (strpos($userAgentLower, $manager) !== false) {
                abort(403, 'Download managers are not allowed');
            }
        }
        
        // Check if request is from browser (not direct download)
        if (empty($referer) && !$request->header('Sec-Fetch-Mode')) {
            // Allow only if it's a video streaming request (Range header present)
            if (!$request->header('Range') && !$request->has('stream')) {
                abort(403, 'Direct access not allowed');
            }
        }
        
        // Check if request is from same domain
        $appUrl = parse_url(url('/'), PHP_URL_HOST);
        $refererHost = parse_url($referer, PHP_URL_HOST);
        if (!empty($referer) && $refererHost !== $appUrl && !str_contains($refererHost, $appUrl)) {
            // Allow video streaming but block direct downloads
            if (!$request->header('Range')) {
                abort(403, 'Cross-origin access not allowed');
            }
        }

        $user_id = auth()->user()->id;

        if (isset($request->course_id) && isset($request->lesson_id) && $user_id > 0) {
            $course_id = $request->course_id;
            $lesson_id = $request->lesson_id;
            $lesson = Lesson::find($lesson_id);
            $get_lesson_type = $lesson->lesson_type;

            if(enroll_status($course_id, $user_id) === 'valid' || auth()->user()->role == 'admin' || is_course_instructor($course_id, $user_id)){

                if ($get_lesson_type == 'image' || $get_lesson_type == 'document_type') {
                    $fileUrl = 'uploads/lesson_file/attachment/' . $lesson->attachment;
                }

                if ($get_lesson_type == 'system-video') {
                    $fileUrl = $lesson->lesson_src;
                }

                if (str_contains('https:', url('')) !== false && str_contains('http:', $fileUrl) !== false) {
                    $fileUrl = str_replace('http:', 'https:', $fileUrl);
                }elseif(str_contains('http:', url('')) !== false && str_contains('https:', $fileUrl) !== false){
                    $fileUrl = str_replace('https:', 'http:', $fileUrl);
                }

                $fileUrl = str_replace(url(''), '', $fileUrl);
                $basename = basename($fileUrl);

                if (str_contains('http', $fileUrl) !== false) {
                    $header_data = get_headers($fileUrl, 1);
                    if (array_key_exists('Content-Type', $header_data)) {
                        $content_type = $header_data['Content-Type'];
                    }
                    if (array_key_exists('Content-type', $header_data)) {
                        $content_type = $header_data['Content-type'];
                    }
                    if (array_key_exists('content-type', $header_data)) {
                        $content_type = $header_data['content-type'];
                    }


                    //$this->get_remote_file_size($fileUrl);
                    if (array_key_exists('Content-Length', $header_data)) {
                        $file_size = $header_data['Content-Length'];
                    }
                    if (array_key_exists('Content-length', $header_data)) {
                        $file_size = $header_data['Content-length'];
                    }
                    if (array_key_exists('content-length', $header_data)) {
                        $file_size = $header_data['content-length'];
                    }
                } else {
                    $content_type = mime_content_type(public_path($fileUrl));
                    $file_size = filesize(public_path($fileUrl));
                }


                if ($get_lesson_type == 'image' || $get_lesson_type == 'document_type') {
                    //for not streaming file as like: img, pdf, txt and more.
                    header('Content-Type: ' . $content_type);
                    header('Content-Length: ' . $file_size);
                    // header('Content-Disposition: inline; filename=' . basename($fileUrl));
                    readfile(public_path($fileUrl));
                    exit;
                } elseif($get_lesson_type == 'system-video') {
                    
                    if($file_size < 3000000){
                        $chunkSize = $file_size;
                    }else{
                        $chunkSize = 3000000;
                    }

                    $start = 0;
                    $end = $file_size - 1;

                    $range = isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : 'bytes=0-' . $chunkSize;

                    header('Accept-Ranges: bytes');
                    header('Content-Type: ' . $content_type);
                    header('Content-Disposition: inline; filename="' . $basename . '"');
                    
                    // Prevent video download
                    header('X-Content-Type-Options: nosniff');
                    header('X-Frame-Options: SAMEORIGIN');
                    
                    // Block download by removing Content-Disposition attachment
                    // Only allow inline playback
                    if (strpos($userAgentLower, 'download') !== false || 
                        strpos($userAgentLower, 'wget') !== false || 
                        strpos($userAgentLower, 'curl') !== false) {
                        abort(403, 'Download not allowed');
                    }

                    if ($range) {
                        header('HTTP/1.1 206 Partial Content');
                        $range = explode('=', $range);
                        $start = intval($range[1]);
                        $end = min($start + $chunkSize - 1, $file_size - 1);
                        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $file_size);
                    } else {
                        header('Content-Length: ' . $file_size);
                    }

                    // Set cache-control headers
                    header('Cache-Control: public, max-age=0');
                    header('Pragma: public');

                    // Remove unnecessary headers
                    header_remove('Expires');

                    $handle = fopen(public_path($fileUrl), 'rb');
                    fseek($handle, $start);


                    while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
                        if ($pos + $chunkSize > $end) {
                            $chunkSize = $end - $pos + 1;
                        }
                        echo fread($handle, $chunkSize);
                        ob_flush();
                        flush();
                    }
                    

                    fclose($handle);
                    exit;
                }
            }
        }
    }

    public function get_video_file(Request $request)
    {
        // Block download managers
        $userAgent = $request->header('User-Agent', '');
        $userAgentLower = strtolower($userAgent);
        
        $downloadManagers = [
            'idm', 'internet download manager', 'download', 'downloader', 
            'jdownloader', 'free download manager', 'fdm', 'orbit downloader', 
            'flashget', 'wget', 'curl', 'aria2', 'axel', 'uget', 'persepolis'
        ];
        
        foreach ($downloadManagers as $manager) {
            if (strpos($userAgentLower, $manager) !== false) {
                abort(403, 'Download managers are not allowed');
            }
        }
        
        // Check referer
        $referer = $request->header('Referer', '');
        if (empty($referer) && !$request->header('Range')) {
            abort(403, 'Direct access not allowed');
        }
        
        $user_id = auth()->user()->id;

        if (isset($request->course_id) && isset($request->lesson_id) && $user_id > 0) {
            $course_id = $request->course_id;
            $lesson_id = $request->lesson_id;
            $lesson = Lesson::find($lesson_id);
            $get_lesson_type = $lesson->lesson_type;

            if(enroll_status($course_id, $user_id) === 'valid' || auth()->user()->role == 'admin' || is_course_instructor($course_id, $user_id)){
                if ($get_lesson_type == 'system-video') {
                    $fileUrl = $lesson->lesson_src;
                    $filePath = public_path($fileUrl);
                }

                if (File::exists($filePath)) {
                    // Open the file as a stream and set headers
                    $stream = fopen($filePath, 'rb');
                    $mimeType = mime_content_type($filePath);
            
                    // Return the file as a stream (no direct download, just playback)
                    return response()->stream(function () use ($stream) {
                        fpassthru($stream);
                    }, 200, [
                        'Content-Type' => $mimeType,
                        'Content-Length' => filesize($filePath),
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                        'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
                        'X-Content-Type-Options' => 'nosniff',
                        'X-Frame-Options' => 'SAMEORIGIN',
                        'X-Download-Options' => 'noopen',
                    ]);
                } else {
                    abort(404, 'Video not found');
                }
            }
        }
    }

    public function pdf_canvas($course_id = "", $lesson_id = "")
    {
        $user_id = auth()->user()->id;


        if(enroll_status($course_id, $user_id) === 'valid' || auth()->user()->role == 'admin' || is_course_instructor($course_id, $user_id)){
            $page_data['course_id'] = $course_id;
            $page_data['lesson_id'] = $lesson_id;
            return view('course_player.pdf_canvas', $page_data);
        } else {
            echo get_phrase('Access denied');
        }
    }
}
