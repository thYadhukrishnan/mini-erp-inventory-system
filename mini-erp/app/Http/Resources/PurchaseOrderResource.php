<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'po_number'    => $this->po_number,
            'status'       => $this->status->value,
            'status_label' => $this->status->label(),
            'total_amount' => (float) $this->total_amount,
            'supplier'     => [
                'id'    => $this->supplier?->id,
                'name'  => $this->supplier?->name,
                'email' => $this->supplier?->email,
            ],
            'items'        => PurchaseOrderItemResource::collection($this->whenLoaded('items')),
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
        ];
    }
}
