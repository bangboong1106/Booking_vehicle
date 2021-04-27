<?php

namespace App\Repositories\Traits;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use stdClass;
use Exception;

trait RoutePriceTrait
{
    // Lấy danh sách đơn hàng của chuyến group theo khách hàng
    //CreatedBy nlhoang 01/07/2020
    public function groupOrderByCustomerByRouteId($id)
    {
        try {
            $query =
                DB::table('routes as r')
                ->join('orders as o', 'o.route_id', '=', 'r.id')
                ->leftJoin('locations as l1', 'l1.id', '=', 'o.location_destination_id')
                ->leftJoin('locations as l2', 'l2.id', '=', 'o.location_arrival_id')
                ->leftJoin('customer as c', 'c.id', '=', 'o.customer_id')
                ->where([
                    ['r.id', '=', $id],
                    ['r.del_flag', '=', 0],
                ])
                ->get([
                    'o.customer_id',
                    'c.full_name',
                    'o.id as order_id',
                    'o.order_code',
                    'o.amount',
                    'o.weight',
                    'o.volume',
                    'l1.title as location_destination',
                    'l2.title as location_arrival'

                ]);

            $results = [];

            foreach ($query as $item) {

                $res = array_filter($results, function ($element) use ($item) {
                    return $element['customer_id'] == $item->{'customer_id'};
                });
                if (count($res) !== 0) {
                    foreach ($results as &$result) {
                        if ($result['customer_id'] == $item->{'customer_id'}) {
                            $result['orders'][] = [
                                'order_id' => $item->{'order_id'},
                                'order_code' => $item->{'order_code'},
                                'location_destination' => $item->{'location_destination'},
                                'location_arrival' => $item->{'location_arrival'},
                                'amount' => $item->{'amount'},
                                'weight' => $item->{'weight'},
                                'volume' => $item->{'volume'}
                            ];
                        }
                    }
                } else {
                    $temp = [
                        'customer_id' => $item->{'customer_id'},
                        'full_name' => $item->{'full_name'},
                        'orders' => [
                            [
                                'order_id' => $item->{'order_id'},
                                'order_code' => $item->{'order_code'},
                                'location_destination' => $item->{'location_destination'},
                                'location_arrival' => $item->{'location_arrival'},
                                'amount' => $item->{'amount'},
                                'weight' => $item->{'weight'},
                                'volume' => $item->{'volume'}
                            ]
                        ]

                    ];
                    $results[] = $temp;
                }
            }
        } catch (Exception $e) {
            logError($e);
        }
        return $results;
    }

    // Tính doanh thu theo báo giá
    //CreatedBy nlhoang 01/07/2020
    // ModifiedBy nlhoang 23/07/2020: bổ sung tính giá theo loại hàng hoá
    public function calculatePrice($params)
    {
        $results = [];
        $priceQuote = DB::table('price_quote')->find($params['pricePolicyId']);
        if ($priceQuote) {
            $type = $priceQuote->{'type'};
            switch ($type) {
                case 1:
                    $orders = $this->_getOrdersForCalcPrice($params);
                    $results = $this->_calcPriceByVehicleGroup($priceQuote, $orders);
                    break;
                case 2:
                    $orders = $this->_getOrdersForCalcPrice($params);
                    $results = $this->_calcPriceByWeightOfVehicle($priceQuote, $orders);
                    break;
                case 3:
                    $orders = $this->_getOrdersForCalcPrice($params);
                    $results = $this->_calcPriceByVolumeOfVehicle($priceQuote, $orders);
                    break;
                case 4:
                    $orders = $this->_getOrdersForCalcPrice($params);
                    $results = $this->_calcPriceByGoodsOfVehicle($priceQuote, $orders);
                    break;
            }
        }
        return $results;
    }

