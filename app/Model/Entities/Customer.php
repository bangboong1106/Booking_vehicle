<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Exception;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class Customer extends ModelSoftDelete implements Auditable
{
    protected $table = "customer";

    protected $fillable = [
        'user_id',
        'avatar_id',
        'customer_code',
        'active',
        'mobile_no',
        'identity_no',
        'full_name',
        'address',
        'current_address',
        'birth_date',
        'sex',
        //        'email',
        'full_name_accent',
        'standard_mobile_no',
        'note',
        'delegate',
        'tax_code',
        'type',
        'ward_id', 'district_id', 'longitude', 'latitude', 'province_id',
        'parent_id',
        'customer_type'
    ];
    protected $_detailNameField = 'customer_code';

    protected static $_destroyRelations = [
        'adminUser'
    ];

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'customer_code', 'active', 'mobile_no', 'identity_no', 'full_name', 'address',
        'current_address', 'birth_date', 'sex', 'email', 'note', 'delegate',
        'tax_code', 'type', 'ward_id', 'district_id', 'province_id', 'parent_id'
    ];

    public function adminUser()
    {
        return $this->hasOne(AdminUserInfo::class, 'id', 'user_id');
    }

    public function getSexText()
    {
        if ($this->sex != null)
            return trans('common.' . $this->sex);
    }

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

    public function parent()
    {
        return $this->hasOne(Customer::class, 'id', 'parent_id');
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

    public function getCustomerType()
    {
        return config('system.customer_type.' . $this->type);
    }

    public function transformAudit(array $data): array
    {
        try {
            if (Arr::has($data, 'new_values.active')) {
                $data['old_values']['active'] = empty($data['old_values']['active']) ? '' : config('system.active.' . $data['old_values']['active']);
                $data['new_values']['active'] = empty($data['new_values']['active']) ? '' : config('system.active.' . $data['new_values']['active']);
            }

            if (Arr::has($data, 'new_values.type')) {
                $data['old_values']['type'] = empty($data['old_values']['type']) ? '' : config('system.customer_type.' . $data['old_values']['type']);
                $data['new_values']['type'] = empty($data['new_values']['type']) ? '' : config('system.customer_type.' . $data['new_values']['type']);
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
