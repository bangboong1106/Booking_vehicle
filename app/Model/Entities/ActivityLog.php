<?php

namespace App\Model\Entities;

use App\Model\Base\Base;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Activity
{
    protected $table = "activity_log";
    protected $_alias = "activity_log";

    protected $fillable = [];

    public static function getTableName()
    {
        return 'activity_log';
    }

    public function getAlias()
    {
        return 'activity_log';
    }

    public function getAttributeName($key)
    {
        return transa($this->getAlias(), $key);
    }

    public function user()
    {
        return $this->hasOne(AdminUserInfo::class, 'causer_id')
            ->where('activity_log.causer_type', '=', 'App\Model\Entities\AdminUserInfo');
    }

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
}