    // Lấy danh sách đơn hàng để tiến hành tính giá
    // CreatedBy nlhoang 03/07/2020
    private function _getOrdersForCalcPrice($params)
    {
        $orders =
            DB::table('routes as r')
            ->join('orders as o', 'o.route_id', '=', 'r.id')
            ->leftJoin('locations as l1', 'l1.id', '=', 'o.location_destination_id')
            ->leftJoin('locations as l2', 'l2.id', '=', 'o.location_arrival_id')
            ->leftJoin('location_group as lg1', 'lg1.id', '=', 'l1.location_group_id')
            ->leftJoin('location_group as lg2', 'lg2.id', '=', 'l2.location_group_id')
            ->leftJoin('vehicle', 'vehicle.id', '=', 'o.vehicle_id')
            ->leftJoin('m_vehicle_group as vg', 'vg.id', '=', 'vehicle.group_id')
            ->leftJoin('customer as c', 'c.id', '=', 'o.customer_id')
            ->where([
                ['r.id', '=', $params['routeId']],
                ['c.id', '=', $params['customerId']],
                ['r.del_flag', '=', 0],
            ])
            ->get([
                'o.id as order_id',
                'l1.location_group_id as location_group_destination_id',
                'l2.location_group_id as location_group_arrival_id',
                'vehicle.group_id as vehicle_group_id',
                'o.volume',
                'o.weight',
                DB::raw('IFNULL(o.gps_distance, 0) as distance')

            ]);
        return $orders;
    }



    // Tính giá theo hàng hoá trên xe
    // CreatedBy nlhoang 23/07/2020
    private function _calcPriceByGoodsOfVehicle($priceQuote, $orders)
    {
        $goodsItems =
            DB::table('order_goods as og')
            ->leftJoin('goods_type as gt', 'gt.id', '=', 'og.goods_type_id')
            ->whereIn('og.order_id', $orders->pluck('order_id'))
            ->get([
                'og.order_id as order_id',
                'og.quantity',
                'og.goods_type_id as goods_type_id',
                'gt.title as name_of_goods_type_id'
            ]);
        foreach ($orders as $key => $order) {
            $goods = array_filter(($goodsItems->toArray()), function ($element) use ($order) {
                return $element->{'order_id'} == $order->{'order_id'};
            });
            $order->{'goods'} = $goods;
        }
        return $this->_calcPriceByVehicle($priceQuote, $orders, 'goods.goods_type_id', true, 'equal');
    }

    // Tính giá theo chủng loại xe
    // CreatedBy nlhoang 03/07/2020
    private function _calcPriceByVehicleGroup($priceQuote, $orders)
    {
        return $this->_calcPriceByVehicle($priceQuote, $orders, 'vehicle_group_id', false, 'equal');
    }

    // Tính giá theo trọng lượng xe
    // CreatedBy nlhoang 03/07/2020
    private function _calcPriceByWeightOfVehicle($priceQuote, $orders)
    {
        return $this->_calcPriceByVehicle($priceQuote, $orders, 'weight', true);
    }

    // Tính giá theo thể tích xe
    // CreatedBy nlhoang 03/07/2020
    private function _calcPriceByVolumeOfVehicle($priceQuote, $orders)
    {
        return $this->_calcPriceByVehicle($priceQuote, $orders, 'volume', true);
    }

