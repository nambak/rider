<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'user_id'     => $this->user->id,
            'employee_no' => $this->employee_no,
            'name'        => $this->user->name,
            'branch_name' => $this->branch->name,
            'branch_id'   => $this->branch_id,
            'phone'       => $this->user->phone,
            'email'       => $this->user->email,
        ];
    }
}
