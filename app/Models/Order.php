<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $appends = ['state'];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }

    public function getStateAttribute()
    {
        $state = '배송전';

        if (isset($this->delivery->completed_at)) {
            $state = '배송완료';
        } else if (isset($this->delivery->started_at)) {
            $state = '배송중';
        } else if (isset($this->delivery->order_picked_at)) {
            $state = '배송준비';
        }

        return $state;
    }

    public function generateTitle()
    {
        $title = $this->details[0]->product_name;

        return ($this->details->count() > 1) ? $title . ' 외' . ($this->details->count() - 1) . '건' : $title;
    }
}