    // Tính giá theo thuộc tính của xe
    // CreatedBy nlhoang 03/07/2020
    public function _calcPriceByVehicle($priceQuote, $orders, $attribute, $isMultipleQuantity = false, $operator = null)
    {
        $results = [];
        if (count($orders) == 0) {
            return $results;
        }

        $tmp_attribute = $attribute;
        $temp = explode(".", $attribute);
        $is_nest_prop = false;
        if (count($temp) > 1) {
            $tmp_attribute = $temp[1];
            $is_nest_prop = true;
        }
        foreach ($orders as $item) {
            $result = [
                'order_id' => $item->{'order_id'},
                'amount' => 0,
                'description' => '',
                'is_point_charge' => false
            ];
            $infos = $this->_getPriceFormula($priceQuote, $item, $attribute, $operator);
            $amounts = [];


            if ($is_nest_prop) {
                $prop = $temp[0];
                $list = $item->{$prop};

                if (is_array($list)) {
                    foreach ($list as $list_item) {
                        foreach ($infos as $info) {
                            $tmp = $this->calcAmount($list_item, $info, $tmp_attribute, $operator);
                            if ($priceQuote->isDistance == 1) {
                                $amounts[] = $isMultipleQuantity ? (property_exists($list_item, 'quantity') ? $list_item->{'quantity'} * $tmp["amount"] * $item->{"distance"} : 0) : $tmp["amount"] * $item->{"distance"};
                            } else {
                                $amounts[] = $isMultipleQuantity ? (property_exists($list_item, 'quantity') ? $list_item->{'quantity'} * $tmp["amount"] : ($item->{$attribute} * $tmp["amount"])) : $tmp["amount"];
                            }
                            if (!empty($tmp["operator"])) {
                                $result['description'] = ($result['description'] == null ? '' : $result['description'] . '<hr/>') . $this->_generateDescription($tmp, $info, $tmp_attribute, $isMultipleQuantity, $list_item, $item->{"distance"});
                            }
                        }
                    }
                    $result['amount'] = collect($amounts)->sum();
                }
            } else {
                $result[$attribute] = $item->{$attribute};
                foreach ($infos as $info) {
                    $tmp = $this->calcAmount($item, $info, $attribute, $operator);
                    if ($priceQuote->isDistance == 1) {
                        $amounts[] = $isMultipleQuantity ? (property_exists($item, 'quantity') ? $item->{'quantity'} * $tmp["amount"] * $item->{"distance"} : 0) : $tmp["amount"] * $item->{"distance"};
                    } else {
                        $amounts[] = $isMultipleQuantity ? (property_exists($item, 'quantity') ? $item->{'quantity'} * $tmp["amount"] : ($item->{$attribute} * $tmp["amount"])) : $tmp["amount"];
                    }
                    if (!empty($tmp["operator"])) {
                        $result['description'] = ($result['description'] == null ? "" : $result['description'] . '<hr/>') . $this->_generateDescription($tmp, $info, $attribute, $isMultipleQuantity, null, $item->{"distance"});
                    }
                }
                $result['amount'] = collect($amounts)->max();
            }
            $results[] = $result;
        }

        // ĐƠn hàng có giá trị lớn nhất sẽ được tính tiền cho chuyến
        $max = collect($results)->max('amount');

        $isMax = false;
        foreach ($results as &$item) {
            if ($isMax == true) {
                $item['amount'] = 0;
                $item['description'] = "";
            } else {
                if ($item['amount'] === $max) {
                    $isMax = true;
                    continue;
                } else {
                    $item['amount'] = 0;
                    $item['description'] = "";
                }
            }
        }

        // Nếu toàn bộ các đơn tính tiền bằng 0 thì ko tính phí rớt điểm
        if (collect($results)->every(function ($item, $key) {
            return $item['amount'] == 0;
        })) {
            return $results;
        }

        // Với các đơn không phải được tính tiền cho chuyến thì các đơn đó
        // là các đơn được trả cho phí rớt điểm
        $pointCharges = $this->_getPriceFormulaPointCharge($priceQuote, $attribute, $operator);

        $tmp_point_charges = [];
        foreach ($orders as $order) {
            $pointChargeAmounts = [];
            $pointChargeAmount = 0;
            $description = null;
            if ($is_nest_prop) {
                $prop = $temp[0];
                $list = property_exists($order, $prop) ? $order->{$prop} : [];
                if (is_array($list)) {
                    foreach ($list as $list_item) {
                        foreach ($pointCharges as $pointCharge) {
                            $tmp = $this->calcAmount($list_item, $pointCharge, $tmp_attribute, $operator);
                            if ($priceQuote->isDistance == 1) {
                                $pointChargeAmounts[] = $isMultipleQuantity ? $list_item->{'quantity'} * $tmp["amount"] * $order->{'distance'} : $tmp["amount"];
                            } else {
                                $pointChargeAmounts[] = $isMultipleQuantity ? $list_item->{'quantity'} * $tmp["amount"] : $tmp["amount"];
                            }
                            if (!empty($tmp["operator"])) {
                                $description = ($description == null ? "" : $description . '<hr/>') .
                                    $this->_generateDescription($tmp, $pointCharge, $tmp_attribute, $isMultipleQuantity, $list_item, $order->{"distance"});
                            }
                        }
                    }
                }
                $pointChargeAmount = collect($pointChargeAmounts)->sum();
            } else {
                foreach ($pointCharges as $pointCharge) {
                    $tmp = $this->calcAmount($order, $pointCharge, $attribute, $operator);
                    if ($priceQuote->isDistance == 1) {
                        $pointChargeAmounts[] = $isMultipleQuantity ? $tmp["amount"] * $order->{'distance'} : $tmp["amount"];
                    } else {
                        $pointChargeAmounts[] = $isMultipleQuantity ? $tmp["amount"] : $tmp["amount"];
                    }
                    if (!empty($tmp["operator"])) {
                        $description = ($description == null ? "" : $description . '<hr/>') .
                            $this->_generateDescription($tmp, $pointCharge, $attribute, $isMultipleQuantity, null, $order->{"distance"});
                    }
                }
                $pointChargeAmount = collect($pointChargeAmounts)->max();
            }
            $tmp_point_charges[] = [
                'order_id' => $order->{'order_id'},
                'amount' => $pointChargeAmount,
                'description' => $description
            ];
        }

        foreach ($results as &$item) {
            if ($item['amount'] == 0) {
                foreach ($tmp_point_charges as $tmp_point_charge) {
                    if ($tmp_point_charge["order_id"] == $item["order_id"]) {
                        $item['amount'] = $pointChargeAmount;
                        $item['is_point_charge'] = true;
                        $item['description'] = $description;
                    }
                }
            }
        }
        return $results;
    }

