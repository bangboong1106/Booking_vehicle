<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:23
 */

namespace App\Model\Entities;


use App\Model\Base\ModelSoftDelete;
use Illuminate\Notifications\Notifiable;

class Province extends ModelSoftDelete
{
    protected $table = "m_province";
    use Notifiable;

    protected $_alias = 'province';
    protected $fillable = ['title', 'type', 'province_id'];

    public function getType()
    {
        return config('system.province.'.$this->type);
    }

    public function districts()
    {
        return $this->hasMany(District::class, 'province_id', 'province_id');
    }
}