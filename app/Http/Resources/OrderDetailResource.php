<?php

namespace App\Http\Resources;

use App\Models\Goods;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'product_code' => $this->custom_product_code,
            'barcode'      => Goods::where('code', $this->custom_product_code)->first()['barcode'],
            'product_name' => $this->product_name,
            'quantity'     => $this->quantity,
            'picked'       => 0,
        ];
    }
}
