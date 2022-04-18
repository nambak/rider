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
        $barcodes = $this->goods->barcodes->pluck('barcode')->toArray();
        array_push($barcodes, $this->goods->barcode);

        return [
            'product_code' => $this->goods->code,
            'barcode'      => implode(',', $barcodes),
            'product_name' => $this->product_name,
            'quantity'     => $this->quantity,
            'picked'       => 0,
        ];
    }
}