    // Lấy công thức báo giá
    // CreatedBy nlhoang 27/07/2020
    private function _getPriceFormula($priceQuote, $item, $attribute, $operator)
    {
        $tmp_attribute = $attribute;
        $temp = explode(".", $attribute);
        $columns = [
            'pq.isDistance',
            'operator',
            'price'
        ];
        if ($priceQuote->isDistance == null || $priceQuote->isDistance == 0) {
            array_push(
                $columns,
                DB::raw('lg1.title as location_group_destination_title'),
                DB::raw('lg2.title as location_group_arrival_title')
            );
        }
        if (count($temp) > 1) {
            $tmp_attribute = $temp[1];
            array_push(
                $columns,
                $tmp_attribute,
                'gt.title as name_of_' . $tmp_attribute
            );
        } else {

            if ($operator != null) {
                array_push(
                    $columns,
                    $tmp_attribute,
                    'vg.name as name_of_' . $tmp_attribute
                );
            } else {
                array_push(
                    $columns,
                    $tmp_attribute . '_from',
                    $tmp_attribute . '_to',
                    $tmp_attribute . '_from as name_of_' . $tmp_attribute
                );
            }
        }
        $query = DB::table('price_quote_formula as pqf')
            ->join('price_quote as pq', 'pq.id', '=', 'pqf.price_quote_id')
            ->leftJoin('m_vehicle_group as vg', 'vg.id', '=', 'pqf.vehicle_group_id')
            ->leftJoin('goods_type as gt', 'gt.id', '=', 'pqf.goods_type_id')
            ->where('pqf.del_flag', '=', 0)
            ->where('pqf.price_quote_id', '=', $priceQuote->{'id'});
        if ($priceQuote->isDistance == 0) {
            $query = $query->leftJoin('location_group as lg1', 'lg1.id', '=', 'pqf.location_group_destination_id')
                ->leftJoin('location_group as lg2', 'lg2.id', '=', 'pqf.location_group_arrival_id')
                ->where('pqf.location_group_destination_id', '=', $item->{'location_group_destination_id'})
                ->where('pqf.location_group_arrival_id', '=', $item->{'location_group_arrival_id'});
        }
        $infos = $query->get($columns);
        return $infos;
    }

    // Lấy phí rớt điểm
    // CreatedBy nlhoang 27/07/2020
    private function _getPriceFormulaPointCharge($priceQuote, $attribute, $operator)
    {
        $tmp_attribute = $attribute;
        $temp = explode(".", $attribute);
        $columns = [
            'operator',
            'price'
        ];
        if (count($temp) > 1) {
            $tmp_attribute = $temp[1];
            array_push(
                $columns,
                $tmp_attribute,
                'gt.title as name_of_' . $tmp_attribute
            );
        } else {

            if ($operator != null) {
                array_push(
                    $columns,
                    $tmp_attribute,
                    'vg.name as name_of_' . $tmp_attribute
                );
            } else {
                array_push(
                    $columns,
                    $tmp_attribute . '_from',
                    $tmp_attribute . '_to',
                    $tmp_attribute . '_from as name_of_' . $tmp_attribute
                );
            }
        }
        $query = DB::table('price_quote_point_charge as pqf')
            ->leftJoin('m_vehicle_group as vg', 'vg.id', '=', 'pqf.vehicle_group_id')
            ->leftJoin('goods_type as gt', 'gt.id', '=', 'pqf.goods_type_id')
            ->where('pqf.del_flag', '=', 0)
            ->where('pqf.price_quote_id', '=', $priceQuote->{'id'});
        $infos = $query->get($columns);
        return $infos;
    }

