<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use OwenIt\Auditing\Contracts\Auditable;

class CustomerDefaultData extends ModelSoftDelete implements Auditable
{
    protected $table = "customer_default_data";

    protected $fillable = [
        'code',
        'customer_id',
        'location_destination_id',
        'location_arrival_id',
        'location_destination_ids',
        'location_arrival_ids',
        'system_code_config_id',
        'client_id'

    ];
    protected $_detailNameField = 'customer_default_data';

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'code',
        'customer_id',
        'location_destination_id',
        'location_arrival_id',
        'location_destination_ids',
        'location_arrival_ids',
        'system_code_config_id',
        'client_id'
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function systemCodeConfig()
    {
        return $this->hasOne(SystemCodeConfig::class, 'id', 'system_code_config_id');
    }

    public function locationDestinationAttributes()
    {
        if (!$this->relationLoaded('locationDestinations')) {
            $locationDestinations = Location::whereIn('id', explode(',', $this->location_destination_ids))->get();

            $this->setRelation('locationDestinations', $locationDestinations);
        }

        return $this->getRelation('locationDestinations');
    }

    public function locationDestinations()
    {
        return Location::whereIn('id', explode(',', $this->location_destination_ids));
    }

    public function locationArrivalAttributes()
    {
        if (!$this->relationLoaded('locationArrivals')) {
            $locationArrivals = Location::whereIn('id', explode(',', $this->location_arrival_ids))->get();

            $this->setRelation('locationArrivals', $locationArrivals);
        }

        return $this->getRelation('locationArrivals');
    }

    public function locationArrivals()
    {
        return Location::whereIn('id', explode(',', $this->location_arrival_ids));
    }

    public function getLocationDestinationsIdsAttribute($commaSeparatedIds)
    {
        return explode(',', $commaSeparatedIds);
    }

    public function setLocationDestinationsIds($ids)
    {
        $this->attributes['location_destination_ids'] = is_string($ids) ? $ids : implode(',', $ids);
    }

    public function setLocationArrivalsIds($ids)
    {
        $this->attributes['location_arrival_ids'] = is_string($ids) ? $ids : implode(',', $ids);
    }

    public function client()
    {
        return $this->hasOne(Customer::class, 'id', 'client_id');
    }
}
