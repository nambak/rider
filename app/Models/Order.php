<?php

namespace App\Models;

use App\NcloudAPI;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $appends = ['state', 'region'];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
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

    public function getRegionAttribute()
    {
        $geoCode = NcloudAPI::getGeoCodeData($this->delivery->address1);
        $geoCodeText = $geoCode[0]->y . ' ' . $geoCode[0]->x;

        $result = GeoFence::whereRaw("ST_CONTAINS(zone_polygon, ST_GEOMFROMTEXT('Point($geoCodeText)'))")
            ->select(['id', 'type', 'branch_office_id'])
            ->first();

        return $result->type;
    }
}
