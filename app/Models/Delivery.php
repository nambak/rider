<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['state'];

    public function getStateAttribute()
    {
        $state = '배송전';

        if (isset($this->completed_at)) {
            $state = '배송완료';
        } else if (isset($this->started_at)) {
            $state = '배송중';
        } else if (isset($this->order_picked_at)) {
            $state = '배송준비';
        }

        return $state;
    }
}
