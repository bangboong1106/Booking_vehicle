<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:24
 */

namespace App\Model\Entities;


use App\Model\Base\ModelSoftDelete;
use Illuminate\Notifications\Notifiable;

class District extends ModelSoftDelete
{

    protected $table = "m_district";
    use Notifiable;

    protected $_alias = 'district';
    protected $fillable = ['title', 'type', 'province_id', 'district_id', 'location'];
    protected $_detailNameField = 'title';

    public function getType()
    {
        return config('system.district.'.$this->type);
    }

    public function province()
    {
        return $this->hasOne(Province::class, 'province_id', 'province_id');
    }

    public function wards()
    {
        return $this->hasMany(Ward::class, 'district_id', 'district_id');
    }
}