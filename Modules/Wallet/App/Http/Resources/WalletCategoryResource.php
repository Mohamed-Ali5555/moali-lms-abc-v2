<?php

namespace App\Modules\Wallet\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletCategoryResource extends JsonResource
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
            'category_id' => $this->category->title,
            'status' => $this->status,
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
