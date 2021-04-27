<?php

namespace App\Model\Base;

use App\Common\AppConstant;
use App\Model\Entities\AdminUserInfo;
use App\Model\Entities\SystemCode;
use App\Model\Entities\SystemCodeConfig;
use Carbon\Carbon;
use Illuminate\Support\Arr;

/**
 * Class Base
 * @package App\Model\Base
 */
class ModelSoftDelete extends Base
{
    use CustomSoftDeletes;
    protected $dates = [];
    protected $_extendData;

    /**
     * @return mixed
     */
    public function getExtendData()
    {
        return $this->_extendData;
    }

    /**
     * @param mixed $extendData
     */
    public function setExtendData($extendData): void
    {
        $this->_extendData = $extendData;
    }

    protected $auditExclude = [
        'upd_date',
        'upd_id'
    ];

    public function getDateTime($field = '', $to = 'd-m-Y', $from = '')
    {
        if (empty($field)) {
            return '';
        }

        if (empty($this->$field)) {
            return '';
        }

        return empty($from) ? Carbon::parse($this->$field)->format($to) :
            Carbon::createFromFormat($this->$field, $from)->format($to);
    }

    protected static function boot()
    {
        parent::boot();
        static::created(function ($model) {
            static::activeSystemCode($model);
        });
        static::updated(function ($model) {
            static::activeSystemCode($model);
        });
    }

    public static function activeSystemCode($model)
    {
        $generateSystemCodeConfig = AppConstant::getListGenerateSystemCodeConfig();
        foreach ($generateSystemCodeConfig as $config) {
            if ($config['table'] == $model->table) {
                $modelArray = $model->toArray();
                static::updateSystemCode($config['type'], $modelArray[$config['attribute']]);
                break;
            }
        }
    }

    public static function updateSystemCode($type, $code)
    {
        $system_code_config = SystemCodeConfig::select()
            ->where('type', '=', $type)
            ->where('code_tmp', '=', $code)
            ->where('del_flag', '=', 0)
            ->first();
        if ($system_code_config != null) {
            $system_code_config->end_suffix = $system_code_config->suffix_tmp;
            $system_code_config->save();
        }
    }

    public function checkFloatValue($data, $fieldName = '', $currency = false)
    {
        if (empty($fieldName) || !Arr::has($data, 'new_values.' . $fieldName)) {
            return $data;
        }

        $newValue = floatval($data['new_values'][$fieldName]);
        $oldValue = isset($data['old_values'][$fieldName]) ? floatval($data['old_values'][$fieldName]) + 0 : 0;
        $epsilon = 0.00001;

        if (abs($newValue - $oldValue) < $epsilon) {
            unset($data['new_values'][$fieldName]);
            unset($data['old_values'][$fieldName]);
        } else if ($currency) {
            $data['new_values'][$fieldName] = numberFormat($data['new_values'][$fieldName]) . ' VND';
            $data['old_values'][$fieldName] = numberFormat($data['old_values'][$fieldName]) . ' VND';
        } else {
            $data['new_values'][$fieldName] = $newValue;
            $data['old_values'][$fieldName] = $oldValue;
        }

        return $data;
    }

    public function checkTimeValue($data, $fieldName = '')
    {
        if (empty($fieldName) || !Arr::has($data, 'new_values.' . $fieldName)) {
            return $data;
        }

        $newValue = $data['new_values'][$fieldName];
        $oldValue = isset($data['old_values'][$fieldName]) ? $data['old_values'][$fieldName] : null;
        if (isset($oldValue) && date('H:i', strtotime($newValue)) == date('H:i', strtotime($oldValue))) {
            unset($data['new_values'][$fieldName]);
            unset($data['old_values'][$fieldName]);
        }

        return $data;
    }

    public function insUser()
    {
        return $this->hasOne(AdminUserInfo::class, 'id', 'ins_id');
    }

    public function updUser()
    {
        return $this->hasOne(AdminUserInfo::class, 'id', 'upd_id');
    }
}