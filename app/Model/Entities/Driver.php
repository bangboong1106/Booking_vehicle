<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Exception;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property mixed sex
 */
class Driver extends ModelSoftDelete implements Auditable
{
    protected $table = "drivers";
    protected $_alias = "driver";
    protected $fillable = ['code', 'user_id', 'active', 'mobile_no', 'identity_no', 'full_name', 'avatar_id', 'address', 'current_address', 'birth_date',
        'sex', 'full_name_accent', 'standard_mobile_no', 'note', 'working_status', 'hometown', 'vehicle_team_id', 'experience_drive',
        'ready_status',
        'experience_work', 'work_date', 'vehicle_old', 'evaluate', 'rank', 'work_description', 'id_no', 'driver_license','partner_id'];

    protected static $_destroyRelations = [
        'adminUser'
    ];

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'code', 'active', 'mobile_no', 'identity_no', 'full_name', 'address', 'current_address', 'birth_date',
        'sex', 'note', 'hometown', 'vehicle_team_id', 'experience_drive',
        'ready_status',
        'experience_work', 'work_date', 'vehicle_old', 'evaluate', 'rank', 'work_description', 'id_no', 'driver_license','partner_id'
    ];
    protected $_detailNameField = 'code';

    public function getSexText()
    {
        if ($this->sex != null)
            return trans('common.' . $this->sex);
    }

    public function adminUser()
    {
        return $this->hasOne(AdminUserInfo::class, 'id', 'user_id');
    }

    public function partner()
    {
        return $this->hasOne(Partner::class, 'id', 'partner_id');
    }

    public function vehicleTeams()
    {
        return $this->belongsToMany(VehicleTeam::class, 'driver_vehicle_team', 'driver_id', 'vehicle_team_id');
    }

    public function avatarFile()
    {
        return $this->hasOne(File::class, 'file_id', 'avatar_id');
    }

    public function transformAudit(array $data): array
    {
        try {
            if (Arr::has($data, 'new_values.sex')) {
                $data['old_values']['sex'] = empty($data['old_values']['sex']) ? '' : config('system.sex.' . $data['old_values']['sex']);
                $data['new_values']['sex'] = empty($data['new_values']['sex']) ? '' : config('system.sex.' . $data['new_values']['sex']);
            }

            if (Arr::has($data, 'new_values.vehicle_team_id')) {
                $data['old_values']['vehicle_team_id'] = empty($data['old_values']['vehicle_team_id']) ? '' : VehicleTeam::find($this->getOriginal('vehicle_team_id'))->name;
                $data['new_values']['vehicle_team_id'] = empty($data['new_values']['vehicle_team_id']) ? '' : VehicleTeam::find($this->getAttribute('vehicle_team_id'))->name;
            }
        } catch (Exception $e) {
            logError($e->getMessage());
        }

        return $data;
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'driver_vehicle', 'driver_id', 'vehicle_id');
    }
}
