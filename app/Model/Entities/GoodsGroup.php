<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:23
 */

namespace App\Model\Entities;

use App\Model\Base\NestedSetBase;

class GoodsGroup extends NestedSetBase
{
    protected $_alias = 'goods_group';
    protected $table = 'goods_group';

    // 'parent_id' column name
    protected $parentColumn = 'parent_id';

    // 'lft' column name
    protected $leftColumn = 'lidx';

    // 'rgt' column name
    protected $rightColumn = 'ridx';


    // guard attributes from mass-assignment
    protected $guarded = array('id', 'parent_id', 'lidx', 'ridx', 'depth');

    protected $fillable = array('id', 'code', 'name', 'parent_id');
    protected $_detailNameField = 'code';

    public static function getScopedNestedList($column, $key = null, $seperator = ' ', $isGetID = false)
    {
        $instance = new static;

        $pkey = $key ?: $instance->getKeyName();
        $depthColumn = $instance->getDepthColumnName();
        $nodes = $instance->orderBy($instance->getLeftColumnName(), 'asc')
            ->get()
            ->toArray();
        $array = [];
        foreach ($nodes as $key => $value) {
            $name = str_repeat($seperator, $value[$depthColumn]) . $value[$column];
            $array[$value[$pkey]] = $isGetID ? $value[$pkey] . '_' . $name : $name;
        }
        return $array;
    }
}