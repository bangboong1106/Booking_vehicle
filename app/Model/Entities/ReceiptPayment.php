<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:23
 */

namespace App\Model\Entities;

use App\Model\Base\NestedSetBase;

class ReceiptPayment extends NestedSetBase
{
    protected $_alias = 'receipt_payment';

    protected $table = 'm_receipt_payment';

    // 'parent_id' column name
    protected $parentColumn = 'parent_id';

    // 'lft' column name
    protected $leftColumn = 'lidx';

    // 'rgt' column name
    protected $rightColumn = 'ridx';

    protected $scoped = array('type');

    // guard attributes from mass-assignment
    protected $guarded = array('id', 'parent_id', 'lidx', 'ridx', 'depth', 'amount', 'is_display_driver');

    protected $fillable = array('id', 'type', 'name', 'parent_id', 'amount', 'is_display_driver');
    protected $_detailNameField = 'name';


    public static function getScopedNestedList($column, $key = null, $seperator = ' ', $type, $isGetID = false)
    {
        $instance = new static;

        $pkey = $key ?: $instance->getKeyName();
        $depthColumn = $instance->getDepthColumnName();
        $nodes = $instance->where('type', $type)
            //            ->where('del_flag', '0')
            ->orderBy($instance->getLeftColumnName(), 'asc')
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
