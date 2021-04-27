<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Exception;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class Partner extends ModelSoftDelete implements Auditable
{
    protected $table = "partner";

    protected $fillable = [
        'code',
        'active',
        'mobile_no',
        'email',
        'full_name',
        'address',
        'current_address',
        'note',
        'delegate',
        'tax_code',
        'ward_id', 'district_id', 'longitude', 'latitude', 'province_id'
    ];
    protected $_detailNameField = 'code';

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'code', 'active', 'mobile_no', 'full_name', 'address',
        'current_address', 'note','delegate','email',
        'tax_code', 'ward_id', 'district_id', 'province_id'
    ];

    public function district()
    {
        return $this->hasOne(District::class, 'district_id', 'district_id');
    }

    public function ward()
    {
        return $this->hasOne(Ward::class, 'ward_id', 'ward_id');
    }

    public function province()
    {
        return $this->hasOne(Province::class, 'province_id', 'province_id');
    }

    public function haveStaff()
    {
        return $this->hasMany(AdminUserInfo::class, 'partner_id', 'id');
    }

    public function getCurrentLocation()
    {
        $provinceTitle = $this->tryGet('province')->title;
        $districtTitle = $this->tryGet('district')->title;
        $wardTitle = $this->tryGet('ward')->title;

        if (empty($this->id)) {
            return '';
        }

        return $this->address . ', ' . $wardTitle . ', ' . $districtTitle . ', ' . $provinceTitle;
    }

    public function transformAudit(array $data): array
    {
        try {
            if (Arr::has($data, 'new_values.active')) {
                $data['old_values']['active'] = empty($data['old_values']['active']) ? '' : config('system.active.' . $data['old_values']['active']);
                $data['new_values']['active'] = empty($data['new_values']['active']) ? '' : config('system.active.' . $data['new_values']['active']);
            }

            if (Arr::has($data, 'new_values.ward_id')) {
                $data['old_values']['ward_id'] = empty($data['old_values']['ward_id']) ? '' : Ward::query()
                    ->where('ward_id', '=', $this->getOriginal('ward_id'))->first()->getAttribute('title');
                $data['new_values']['ward_id'] = empty($data['new_values']['ward_id']) ? '' : Ward::query()
                    ->where('ward_id', '=', $this->getAttribute('ward_id'))->first()->getAttribute('title');
            }

            if (Arr::has($data, 'new_values.district_id')) {
                $data['old_values']['district_id'] = empty($data['old_values']['district_id']) ? '' : District::query()
                    ->where('district_id', '=', $this->getOriginal('district_id'))->first()->getAttribute('title');
                $data['new_values']['district_id'] = empty($data['new_values']['district_id']) ? '' : District::query()
                    ->where('district_id', '=', $this->getAttribute('district_id'))->first()->getAttribute('title');
            }

            if (Arr::has($data, 'new_values.province_id')) {
                $data['old_values']['province_id'] = empty($data['old_values']['province_id']) ? '' : Province::query()
                    ->where('province_id', '=', $this->getOriginal('province_id'))->first()->getAttribute('title');
                $data['new_values']['province_id'] = empty($data['new_values']['province_id']) ? '' : Province::query()
                    ->where('province_id', '=', $this->getAttribute('province_id'))->first()->getAttribute('title');
            }
        } catch (Exception $e) {
            logError($e->getMessage());
        }

        return $data;
    }
}

