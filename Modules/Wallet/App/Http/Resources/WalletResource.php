<?php

namespace App\Modules\Wallet\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'balance' => $this->balance,
            'type' => $this->type,

            'payment_id' => $this->payment_id,
            'uuid' => $this->uuid,

            'wallet_category_id' => $this->wallet_category_id,
            'status' => $this->status,

            'student_id' => $this->student->name,
            'added_by' => $this->student->name,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // if (auth()->check()) {
        //     $data['added_by'] = auth()->user()->id;
        // }

        return $data;
    }
}
