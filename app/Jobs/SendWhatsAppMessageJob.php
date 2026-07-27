<?php

namespace App\Jobs;

use App\Models\WhatsappLog;
use App\Services\WaPilot\WaPilotClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $phone,
        public string $message,
        public ?string $eventKey = null,
        public ?int $userId = null,
        public string $recipientType = 'student'
    ) {
    }

    public function handle(WaPilotClient $client): void
    {
        $log = WhatsappLog::create([
            'event_key' => $this->eventKey,
            'user_id' => $this->userId,
            'recipient_type' => $this->recipientType,
            'phone' => $this->phone,
            'message' => $this->message,
            'status' => 'pending',
        ]);

        if (!$client->isEnabled()) {
            $log->update([
                'status' => 'failed',
                'response' => 'WaPilot is disabled.',
            ]);
            return;
        }

        $result = $client->send($this->phone, $this->message);

        $log->update([
            'status' => ($result['success'] ?? false) ? 'success' : 'failed',
            'response' => is_string($result['response'] ?? null)
                ? substr($result['response'], 0, 2000)
                : json_encode($result['response'] ?? null),
        ]);
    }
}