    // Tính giá theo thuộc tính của xe
    // CreatedBy nlhoang 03/07/2020
    private function calcAmount($item, $formula, $attribute, $operator = null)
    {
        $amount = 0;
        $leftVariable = $item->{$attribute};
        $rightVariable = $operator == null ? $formula->{$attribute . '_from'} : $formula->{$attribute};
        $operator = $operator == null ? $formula->{'operator'} : $operator;

        $operator_result = '';
        switch ($operator) {

            case 'equal':
                $amount = $leftVariable == $rightVariable ? $formula->{'price'} : 0;
                $operator_result = $leftVariable == $rightVariable ? trans('models.route.price.' . $operator) : '';
                break;
            case 'not_equal':
                $amount = $leftVariable != $rightVariable ? $formula->{'price'} : 0;
                $operator_result = $leftVariable != $rightVariable ? trans('models.route.price.' . $operator) : '';
                break;
            case 'greater':
                $amount = $leftVariable > $rightVariable ? $formula->{'price'} : 0;
                $operator_result = $leftVariable > $rightVariable ? trans('models.route.price.' . $operator) : '';
                break;
            case 'less':
                $amount = $leftVariable < $rightVariable ? $formula->{'price'} : 0;
                $operator_result = $leftVariable < $rightVariable ? trans('models.route.price.' . $operator) : '';
                break;
            case 'greater_equal':
                $amount = $leftVariable >= $rightVariable ? $formula->{'price'} : 0;
                $operator_result = $leftVariable >= $rightVariable ? trans('models.route.price.' . $operator) : '';
                break;
            case 'less_equal':
                $amount = $leftVariable <= $rightVariable ? $formula->{'price'} : 0;
                $operator_result = $leftVariable <= $rightVariable ? trans('models.route.price.' . $operator) : '';
                break;
            case 'in':
                $amount = ($leftVariable >= $formula->{$attribute . '_from'} && $leftVariable <= $formula->{$attribute . '_to'}) ?
                    $formula->{'price'} : 0;
                $operator_result = ($leftVariable >= $formula->{$attribute . '_from'} && $leftVariable <= $formula->{$attribute . '_to'}) ? trans('models.route.price.' . $operator) : '';
                break;
            default:
                $operator_result = $leftVariable == $rightVariable ? trans('models.route.price.' . $operator) : '';
                $amount = $leftVariable == $rightVariable ? $formula->{'price'} : 0;
                break;
        }
        return [
            'amount' => $amount,
            'operator' => $operator_result,
            'input_operator' => $operator
        ];
    }

    private function _generateDescription($amountPrice, $priceQuote, $attribute, $isMultipleQuantity, $list_item, $distance)
    {
        $result = '';
        if (property_exists($priceQuote, 'isDistance')) {
            if ($priceQuote->isDistance == 0) {
                $result .=
                    trans('models.route.price.location_group_destination_title') . ': ' . $priceQuote->{'location_group_destination_title'} . '<br/>' .
                    trans('models.route.price.location_group_arrival_title') . ': ' . $priceQuote->{'location_group_arrival_title'} . '<br/>';
            } else {
                if ($distance !== null) {
                    $result .= trans('models.route.price.distance') . ': ' . numberFormat($distance) . '<br/>';
                }
            }
        } else {
            $result .= trans('models.route.price.point_charge') . '<br/>';
        }
        $result .= trans('models.route.price.operator') . ':' . $amountPrice["operator"] . '<br/>';
        if ($amountPrice['input_operator'] == 'in') {
            $result .=
                trans('models.route.price.' . $attribute) . ': ' . (numberFormat($priceQuote->{$attribute . '_from'})) . '-' . (numberFormat($priceQuote->{$attribute . '_to'})) . '<br/>';
        } else {
            $result .=
                trans('models.route.price.' . $attribute) . ': ' . (is_string($priceQuote->{'name_of_' . $attribute}) ? $priceQuote->{'name_of_' . $attribute} : numberFormat($priceQuote->{'name_of_' . $attribute})) . '<br/>';
        }

        if ($list_item !== null) {
            $result .= ($isMultipleQuantity ? trans('models.route.price.quantity') . ': ' . numberFormat($list_item->{'quantity'}) . '<br/>' : '');
        }

        $result .= trans('models.route.price.amount') . ': ' . numberFormat($amountPrice["amount"]);
        return $result;
    }
}
