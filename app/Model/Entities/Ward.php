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

class Ward extends ModelSoftDelete
{
    protected $table = "m_ward";
    use Notifiable;

    protected $_alias = 'ward';
    protected $fillable = ['title', 'type', 'ward_id', 'district_id', 'location'];

    public function getType()
    {
        return config('system.ward.'.$this->type);
    }

    public function district()
    {
        return $this->hasOne(District::class, 'district_id', 'district_id');
    }
}