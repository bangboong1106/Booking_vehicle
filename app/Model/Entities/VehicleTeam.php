<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Illuminate\Support\Facades\DB;

/**
 * @property mixed driver_ids
 * @property mixed id
 */
class VehicleTeam extends ModelSoftDelete
{
    protected $table = "vehicle_team";

    protected $_alias = 'vehicle_team';

    protected $fillable = ['code', 'name', 'capital_driver_id', 'partner_id'];

    public $driver_ids;
    protected $_detailNameField = 'code';

    public function capital_driver()
    {
        return $this->hasOne(Driver::class, 'id', 'capital_driver_id');
    }

    public function drivers()
    {
        return $this->belongsToMany('App\Model\Entities\Driver', 'driver_vehicle_team', 'vehicle_team_id', 'driver_id');
    }

    public function partner()
    {
        return $this->hasOne(Partner::class, 'id', 'partner_id');
    }
}