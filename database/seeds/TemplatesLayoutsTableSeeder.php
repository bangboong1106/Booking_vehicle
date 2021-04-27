<?php

use App\Model\Entities\TemplateLayout;
use Illuminate\Database\Seeder;

class TemplatesLayoutsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TemplateLayout::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->createOrder();
        $this->createDocument();
        $this->createRoute();
        $this->createOrderCustomer();
        $this->createCustomer();
        $this->createVehicle();
        $this->createDriver();
        $this->createQuota();
    }

    private function createOrder()
    {
        $items = [
            [
                'table_name' => 'orders',
                'column_name' => 'index',
                'display_name' => 'Số thứ tự',
                'merge_name' => '${Số thứ tự}',
                'sort_order' => 0, 'field_type' => 'INDEX', 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'sysdate',
                'display_name' => 'Ngày giờ xuất tệp tin',
                'merge_name' => '${Ngày giờ xuất tệp tin}',
                'sort_order' => 1, 'field_type' => 'SYSDATE', 'data_type' => 'DATETIME', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'order_code',
                'display_name' => 'Mã hệ thống',
                'merge_name' => '${Mã hệ thống}',
                'sort_order' => 2, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'order_no',
                'display_name' => 'Số đơn hàng',
                'merge_name' => '${Số đơn hàng}',
                'sort_order' => 3, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'bill_no',
                'display_name' => 'Số hóa đơn',
                'merge_name' => '${Số hóa đơn}',
                'sort_order' => 4, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],

        ];

        $supportCarTransportation = env('SUPPORT_CAR_TRANSPORTATION', false);
        if ($supportCarTransportation) {
            array_push(
                $items,
                [
                    'table_name' => 'orders',
                    'column_name' => 'model_no',
                    'display_name' => 'Số model',
                    'merge_name' => '${Số model}',
                    'sort_order' => 4, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
                ],
                [
                    'table_name' => 'orders',
                    'column_name' => 'vin_no',
                    'display_name' => "Số khung xe",
                    'merge_name' => '${Số khung xe}',
                    'sort_order' => 4, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
                ]
            );
        }
        array_push(
            $items,
            [
                'table_name' => 'orders',
                'column_name' => 'is_merge_item',
                'display_name' => 'Là hàng ghép hay không',
                'merge_name' => '${Là hàng ghép hay không}',
                'sort_order' => 4, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'status',
                'display_name' => 'Trạng thái',
                'merge_name' => '${Trạng thái}',
                'sort_order' => 5, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'customer_code',
                'display_name' => 'Mã khách hàng',
                'merge_name' => '${Mã khách hàng}',
                'sort_order' => 6, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'customer_full_name',
                'display_name' => 'Tên khách hàng',
                'merge_name' => '${Tên khách hàng}',
                'sort_order' => 6, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'customer_name',
                'display_name' => 'Tên liên hệ',
                'merge_name' => '${Tên liên hệ}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'customer_mobile_no',
                'display_name' => 'Số điện thoại liên hệ',
                'merge_name' => '${Số điện thoại liên hệ}',
                'sort_order' => 8, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'order_date',
                'display_name' => 'Ngày đặt hàng',
                'merge_name' => '${Ngày đặt hàng}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'DATE', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'note',
                'display_name' => 'Chú thích',
                'merge_name' => '${Chú thích}',
                'sort_order' => 10, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'precedence',
                'display_name' => 'Độ ưu tiên',
                'merge_name' => '${Độ ưu tiên}',
                'sort_order' => 12, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'commission_amount',
                'display_name' => 'Phí hoa hồng',
                'merge_name' => '${Phí hoa hồng}',
                'sort_order' => 13, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'commission_value',
                'display_name' => 'Tỉ lệ hoa hồng',
                'merge_name' => '${Tỉ lệ hoa hồng}',
                'sort_order' => 14, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'amount',
                'display_name' => 'Cước phí vận chuyển',
                'merge_name' => '${Cước phí vận chuyển}',
                'sort_order' => 15, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'ETD_time_reality',
                'display_name' => 'Giờ nhận hàng thực tế',
                'merge_name' => '${Giờ nhận hàng thực tế}',
                'sort_order' => 15, 'field_type' => null, 'data_type' => 'TIME', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'ETD_date_reality',
                'display_name' => 'Ngày nhận hàng thực tế',
                'merge_name' => '${Ngày nhận hàng thực tế}',
                'sort_order' => 15, 'field_type' => null, 'data_type' => 'DATE', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'ETA_time_reality',
                'display_name' => 'Giờ trả hàng thực tế',
                'merge_name' => '${Giờ trả hàng thực tế}',
                'sort_order' => 15, 'field_type' => null, 'data_type' => 'TIME', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'ETA_date_reality',
                'display_name' => 'Ngày trả hàng thực tế',
                'merge_name' => '${Ngày trả hàng thực tế}',
                'sort_order' => 15, 'field_type' => null, 'data_type' => 'DATE', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_name_destination',
                'display_name' => 'Tên liên hệ nhận hàng',
                'merge_name' => '${Tên liên hệ nhận hàng}',
                'sort_order' => 16, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_mobile_no_destination',
                'display_name' => 'Điện thoại liên hệ nhận hàng',
                'merge_name' => '${Điện thoại liên hệ nhận hàng}',
                'sort_order' => 17, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_email_destination',
                'display_name' => 'Email nhận hàng',
                'merge_name' => '${Email nhận hàng}',
                'sort_order' => 18, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'informative_destination',
                'display_name' => 'Thông tin bổ sung nhận hàng',
                'merge_name' => '${Thông tin bổ sung nhận hàng}',
                'sort_order' => 19, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_name_arrival',
                'display_name' => 'Tên liên hệ trả hàng',
                'merge_name' => '${Tên liên hệ trả hàng}',
                'sort_order' => 20, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_mobile_no_arrival',
                'display_name' => 'Điện thoại liên hệ trả hàng',
                'merge_name' => '${Điện thoại liên hệ trả hàng}',
                'sort_order' => 21, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_email_arrival',
                'display_name' => 'Email trả hàng',
                'merge_name' => '${Email trả hàng}',
                'sort_order' => 22, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'informative_arrival',
                'display_name' => 'Thông tin bổ sung trả hàng',
                'merge_name' => '${Thông tin bổ sung trả hàng}',
                'sort_order' => 23, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'quantity',
                'display_name' => 'Tổng số lượng',
                'merge_name' => '${Tổng số lượng}',
                'sort_order' => 24, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'volume',
                'display_name' => 'Tổng thể tích',
                'merge_name' => '${Tổng thể tích}',
                'sort_order' => 24, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'weight',
                'display_name' => 'Tổng khối lượng',
                'merge_name' => '${Tổng khối lượng}',
                'sort_order' => 25, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'final_amount',
                'display_name' => 'Doanh thu',
                'merge_name' => '${Doanh thu}',
                'sort_order' => 26, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'loading_destination_fee',
                'display_name' => 'Phí bốc xếp hàng hóa nhận hàng',
                'merge_name' => '${Phí bốc xếp hàng hóa nhận hàng}',
                'sort_order' => 27, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'loading_arrival_fee',
                'display_name' => 'Phí bốc xếp hàng hoá trả hàng',
                'merge_name' => '${Phí bốc xếp hàng hoá trả hàng}',
                'sort_order' => 28, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 1, 'del_flag' => 0
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.index',
                'display_name' => 'Địa điểm.Số thứ tự',
                'merge_name' => '${Địa điểm.Số thứ tự}',
                'sort_order' => 29, 'field_type' => 'ARRAY.INDEX', 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.title',
                'display_name' => 'Địa điểm.Địa điểm nhận hàng',
                'merge_name' => '${Địa điểm.Địa điểm nhận hàng}',
                'sort_order' => 30, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.ward_title',
                'display_name' => 'Địa điểm.Xã/Phường nhận hàng',
                'merge_name' => '${Địa điểm.Xã/Phường nhận hàng}',
                'sort_order' => 30, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.district_title',
                'display_name' => 'Địa điểm.Quận/Huyện nhận hàng',
                'merge_name' => '${Địa điểm.Quận/Huyện nhận hàng}',
                'sort_order' => 30, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.province_title',
                'display_name' => 'Địa điểm.Tỉnh/Thành phố nhận hàng',
                'merge_name' => '${Địa điểm.Tỉnh/Thành phố nhận hàng}',
                'sort_order' => 30, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.date',
                'display_name' => 'Địa điểm.Ngày nhận hàng',
                'merge_name' => '${Địa điểm.Ngày nhận hàng}',
                'sort_order' => 31, 'field_type' => 'ARRAY', 'data_type' => 'DATE', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.time',
                'display_name' => 'Địa điểm.Giờ nhận hàng',
                'merge_name' => '${Địa điểm.Giờ nhận hàng}',
                'sort_order' => 32, 'field_type' => 'ARRAY', 'data_type' => 'TIME', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.date_reality',
                'display_name' => 'Địa điểm.Ngày nhận hàng thực tế',
                'merge_name' => '${Địa điểm.Ngày nhận hàng thực tế}',
                'sort_order' => 33, 'field_type' => 'ARRAY', 'data_type' => 'DATE', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.time_reality',
                'display_name' => 'Địa điểm.Giờ nhận hàng thực tế',
                'merge_name' => '${Địa điểm.Giờ nhận hàng thực tế}',
                'sort_order' => 34, 'field_type' => 'ARRAY', 'data_type' => 'TIME', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.title',
                'display_name' => 'Địa điểm.Địa điểm trả hàng',
                'merge_name' => '${Địa điểm.Địa điểm trả hàng}',
                'sort_order' => 35, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.ward_title',
                'display_name' => 'Địa điểm.Xã/Phường trả hàng',
                'merge_name' => '${Địa điểm.Xã/Phường trả hàng}',
                'sort_order' => 35, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.district_title',
                'display_name' => 'Địa điểm.Quận/Huyện trả hàng',
                'merge_name' => '${Địa điểm.Quận/Huyện trả hàng}',
                'sort_order' => 35, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.province_title',
                'display_name' => 'Địa điểm.Tỉnh/Thành phố trả hàng',
                'merge_name' => '${Địa điểm.Tỉnh/Thành phố trả hàng}',
                'sort_order' => 35, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.date',
                'display_name' => 'Địa điểm.Ngày trả hàng',
                'merge_name' => '${Địa điểm.Ngày trả hàng}',
                'sort_order' => 36, 'field_type' => 'ARRAY', 'data_type' => 'DATE', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.time',
                'display_name' => 'Địa điểm.Giờ trả hàng',
                'merge_name' => '${Địa điểm.Giờ trả hàng}',
                'sort_order' => 37, 'field_type' => 'ARRAY', 'data_type' => 'TIME', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.date_reality',
                'display_name' => 'Địa điểm.Ngày trả hàng thực tế',
                'merge_name' => '${Địa điểm.Ngày trả hàng thực tế}',
                'sort_order' => 38, 'field_type' => 'ARRAY', 'data_type' => 'DATE', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.time_reality',
                'display_name' => 'Địa điểm.Giờ trả hàng thực tế',
                'merge_name' => '${Địa điểm.Giờ trả hàng thực tế}',
                'sort_order' => 39, 'field_type' => 'ARRAY', 'data_type' => 'TIME', 'type' => 1, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listGoods.index',
                'display_name' => 'Bảng hàng hóa.Số thứ tự',
                'merge_name' => '${Bảng hàng hóa.Số thứ tự}',
                'sort_order' => 40, 'field_type' => 'ARRAY.INDEX', 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listGoods.title',
                'display_name' => 'Bảng hàng hóa.Loại hàng hóa',
                'merge_name' => '${Bảng hàng hóa.Loại hàng hóa}',
                'sort_order' => 41, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_dynamic_column' => 1, 'dynamic_column_header' => 1
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'listGoods.quantity',
                'display_name' => 'Bảng hàng hóa.Số lượng',
                'merge_name' => '${Bảng hàng hóa.Số lượng}',
                'sort_order' => 42, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'listGoods.unitTitle',
                'display_name' => 'Bảng hàng hóa.Đơn vị',
                'merge_name' => '${Bảng hàng hóa.Đơn vị}',
                'sort_order' => 43, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'listGoods.insured_goods',
                'display_name' => 'Bảng hàng hóa.Hàng hóa có bảo hiểm',
                'merge_name' => '${Bảng hàng hóa.Hàng hóa có bảo hiểm}',
                'sort_order' => 44, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'listGoods.weight',
                'display_name' => 'Bảng hàng hóa.Tải trọng',
                'merge_name' => '${Bảng hàng hóa.Tải trọng}',
                'sort_order' => 45, 'field_type' => 'ARRAY', 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'listGoods.volume',
                'display_name' => 'Bảng hàng hóa.Dung tích',
                'merge_name' => '${Bảng hàng hóa.Dung tích}',
                'sort_order' => 46, 'field_type' => 'ARRAY', 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'listGoods.total_weight',
                'display_name' => 'Bảng hàng hóa.Khối lượng',
                'merge_name' => '${Bảng hàng hóa.Khối lượng}',
                'sort_order' => 47, 'field_type' => 'ARRAY', 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'listGoods.total_volume',
                'display_name' => 'Bảng hàng hóa.Thể tích',
                'merge_name' => '${Bảng hàng hóa.Thể tích}',
                'sort_order' => 48, 'field_type' => 'ARRAY', 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'listGoods.note',
                'display_name' => 'Bảng hàng hóa.Ghi chú',
                'merge_name' => '${Bảng hàng hóa.Ghi chú}',
                'sort_order' => 49, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'good_details',
                'display_name' => 'Mô tả hàng hóa',
                'merge_name' => '${Mô tả hàng hóa}',
                'sort_order' => 50, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'reg_no',
                'display_name' => 'Thông tin xe',
                'merge_name' => '${Thông tin xe}',
                'sort_order' => 51, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'vehicle_group',
                'display_name' => 'Chủng loại xe',
                'merge_name' => '${Chủng loại xe}',
                'sort_order' => 51, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'full_name',
                'display_name' => 'Thông tin tài xế',
                'merge_name' => '${Thông tin tài xế}',
                'sort_order' => 52, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'note',
                'display_name' => 'Thông tin bổ sung (ghi chú)',
                'merge_name' => '${Thông tin bổ sung (ghi chú)}',
                'sort_order' => 53, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'payment_type_title',
                'display_name' => 'Hình thức thanh toán',
                'merge_name' => '${Hình thức thanh toán}',
                'sort_order' => 54, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'pu_username',
                'display_name' => 'Người chịu trách nhiệm thanh toán',
                'merge_name' => '${Người chịu trách nhiệm thanh toán}',
                'sort_order' => 55, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'vat_title',
                'display_name' => 'VAT',
                'merge_name' => '${VAT}',
                'sort_order' => 56, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'goods_amount',
                'display_name' => 'Giá trị hàng hóa',
                'merge_name' => '${Giá trị hàng hóa}',
                'sort_order' => 57, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'anonymous_amount',
                'display_name' => 'Cước gửi',
                'merge_name' => '${Cước gửi}',
                'sort_order' => 58, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listGoodsTitle',
                'display_name' => 'Loại hàng hóa',
                'merge_name' => '${Loại hàng hóa}',
                'sort_order' => 59, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'is_insured_goods',
                'display_name' => 'Bảo hiểm hàng hóa',
                'merge_name' => '${Bảo hiểm hàng hóa}',
                'sort_order' => 60, 'field_type' => null, 'data_type' => 'STRING', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'quantity_order_customer',
                'display_name' => 'Tổng số lượng ĐHKH',
                'merge_name' => '${Tổng số lượng ĐHKH}',
                'sort_order' => 61, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'volume_order_customer',
                'display_name' => 'Tổng thể tích ĐHKH',
                'merge_name' => '${Tổng thể tích ĐHKH}',
                'sort_order' => 62, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'weight_order_customer',
                'display_name' => 'Tổng khối lượng ĐHKH',
                'merge_name' => '${Tổng khối lượng ĐHKH}',
                'sort_order' => 63, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'number_of_delivery_points',
                'display_name' => 'Số lượng điểm nhận hàng',
                'merge_name' => '${Số lượng điểm nhận hàng}',
                'sort_order' => 64, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'number_of_arrival_points',
                'display_name' => 'Số lượng điểm trả hàng',
                'merge_name' => '${Số lượng điểm trả hàng}',
                'sort_order' => 65, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 1, 'del_flag' => 0
            ]
        );
        foreach ($items as $item) {
            TemplateLayout::firstOrCreate($item);
        }
    }

    private function createRoute()
    {
        // Chuyến xe
        $items = [
            [
                'table_name' => 'routes',
                'column_name' => 'index',
                'display_name' => 'Số thứ tự',
                'merge_name' => '${Số thứ tự}',
                'sort_order' => 1, 'field_type' => 'INDEX', 'data_type' => 'NUMBER', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'sysdate',
                'display_name' => 'Ngày giờ xuất tệp tin',
                'merge_name' => '${Ngày giờ xuất tệp tin}',
                'sort_order' => 2, 'field_type' => 'SYSDATE', 'data_type' => 'DATETIME', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'route_code',
                'display_name' => 'Mã chuyến',
                'merge_name' => '${Mã chuyến}',
                'sort_order' => 3, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'name',
                'display_name' => 'Tên chuyến',
                'merge_name' => '${Tên chuyến}',
                'sort_order' => 4, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ]
        ];
        $supportCarTransportation = env('SUPPORT_CAR_TRANSPORTATION', false);

        if ($supportCarTransportation) {
            array_push(
                $items,
                [
                    'table_name' => 'routes',
                    'column_name' => 'vin_nos',
                    'display_name' => 'Danh sách khung xe',
                    'merge_name' => '${Danh sách khung xe}',
                    'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
                ],
                [
                    'table_name' => 'routes',
                    'column_name' => 'model_nos',
                    'display_name' => 'Danh sách model',
                    'merge_name' => '${Danh sách model}',
                    'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
                ]
            );
        }

        array_push(
            $items,
            [
                'table_name' => 'routes',
                'column_name' => 'vehicle',
                'display_name' => 'Xe',
                'merge_name' => '${Xe}',
                'sort_order' => 5, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'vehicle_group',
                'display_name' => 'Chủng loại xe',
                'merge_name' => '${Chủng loại xe}',
                'sort_order' => 5, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'driver',
                'display_name' => 'Tài xế',
                'merge_name' => '${Tài xế}',
                'sort_order' => 6, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_order',
                'display_name' => 'Danh sách đơn hàng',
                'merge_name' => '${Danh sách đơn hàng}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_quantity',
                'display_name' => 'Danh sách số lượng',
                'merge_name' => '${Danh sách số lượng}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_weight',
                'display_name' => 'Danh sách khối lượng',
                'merge_name' => '${Danh sách khối lượng}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_volume',
                'display_name' => 'Danh sách dung tích',
                'merge_name' => '${Danh sách dung tích}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'routes_status',
                'display_name' => 'Trạng thái',
                'merge_name' => '${Trạng thái}',
                'sort_order' => 8, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'total_order',
                'display_name' => 'Tổng đơn hàng',
                'merge_name' => '${Tổng đơn hàng}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 3, 'del_flag' => 0
            ],
            [
                'column_name' => 'routes_amount',
                'table_name' => 'routes',
                'display_name' => 'Doanh thu chuyến xe',
                'merge_name' => '${Doanh thu chuyến xe}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'total_quantity',
                'display_name' => 'Tổng số lượng',
                'merge_name' => '${Tổng số lượng}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'total_weight',
                'display_name' => 'Tổng khối lượng',
                'merge_name' => '${Tổng khối lượng}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'total_volume',
                'display_name' => 'Tổng dung tích',
                'merge_name' => '${Tổng dung tích}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_customer',
                'display_name' => 'Danh sách khách hàng',
                'merge_name' => '${Danh sách khách hàng}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'approve_status',
                'display_name' => 'Trạng thái phê duyệt',
                'merge_name' => '${Trạng thái phê duyệt}',
                'sort_order' => 10, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'approved_note',
                'display_name' => 'Ghi chú phê duyệt',
                'merge_name' => '${Ghi chú phê duyệt}',
                'sort_order' => 11, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'approved_name',
                'display_name' => 'Người phê duyệt',
                'merge_name' => '${Người phê duyệt}',
                'sort_order' => 12, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'approved_date',
                'display_name' => 'Ngày phê duyệt',
                'merge_name' => '${Ngày phê duyệt}',
                'sort_order' => 13, 'field_type' => null, 'data_type' => 'DATE', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'ETD_date',
                'display_name' => 'Ngày dự kiến xuất phát',
                'merge_name' => '${Ngày dự kiến xuất phát}',
                'sort_order' => 14, 'field_type' => null, 'data_type' => 'DATE', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'ETD_date_reality',
                'display_name' => 'Ngày nhận hàng thực tế',
                'merge_name' => '${Ngày nhận hàng thực tế}',
                'sort_order' => 15, 'field_type' => null, 'data_type' => 'DATE', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'ETA_date',
                'display_name' => 'Ngày dự kiến đến',
                'merge_name' => '${Ngày dự kiến đến}',
                'sort_order' => 16, 'field_type' => null, 'data_type' => 'DATE', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'ETA_date_reality',
                'display_name' => 'Ngày trả hàng thực tế',
                'merge_name' => '${Ngày trả hàng thực tế}',
                'sort_order' => 17, 'field_type' => null, 'data_type' => 'DATE', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'gps_distance',
                'display_name' => 'Khoảng cách chạy thực tế (GPS)',
                'merge_name' => '${Khoảng cách chạy thực tế (GPS)}',
                'sort_order' => 17, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'route_note',
                'display_name' => 'Ghi chú',
                'merge_name' => '${Ghi chú}',
                'sort_order' => 17, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_locations.index',
                'display_name' => 'Thông tin lộ trình.Số thứ tự',
                'merge_name' => '${Thông tin lộ trình.Số thứ tự}',
                'sort_order' => 18, 'field_type' => 'ARRAY.INDEX', 'data_type' => 'NUMBER', 'type' => 3, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_locations.destination_location_title',
                'display_name' => 'Thông tin lộ trình.Điểm nhận hàng',
                'merge_name' => '${Thông tin lộ trình.Điểm nhận hàng}',
                'sort_order' => 19, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_locations.name_of_district_destination_id',
                'display_name' => 'Thông tin lộ trình.Quận/Huyện điểm nhận hàng',
                'merge_name' => '${Thông tin lộ trình.Quận/Huyện điểm nhận hàng}',
                'sort_order' => 19, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_locations.name_of_province_destination_id',
                'display_name' => 'Thông tin lộ trình.Tỉnh/Thành phố điểm nhận hàng',
                'merge_name' => '${Thông tin lộ trình.Tỉnh/Thành phố điểm nhận hàng}',
                'sort_order' => 19, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_locations.destination_location_date',
                'display_name' => 'Thông tin lộ trình.Ngày nhận hàng',
                'merge_name' => '${Thông tin lộ trình.Ngày nhận hàng}',
                'sort_order' => 20, 'field_type' => 'ARRAY', 'data_type' => 'DATE', 'type' => 3, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_locations.destination_location_time',
                'display_name' => 'Thông tin lộ trình.Giờ nhận hàng',
                'merge_name' => '${Thông tin lộ trình.Giờ nhận hàng}',
                'sort_order' => 21, 'field_type' => 'ARRAY', 'data_type' => 'TIME', 'type' => 3, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_locations.arrival_location_title',
                'display_name' => 'Thông tin lộ trình.Điểm trả hàng',
                'merge_name' => '${Thông tin lộ trình.Điểm trả hàng}',
                'sort_order' => 22, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_locations.name_of_district_arrival_id',
                'display_name' => 'Thông tin lộ trình.Quận/Huyện điểm trả hàng',
                'merge_name' => '${Thông tin lộ trình.Quận/Huyện điểm trả hàng}',
                'sort_order' => 22, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_locations.name_of_province_arrival_id',
                'display_name' => 'Thông tin lộ trình.Tỉnh/Thành phố điểm trả hàng',
                'merge_name' => '${Thông tin lộ trình.Tỉnh/Thành phố điểm trả hàng}',
                'sort_order' => 22, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_locations.arrival_location_date',
                'display_name' => 'Thông tin lộ trình.Ngày trả hàng',
                'merge_name' => '${Thông tin lộ trình.Ngày trả hàng}',
                'sort_order' => 23, 'field_type' => 'ARRAY', 'data_type' => 'DATE', 'type' => 3, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_locations.arrival_location_time',
                'display_name' => 'Thông tin lộ trình.Giờ trả hàng',
                'merge_name' => '${Thông tin lộ trình.Giờ trả hàng}',
                'sort_order' => 24, 'field_type' => 'ARRAY', 'data_type' => 'TIME', 'type' => 3, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'quota',
                'display_name' => 'Bảng định mức chi phí',
                'merge_name' => '${Bảng định mức chi phí}',
                'sort_order' => 25, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'total_amount_admin',
                'display_name' => 'Tổng chi phí định mức',
                'merge_name' => '${Tổng chi phí định mức}',
                'sort_order' => 26, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'total_amount_driver',
                'display_name' => 'Tổng chi phí thực tế',
                'merge_name' => '${Tổng chi phí thực tế}',
                'sort_order' => 27, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'total_amount',
                'display_name' => 'Tổng chi phí cuối cùng',
                'merge_name' => '${Tổng chi phí cuối cùng}',
                'sort_order' => 28, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_costs.index',
                'display_name' => 'Thông tin chi phí.Số thứ tự',
                'merge_name' => '${Thông tin chi phí.Số thứ tự}',
                'sort_order' => 29, 'field_type' => 'ARRAY.INDEX', 'data_type' => 'NUMBER', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_costs.receipt_payment',
                'display_name' => 'Thông tin chi phí.Diễn giải',
                'merge_name' => '${Thông tin chi phí.Diễn giải}',
                'sort_order' => 30, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0, 'allow_dynamic_column' => 1, 'dynamic_column_header' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_costs.amount_admin',
                'display_name' => 'Thông tin chi phí.Chi phí định mức',
                'merge_name' => '${Thông tin chi phí.Chi phí định mức}',
                'sort_order' => 31, 'field_type' => 'ARRAY', 'data_type' => 'CURRENCY', 'type' => 3, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_costs.amount_driver',
                'display_name' => 'Thông tin chi phí.Chi phí thực tế',
                'merge_name' => '${Thông tin chi phí.Chi phí thực tế}',
                'sort_order' => 32, 'field_type' => 'ARRAY', 'data_type' => 'CURRENCY', 'type' => 3, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_costs.amount',
                'display_name' => 'Thông tin chi phí.Chi phí cuối cùng',
                'merge_name' => '${Thông tin chi phí.Chi phí cuối cùng}',
                'sort_order' => 33, 'field_type' => 'ARRAY', 'data_type' => 'CURRENCY', 'type' => 3, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'ins_date',
                'display_name' => 'Ngày giờ tạo',
                'merge_name' => '${Ngày giờ tạo}',
                'sort_order' => 34, 'field_type' => null, 'data_type' => 'DATETIME', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'ins_user',
                'display_name' => 'Người tạo',
                'merge_name' => '${Người tạo}',
                'sort_order' => 35, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'max_fuel',
                'display_name' => 'Định mức nhiên liệu không hàng',
                'merge_name' => '${Định mức nhiên liệu không hàng}',
                'sort_order' => 36, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'max_fuel_with_goods',
                'display_name' => 'Định mức nhiên liệu có hàng',
                'merge_name' => '${Định mức nhiên liệu có hàng}',
                'sort_order' => 37, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 3, 'del_flag' => 0
            ],
            [
                'table_name' => 'routes',
                'column_name' => 'list_commission_amount',
                'display_name' => 'Danh sách phí hoa hồng',
                'merge_name' => '${Danh sách phí hoa hồng}',
                'sort_order' => 38, 'field_type' => null, 'data_type' => 'STRING', 'type' => 3, 'del_flag' => 0
            ]
        );

        foreach ($items as $item) {
            TemplateLayout::firstOrCreate($item);
        }
    }

    private function createOrderCustomer()
    {
        $items = [
            [
                'table_name' => 'order_customer',
                'column_name' => 'sysdate',
                'display_name' => 'Ngày giờ xuất tệp tin',
                'merge_name' => '${Ngày giờ xuất tệp tin}',
                'sort_order' => 0, 'field_type' => 'SYSDATE', 'data_type' => 'DATETIME', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'index',
                'display_name' => 'Số thứ tự',
                'merge_name' => '${Số thứ tự}',
                'sort_order' => 1, 'field_type' => 'INDEX', 'data_type' => 'NUMBER', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'code',
                'display_name' => 'Mã đơn hàng khách hàng',
                'merge_name' => '${Mã đơn hàng khách hàng}',
                'sort_order' => 2, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'name',
                'display_name' => 'Tên đơn hàng khách hàng',
                'merge_name' => '${Tên đơn hàng khách hàng}',
                'sort_order' => 3, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'order_no',
                'display_name' => 'Số đơn hàng',
                'merge_name' => '${Số đơn hàng}',
                'sort_order' => 4, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'order_date',
                'display_name' => 'Ngày đặt hàng',
                'merge_name' => '${Ngày đặt hàng}',
                'sort_order' => 5, 'field_type' => null, 'data_type' => 'DATE', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'status',
                'display_name' => 'Trạng thái',
                'merge_name' => '${Trạng thái}',
                'sort_order' => 6, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'code_of_customer_id',
                'display_name' => 'Mã khách hàng',
                'merge_name' => '${Mã khách hàng}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'name_of_customer_id',
                'display_name' => 'Khách hàng',
                'merge_name' => '${Khách hàng}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'customer_name',
                'display_name' => 'Tên khách hàng/người đại diện',
                'merge_name' => '${Tên khách hàng/người đại diện}',
                'sort_order' => 8, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'customer_mobile_no',
                'display_name' => 'Số điện thoại khách hàng',
                'merge_name' => '${Số điện thoại khách hàng}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'ETA_date_reality',
                'display_name' => 'Ngày hoàn thành thực tế',
                'merge_name' => '${Ngày hoàn thành thực tế}',
                'sort_order' => 10, 'field_type' => null, 'data_type' => 'DATE', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'name_of_location_destination_id',
                'display_name' => 'Điểm nhận hàng',
                'merge_name' => '${Điểm nhận hàng}',
                'sort_order' => 11, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            // [
            //     'table_name' => 'order_customer',
            //     'column_name' => 'name_of_ward_destination_id',
            //     'display_name' => 'Xã/Phường nhận hàng',
            //     'merge_name' => '${Xã/Phường nhận hàng}',
            //     'sort_order' => 11, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            // ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'name_of_district_destination_id',
                'display_name' => 'Quận/Huyện nhận hàng',
                'merge_name' => '${Quận/Huyện nhận hàng}',
                'sort_order' => 11, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'name_of_province_destination_id',
                'display_name' => 'Tỉnh/Thành phố nhận hàng',
                'merge_name' => '${Tỉnh/Thành phố nhận hàng}',
                'sort_order' => 11, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'name_of_location_arrival_id',
                'display_name' => 'Điểm trả hàng',
                'merge_name' => '${Điểm trả hàng}',
                'sort_order' => 12, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            // [
            //     'table_name' => 'order_customer',
            //     'column_name' => 'name_of_ward_arrival_id',
            //     'display_name' => 'Xã/Phường nhận hàng',
            //     'merge_name' => '${Xã/Phường nhận hàng}',
            //     'sort_order' => 11, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            // ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'name_of_district_arrival_id',
                'display_name' => 'Quận/Huyện trả hàng',
                'merge_name' => '${Quận/Huyện trả hàng}',
                'sort_order' => 12, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'name_of_province_arrival_id',
                'display_name' => 'Tỉnh/Thành phố trả hàng',
                'merge_name' => '${Tỉnh/Thành phố trả hàng}',
                'sort_order' => 12, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'amount',
                'display_name' => 'Cước phí',
                'merge_name' => '${Cước phí}',
                'sort_order' => 13, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'commission_amount',
                'display_name' => 'Phí hoa hồng',
                'merge_name' => '${Phí hoa hồng}',
                'sort_order' => 14, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'ETD_date',
                'display_name' => 'Ngày nhận hàng',
                'merge_name' => '${Ngày nhận hàng}',
                'sort_order' => 15, 'field_type' => null, 'data_type' => 'DATE', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'ETD_time',
                'display_name' => 'Giờ nhận hàng',
                'merge_name' => '${Giờ nhận hàng}',
                'sort_order' => 16, 'field_type' => null, 'data_type' => 'TIME', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'ETA_date',
                'display_name' => 'Ngày trả hàng',
                'merge_name' => '${Ngày trả hàng}',
                'sort_order' => 17, 'field_type' => null, 'data_type' => 'DATE', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'ETA_time',
                'display_name' => 'Giờ trả hàng',
                'merge_name' => '${Giờ trả hàng}',
                'sort_order' => 18, 'field_type' => null, 'data_type' => 'TIME', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'vehicle_group_id',
                'display_name' => 'Chủng loại xe',
                'merge_name' => '${Chủng loại xe}',
                'sort_order' => 19, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'vehicle_number',
                'display_name' => 'Số lượng xe',
                'merge_name' => '${Số lượng xe}',
                'sort_order' => 20, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'quantity',
                'display_name' => 'Tổng số lượng',
                'merge_name' => '${Tổng số lượng}',
                'sort_order' => 21, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'weight',
                'display_name' => 'Tổng khối lượng',
                'merge_name' => '${Tổng khối lượng}',
                'sort_order' => 21, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'volume',
                'display_name' => 'Tổng dung tích',
                'merge_name' => '${Tổng dung tích}',
                'sort_order' => 22, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'distance',
                'display_name' => 'Quãng đường',
                'merge_name' => '${Quãng đường}',
                'sort_order' => 23, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'payment_type_title',
                'display_name' => 'Hình thức thanh toán',
                'merge_name' => '${Hình thức thanh toán}',
                'sort_order' => 24, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'pu_username',
                'display_name' => 'Người chịu trách nhiệm thanh toán',
                'merge_name' => '${Người chịu trách nhiệm thanh toán}',
                'sort_order' => 25, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'vat_title',
                'display_name' => 'VAT',
                'merge_name' => '${VAT}',
                'sort_order' => 26, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'goods_amount',
                'display_name' => 'Giá trị hàng hóa',
                'merge_name' => '${Giá trị hàng hóa}',
                'sort_order' => 27, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 8, 'del_flag' => 0
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'anonymous_amount',
                'display_name' => 'Cước gửi',
                'merge_name' => '${Cước gửi}',
                'sort_order' => 28, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 8, 'del_flag' => 0
            ]
        ];

        $supportCarTransportation = env('SUPPORT_CAR_TRANSPORTATION', false);
        if ($supportCarTransportation) {
            array_push(
                $items,
                [
                    'table_name' => 'order_customer',
                    'column_name' => 'vin_nos',
                    'display_name' => 'Số khung',
                    'merge_name' => '${Số khung}',
                    'sort_order' => 28, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
                ],
                [
                    'table_name' => 'order_customer',
                    'column_name' => 'model_nos',
                    'display_name' => 'Số model',
                    'merge_name' => '${Số model}',
                    'sort_order' => 29, 'field_type' => null, 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0
                ]
            );
        }


        array_push(
            $items,
            [
                'table_name' => 'order_customer',
                'column_name' => 'list_order.order_code',
                'display_name' => 'Thông tin đơn hàng.Mã đơn hàng',
                'merge_name' => '${Thông tin đơn hàng.Mã đơn hàng}',
                'sort_order' => 30, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'list_order.reg_no',
                'display_name' => 'Thông tin đơn hàng.Xe',
                'merge_name' => '${Thông tin đơn hàng.Xe}',
                'sort_order' => 30, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'list_order.vehicle_group',
                'display_name' => 'Thông tin đơn hàng.Chủng loại xe',
                'merge_name' => '${Thông tin đơn hàng.Chủng loại xe}',
                'sort_order' => 30, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'list_order.driver_name',
                'display_name' => 'Thông tin đơn hàng.Tài xế',
                'merge_name' => '${Thông tin đơn hàng.Tài xế}',
                'sort_order' => 30, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'list_goods.name',
                'display_name' => 'Thông tin hàng hoá.Tên hàng hoá',
                'merge_name' => '${Thông tin hàng hoá.Tên hàng hoá}',
                'sort_order' => 30, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 8, 'del_flag' => 0, 'allow_dynamic_column' => 1, 'dynamic_column_header' => 1
            ],
            [
                'table_name' => 'order_customer',
                'column_name' => 'list_goods.quantity',
                'display_name' => 'Thông tin hàng hoá.Số lượng',
                'merge_name' => '${Thông tin hàng hoá.Số lượng}',
                'sort_order' => 31, 'field_type' => 'ARRAY', 'data_type' => 'CURRENCY', 'type' => 8, 'del_flag' => 0, 'allow_dynamic_column' => 1
            ]
        );

        foreach ($items as $item) {
            TemplateLayout::firstOrCreate($item);
        }
    }

    private function createCustomer()
    {
        $items = [
            [
                'table_name' => 'customer',
                'column_name' => 'sysdate',
                'display_name' => 'Ngày giờ xuất tệp tin',
                'merge_name' => '${Ngày giờ xuất tệp tin}',
                'sort_order' => 0, 'field_type' => 'SYSDATE', 'data_type' => 'DATETIME', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'index',
                'display_name' => 'Số thứ tự',
                'merge_name' => '${Số thứ tự}',
                'sort_order' => 1, 'field_type' => 'INDEX', 'data_type' => 'NUMBER', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'customer_code',
                'display_name' => 'Mã khách hàng',
                'merge_name' => '${Mã khách hàng}',
                'sort_order' => 1, 'field_type' => null, 'data_type' => 'STRING', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'customer_type',
                'display_name' => 'Loại khách hàng',
                'merge_name' => '${Loại khách hàng}',
                'sort_order' => 2, 'field_type' => null, 'data_type' => 'STRING', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'full_name',
                'display_name' => 'Tên khách hàng',
                'merge_name' => '${Tên khách hàng}',
                'sort_order' => 3, 'field_type' => null, 'data_type' => 'STRING', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'account_name',
                'display_name' => 'Tên tài khoản',
                'merge_name' => '${Tên tài khoản}',
                'sort_order' => 4, 'field_type' => null, 'data_type' => 'STRING', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'email',
                'display_name' => 'Email',
                'merge_name' => '${Email}',
                'sort_order' => 5, 'field_type' => null, 'data_type' => 'STRING', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'mobile_no',
                'display_name' => 'Số điện thoại',
                'merge_name' => '${Số điện thoại}',
                'sort_order' => 6, 'field_type' => null, 'data_type' => 'STRING', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'sex_type',
                'display_name' => 'Giới tính',
                'merge_name' => '${Giới tính}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'birth_date',
                'display_name' => 'Ngày sinh',
                'merge_name' => '${Ngày sinh}',
                'sort_order' => 8, 'field_type' => null, 'data_type' => 'DATE', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'delegate',
                'display_name' => 'Người đại diện',
                'merge_name' => '${Người đại diện}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'STRING', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'tax_code',
                'display_name' => 'Mã số thuế',
                'merge_name' => '${Mã số thuế}',
                'sort_order' => 10, 'field_type' => null, 'data_type' => 'STRING', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'note',
                'display_name' => 'Ghi chú',
                'merge_name' => '${Ghi chú}',
                'sort_order' => 11, 'field_type' => null, 'data_type' => 'STRING', 'type' => 5, 'del_flag' => 0
            ],
            [
                'table_name' => 'customer',
                'column_name' => 'current_address',
                'display_name' => 'Địa chỉ',
                'merge_name' => '${Địa chỉ}',
                'sort_order' => 12, 'field_type' => null, 'data_type' => 'STRING', 'type' => 5, 'del_flag' => 0
            ]
        ];

        foreach ($items as $item) {
            TemplateLayout::firstOrCreate($item);
        }
    }

    private function createVehicle()
    {
        $items = [
            // Xe
            [
                'table_name' => 'vehicles',
                'column_name' => 'sysdate',
                'display_name' => 'Ngày giờ xuất tệp tin',
                'merge_name' => '${Ngày giờ xuất tệp tin}',
                'sort_order' => 0, 'field_type' => 'SYSDATE', 'data_type' => 'DATETIME', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'index',
                'display_name' => 'Số thứ tự',
                'merge_name' => '${Số thứ tự}',
                'sort_order' => 1, 'field_type' => 'INDEX', 'data_type' => 'NUMBER', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'reg_no',
                'display_name' => 'Biển số',
                'merge_name' => '${Biển số}',
                'sort_order' => 2, 'field_type' => null, 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'group',
                'display_name' => 'Chủng loại xe',
                'merge_name' => '${Chủng loại xe}',
                'sort_order' => 4, 'field_type' => null, 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'gps_company',
                'display_name' => 'Công ty GPS',
                'merge_name' => '${Công ty GPS}',
                'sort_order' => 5, 'field_type' => null, 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'drivers',
                'display_name' => 'Danh sách tài xế',
                'merge_name' => '${Danh sách tài xế}',
                'sort_order' => 6, 'field_type' => null, 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'vehicle_status',
                'display_name' => 'Trạng thái',
                'merge_name' => '${Trạng thái}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'vehicle_type',
                'display_name' => 'Loại xe',
                'merge_name' => '${Loại xe}',
                'sort_order' => 8, 'field_type' => null, 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'active_status',
                'display_name' => 'Tình trạng',
                'merge_name' => '${Tình trạng}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'length',
                'display_name' => 'Kích thước bao (dài)',
                'merge_name' => '${Kích thước bao (dài)}',
                'sort_order' => 10, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'width',
                'display_name' => 'Kích thước bao (rộng)',
                'merge_name' => '${Kích thước bao (rộng)}',
                'sort_order' => 11, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'height',
                'display_name' => 'Kích thước bao (cao)',
                'merge_name' => '${Kích thước bao (cao)}',
                'sort_order' => 12, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'volume',
                'display_name' => 'Dung tích',
                'merge_name' => '${Dung tích}',
                'sort_order' => 13, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'weight',
                'display_name' => 'Tải trọng',
                'merge_name' => '${Tải trọng}',
                'sort_order' => 14, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'repair_distance',
                'display_name' => 'Số km bảo dưỡng',
                'merge_name' => '${Số km bảo dưỡng}',
                'sort_order' => 15, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'repair_date',
                'display_name' => 'Ngày bảo dưỡng gần nhất',
                'merge_name' => '${Ngày bảo dưỡng gần nhất}',
                'sort_order' => 16, 'field_type' => null, 'data_type' => 'DATE', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'max_fuel',
                'display_name' => 'Định mức tiêu thụ nhiên liệu không hàng',
                'merge_name' => '${Định mức tiêu thụ nhiên liệu không hàng}',
                'sort_order' => 17, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'max_fuel_with_goods',
                'display_name' => 'Định mức tiêu thụ nhiên liệu có hàng',
                'merge_name' => '${Định mức tiêu thụ nhiên liệu có hàng}',
                'sort_order' => 18, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'category_of_barrel',
                'display_name' => 'Chủng loại thùng',
                'merge_name' => '${Chủng loại thùng}',
                'sort_order' => 19, 'field_type' => null, 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'weight_lifting_system',
                'display_name' => 'Hệ thống nâng hạ',
                'merge_name' => '${Hệ thống nâng hạ}',
                'sort_order' => 20, 'field_type' => null, 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'register_year',
                'display_name' => 'Năm sản xuất',
                'merge_name' => '${Năm sản xuất}',
                'sort_order' => 21, 'field_type' => null, 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'brand',
                'display_name' => 'Nhãn hiệu',
                'merge_name' => '${Nhãn hiệu}',
                'sort_order' => 22, 'field_type' => null, 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'distance_by_gps',
                'display_name' => 'Số km theo GPS',
                'merge_name' => '${Số km theo GPS}',
                'sort_order' => 23, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'fuel_by_gps',
                'display_name' => 'Nhiên liệu theo GPS',
                'merge_name' => '${Nhiên liệu theo GPS}',
                'sort_order' => 24, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'list_costs.payment',
                'display_name' => 'Thông tin chi phí.Diễn giải',
                'merge_name' => '${Thông tin chi phí.Diễn giải}',
                'sort_order' => 25, 'field_type' => "ARRAY", 'data_type' => 'STRING', 'type' => 7, 'del_flag' => 0, 'allow_dynamic_column' => 1, 'dynamic_column_header' => 1
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'list_costs.amount',
                'display_name' => 'Thông tin chi phí.Chi phí',
                'merge_name' => '${Thông tin chi phí.Chi phí}',
                'sort_order' => 26, 'field_type' => "ARRAY", 'data_type' => 'CURRENCY', 'type' => 7, 'del_flag' => 0, 'allow_dynamic_column' => 1,
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'total_payment',
                'display_name' => 'Tổng chi phí',
                'merge_name' => '${Tổng chi phí}',
                'sort_order' => 27, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'total_revenue',
                'display_name' => 'Tổng doanh thu',
                'merge_name' => '${Tổng doanh thu}',
                'sort_order' => 28, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'total_com_amount',
                'display_name' => 'Tổng tiền COM',
                'merge_name' => '${Tổng tiền COM}',
                'sort_order' => 29, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 7, 'del_flag' => 0
            ],
            [
                'table_name' => 'vehicles',
                'column_name' => 'total_profit',
                'display_name' => 'Lợi nhuận',
                'merge_name' => '${Lợi nhuận}',
                'sort_order' => 30, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 7, 'del_flag' => 0
            ]
        ];

        foreach ($items as $item) {
            TemplateLayout::firstOrCreate($item);
        }
    }

    private function createDriver()
    {
        $items = [
            [
                'table_name' => 'driver',
                'column_name' => 'sysdate',
                'display_name' => 'Ngày giờ xuất tệp tin',
                'merge_name' => '${Ngày giờ xuất tệp tin}',
                'sort_order' => 0, 'field_type' => 'SYSDATE', 'data_type' => 'DATETIME', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'index',
                'display_name' => 'Số thứ tự',
                'merge_name' => '${Số thứ tự}',
                'sort_order' => 0, 'field_type' => 'INDEX', 'data_type' => 'NUMBER', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'code',
                'display_name' => 'Mã tài xế',
                'merge_name' => '${Mã tài xế}',
                'sort_order' => 1, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'full_name',
                'display_name' => 'Tên tài xế',
                'merge_name' => '${Tên tài xế}',
                'sort_order' => 2, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'account_name',
                'display_name' => 'Tên tài khoản',
                'merge_name' => '${Tên tài khoản}',
                'sort_order' => 3, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'email',
                'display_name' => 'Email',
                'merge_name' => '${Email}',
                'sort_order' => 4, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'mobile_no',
                'display_name' => 'Số điện thoại',
                'merge_name' => '${Số điện thoại}',
                'sort_order' => 5, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'identity_no',
                'display_name' => 'CMND',
                'merge_name' => '${CMND}',
                'sort_order' => 6, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'sex_type',
                'display_name' => 'Giới tính',
                'merge_name' => '${Giới tính}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'birth_date',
                'display_name' => 'Ngày sinh',
                'merge_name' => '${Ngày sinh}',
                'sort_order' => 8, 'field_type' => null, 'data_type' => 'DATE', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'driver_license',
                'display_name' => 'Bằng lái',
                'merge_name' => '${Bằng lái}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'DATE', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'hometown',
                'display_name' => 'Quê quán',
                'merge_name' => '${Quê quán}',
                'sort_order' => 10, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'current_address',
                'display_name' => 'Nơi ở hiện tại',
                'merge_name' => '${Nơi ở hiện tại}',
                'sort_order' => 11, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'rank',
                'display_name' => 'Xếp hạng lái xe',
                'merge_name' => '${Xếp hạng lái xe}',
                'sort_order' => 12, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'experience_drive',
                'display_name' => 'Thâm niên lái xe',
                'merge_name' => '${Thâm niên lái xe}',
                'sort_order' => 13, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'experience_work',
                'display_name' => 'Thâm niên làm việc',
                'merge_name' => '${Thâm niên làm việc}',
                'sort_order' => 14, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'work_date',
                'display_name' => 'Ngày vào công ty',
                'merge_name' => '${Ngày vào công ty}',
                'sort_order' => 15, 'field_type' => null, 'data_type' => 'DATE', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'work_description',
                'display_name' => 'Mô tả quá trình công tác',
                'merge_name' => '${Mô tả quá trình công tác}',
                'sort_order' => 16, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'note',
                'display_name' => 'Ghi chú',
                'merge_name' => '${Ghi chú}',
                'sort_order' => 17, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'evaluate',
                'display_name' => 'Đánh giá của điều hành trực tiếp',
                'merge_name' => '${Đánh giá của điều hành trực tiếp}',
                'sort_order' => 18, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'list_vehicles',
                'display_name' => 'Danh sách xe',
                'merge_name' => '${Danh sách xe}',
                'sort_order' => 19, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'list_vehicle_teams',
                'display_name' => 'Danh sách đội tài xế',
                'merge_name' => '${Danh sách đội tài xế}',
                'sort_order' => 20, 'field_type' => null, 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'list_costs.payment',
                'display_name' => 'Thông tin chi phí.Diễn giải',
                'merge_name' => '${Thông tin chi phí.Diễn giải}',
                'sort_order' => 21, 'field_type' => "ARRAY", 'data_type' => 'STRING', 'type' => 6, 'del_flag' => 0, 'allow_dynamic_column' => 1, 'dynamic_column_header' => 1
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'list_costs.amount',
                'display_name' => 'Thông tin chi phí.Chi phí',
                'merge_name' => '${Thông tin chi phí.Chi phí}',
                'sort_order' => 22, 'field_type' => "ARRAY", 'data_type' => 'CURRENCY', 'type' => 6, 'del_flag' => 0, 'allow_dynamic_column' => 1,
            ],
            [
                'table_name' => 'driver',
                'column_name' => 'total_cost',
                'display_name' => 'Tổng chi phí',
                'merge_name' => '${Tổng chi phí}',
                'sort_order' => 23, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 6, 'del_flag' => 0
            ]
        ];

        foreach ($items as $item) {
            TemplateLayout::firstOrCreate($item);
        }
    }

    private function createQuota()
    {
        $items = [
            // Bảng định mức chi phí
            [
                'table_name' => 'quota',
                'column_name' => 'index',
                'display_name' => 'Số thứ tự',
                'merge_name' => '${Số thứ tự}',
                'sort_order' => 1, 'field_type' => 'INDEX', 'data_type' => 'NUMBER', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'sysdate',
                'display_name' => 'Ngày giờ xuất tệp tin',
                'merge_name' => '${Ngày giờ xuất tệp tin}',
                'sort_order' => 0, 'field_type' => 'SYSDATE', 'data_type' => 'DATETIME', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'quota_code',
                'display_name' => 'Mã định mức',
                'merge_name' => '${Mã định mức}',
                'sort_order' => 1, 'field_type' => null, 'data_type' => 'STRING', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'name',
                'display_name' => 'Tên',
                'merge_name' => '${Tên}',
                'sort_order' => 2, 'field_type' => null, 'data_type' => 'STRING', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'vehicle_group',
                'display_name' => 'Chủng loại xe',
                'merge_name' => '${Chủng loại xe}',
                'sort_order' => 3, 'field_type' => null, 'data_type' => 'STRING', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'distance',
                'display_name' => 'Khoảng cách',
                'merge_name' => '${Khoảng cách}',
                'sort_order' => 4, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'location_destination',
                'display_name' => 'Điểm nhận hàng',
                'merge_name' => '${Điểm nhận hàng}',
                'sort_order' => 5, 'field_type' => null, 'data_type' => 'STRING', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'location_arrival',
                'display_name' => 'Điểm trả hàng',
                'merge_name' => '${Điểm trả hàng}',
                'sort_order' => 6, 'field_type' => null, 'data_type' => 'STRING', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'total_cost',
                'display_name' => 'Tổng chi phí',
                'merge_name' => '${Tổng chi phí}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'list_costs.index',
                'display_name' => 'Thông tin chi phí.Số thứ tự',
                'merge_name' => '${Thông tin chi phí.Số thứ tự}',
                'sort_order' => 8, 'field_type' => 'ARRAY.INDEX', 'data_type' => 'NUMBER', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'list_locations.index',
                'display_name' => 'Thông tin lộ trình.Số thứ tự',
                'merge_name' => '${Thông tin lộ trình.Số thứ tự}',
                'sort_order' => 10, 'field_type' => 'ARRAY.INDEX', 'data_type' => 'NUMBER', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'list_locations.loaction_title',
                'display_name' => 'Thông tin lộ trình.Điạ điểm',
                'merge_name' => '${Thông tin lộ trình.Địa điểm}',
                'sort_order' => 9, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'list_costs.index',
                'display_name' => 'Thông tin chi phí.Số thứ tự',
                'merge_name' => '${Thông tin chi phí.Số thứ tự}',
                'sort_order' => 10, 'field_type' => 'ARRAY.INDEX', 'data_type' => 'NUMBER', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'list_costs.receipt_payment_name',
                'display_name' => 'Thông tin chi phí.Tên chi phí',
                'merge_name' => '${Thông tin chi phí.Tên chi phí}',
                'sort_order' => 10, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 4, 'del_flag' => 0
            ],
            [
                'table_name' => 'quota',
                'column_name' => 'list_costs.amount',
                'display_name' => 'Thông tin chi phí.Số tiền',
                'merge_name' => '${Thông tin chi phí.Số tiền}',
                'sort_order' => 11, 'field_type' => 'ARRAY', 'data_type' => 'CURRENCY', 'type' => 4, 'del_flag' => 0
            ]
        ];

        foreach ($items as $item) {
            TemplateLayout::firstOrCreate($item);
        }
    }

    private function createDocument()
    {
        // Chứng từ
        $items = [
            [
                'table_name' => 'orders',
                'column_name' => 'index',
                'display_name' => 'Số thứ tự',
                'merge_name' => '${Số thứ tự}',
                'sort_order' => 0, 'field_type' => 'INDEX', 'data_type' => 'NUMBER', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'sysdate',
                'display_name' => 'Ngày giờ xuất tệp tin',
                'merge_name' => '${Ngày giờ xuất tệp tin}',
                'sort_order' => 0, 'field_type' => 'SYSDATE', 'data_type' => 'DATETIME', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'order_code',
                'display_name' => 'Mã hệ thống',
                'merge_name' => '${Mã hệ thống}',
                'sort_order' => 1, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'order_no',
                'display_name' => 'Số đơn hàng',
                'merge_name' => '${Số đơn hàng}',
                'sort_order' => 2, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'bill_no',
                'display_name' => 'Số hóa đơn',
                'merge_name' => '${Số hóa đơn}',
                'sort_order' => 3, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'status',
                'display_name' => 'Trạng thái',
                'merge_name' => '${Trạng thái}',
                'sort_order' => 3, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'customer_full_name',
                'display_name' => 'Tên khách hàng',
                'merge_name' => '${Tên khách hàng}',
                'sort_order' => 4, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'customer_name',
                'display_name' => 'Tên liên hệ',
                'merge_name' => '${Tên liên hệ}',
                'sort_order' => 5, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'customer_mobile_no',
                'display_name' => 'Số điện thoại liên hệ',
                'merge_name' => '${Số điện thoại liên hệ}',
                'sort_order' => 5, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'reg_no',
                'display_name' => 'Thông tin xe',
                'merge_name' => '${Thông tin xe}',
                'sort_order' => 6, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'full_name',
                'display_name' => 'Thông tin tài xế',
                'merge_name' => '${Thông tin tài xế}',
                'sort_order' => 7, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'is_collected_documents',
                'display_name' => 'Bắt buộc thu chứng từ',
                'merge_name' => '${Bắt buộc thu chứng từ}',
                'sort_order' => 8, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'status_collected_documents',
                'display_name' => 'Tình trạng thu chứng từ',
                'merge_name' => '${Tình trạng thu chứng từ}',
                'sort_order' => 9, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],

            [
                'table_name' => 'orders',
                'column_name' => 'date_collected_documents',
                'display_name' => 'Ngày hạn thu chứng từ',
                'merge_name' => '${Ngày hạn thu chứng từ}',
                'sort_order' => 10, 'field_type' => null, 'data_type' => 'DATE', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'time_collected_documents',
                'display_name' => 'Giờ hạn thu chứng từ',
                'merge_name' => '${Giờ hạn thu chứng từ}',
                'sort_order' => 11, 'field_type' => null, 'data_type' => 'TIME', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'date_collected_documents_reality',
                'display_name' => 'Ngày thu chứng từ thực tế',
                'merge_name' => '${Ngày thu chứng từ thực tế}',
                'sort_order' => 10, 'field_type' => null, 'data_type' => 'DATE', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'time_collected_documents_reality',
                'display_name' => 'Giờ thu chứng từ thực tế',
                'merge_name' => '${Giờ thu chứng từ thực tế}',
                'sort_order' => 11, 'field_type' => null, 'data_type' => 'TIME', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'num_of_document_page',
                'display_name' => 'Số tờ',
                'merge_name' => '${Số tờ}',
                'sort_order' => 11, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'document_type',
                'display_name' => 'Loại chứng từ',
                'merge_name' => '${Loại chứng từ}',
                'sort_order' => 12, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'document_note',
                'display_name' => 'Ghi chú chứng từ',
                'merge_name' => '${Ghi chú chứng từ}',
                'sort_order' => 12, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'order_date',
                'display_name' => 'Ngày đặt hàng',
                'merge_name' => '${Ngày đặt hàng}',
                'sort_order' => 13, 'field_type' => null, 'data_type' => 'DATE', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'note',
                'display_name' => 'Chú thích',
                'merge_name' => '${Chú thích}',
                'sort_order' => 14, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'description',
                'display_name' => 'Diễn giải',
                'merge_name' => '${Diễn giải}',
                'sort_order' => 15, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'precedence',
                'display_name' => 'Độ ưu tiên',
                'merge_name' => '${Độ ưu tiên}',
                'sort_order' => 16, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'commission_amount',
                'display_name' => 'Phí hoa hồng',
                'merge_name' => '${Phí hoa hồng}',
                'sort_order' => 17, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'commission_value',
                'display_name' => 'Tỉ lệ hoa hồng',
                'merge_name' => '${Tỉ lệ hoa hồng}',
                'sort_order' => 18, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'amount',
                'display_name' => 'Cước phí vận chuyển',
                'merge_name' => '${Cước phí vận chuyển}',
                'sort_order' => 19, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_name_destination',
                'display_name' => 'Tên liên hệ nhận hàng',
                'merge_name' => '${Tên liên hệ nhận hàng}',
                'sort_order' => 20, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_mobile_no_destination',
                'display_name' => 'Điện thoại liên hệ nhận hàngg',
                'merge_name' => '${Điện thoại liên hệ nhận hàng}',
                'sort_order' => 21, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_email_destination',
                'display_name' => 'Email nhận hàng',
                'merge_name' => '${Email nhận hàng}',
                'sort_order' => 22, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'informative_destination',
                'display_name' => 'Thông tin bổ sung nhận hàng',
                'merge_name' => '${Thông tin bổ sung nhận hàng}',
                'sort_order' => 23, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_name_arrival',
                'display_name' => 'Tên liên hệ trả hàng',
                'merge_name' => '${Tên liên hệ trả hàng}',
                'sort_order' => 23, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_mobile_no_arrival',
                'display_name' => 'Điện thoại liên hệ trả hàng',
                'merge_name' => '${Điện thoại liên hệ trả hàng}',
                'sort_order' => 24, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'contact_email_arrival',
                'display_name' => 'Email trả hàng',
                'merge_name' => '${Email trả hàng}',
                'sort_order' => 25, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'informative_arrival',
                'display_name' => 'Thông tin bổ sung trả hàng',
                'merge_name' => '${Thông tin bổ sung trả hàng}',
                'sort_order' => 25, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'volume',
                'display_name' => 'Tổng thể tích',
                'merge_name' => '${Tổng thể tích}',
                'sort_order' => 26, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'weight',
                'display_name' => 'Tổng khối lượng',
                'merge_name' => '${Tổng khối lượng}',
                'sort_order' => 27, 'field_type' => null, 'data_type' => 'NUMBER', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'final_amount',
                'display_name' => 'Doanh thu',
                'merge_name' => '${Doanh thu}',
                'sort_order' => 28, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'loading_destination_fee',
                'display_name' => 'Phí bốc xếp hàng hóa nhận hàng',
                'merge_name' => '${Phí bốc xếp hàng hóa nhận hàng}',
                'sort_order' => 29, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'loading_arrival_fee',
                'display_name' => 'Phí bốc xếp hàng hoá trả hàng',
                'merge_name' => '${Phí bốc xếp hàng hoá trả hàng}',
                'sort_order' => 30, 'field_type' => null, 'data_type' => 'CURRENCY', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'good_details',
                'display_name' => 'Mô tả hàng hóa',
                'merge_name' => '${Mô tả hàng hóa}',
                'sort_order' => 31, 'field_type' => null, 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'ETD_time_reality',
                'display_name' => 'Giờ nhận hàng thực tế',
                'merge_name' => '${Giờ nhận hàng thực tế}',
                'sort_order' => 32, 'field_type' => null, 'data_type' => 'TIME', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'ETD_date_reality',
                'display_name' => 'Ngày nhận hàng thực tế',
                'merge_name' => '${Ngày nhận hàng thực tế}',
                'sort_order' => 32, 'field_type' => null, 'data_type' => 'DATE', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'ETA_time_reality',
                'display_name' => 'Giờ trả hàng thực tế',
                'merge_name' => '${Giờ trả hàng thực tế}',
                'sort_order' => 32, 'field_type' => null, 'data_type' => 'TIME', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'ETA_date_reality',
                'display_name' => 'Ngày trả hàng thực tế',
                'merge_name' => '${Ngày trả hàng thực tế}',
                'sort_order' => 32, 'field_type' => null, 'data_type' => 'DATE', 'type' => 2, 'del_flag' => 0
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.title',
                'display_name' => 'Địa điểm.Địa điểm nhận hàng',
                'merge_name' => '${Địa điểm.Địa điểm nhận hàng}',
                'sort_order' => 32, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.date',
                'display_name' => 'Địa điểm.Ngày nhận hàng',
                'merge_name' => '${Địa điểm.Ngày nhận hàng}',
                'sort_order' => 33, 'field_type' => 'ARRAY', 'data_type' => 'DATE', 'type' => 2, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.time',
                'display_name' => 'Địa điểm.Giờ nhận hàng',
                'merge_name' => '${Địa điểm.Giờ nhận hàng}',
                'sort_order' => 34, 'field_type' => 'ARRAY', 'data_type' => 'TIME', 'type' => 2, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.date_reality',
                'display_name' => 'Địa điểm.Ngày nhận hàng thực tế',
                'merge_name' => '${Địa điểm.Ngày nhận hàng thực tế}',
                'sort_order' => 35, 'field_type' => 'ARRAY', 'data_type' => 'DATE', 'type' => 2, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listDestinationLocations.time_reality',
                'display_name' => 'Địa điểm.Giờ nhận hàng thực tế',
                'merge_name' => '${Địa điểm.Giờ nhận hàng thực tế}',
                'sort_order' => 36, 'field_type' => 'ARRAY', 'data_type' => 'TIME', 'type' => 2, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.title',
                'display_name' => 'Địa điểm.Địa điểm trả hàng',
                'merge_name' => '${Địa điểm.Địa điểm trả hàng}',
                'sort_order' => 37, 'field_type' => 'ARRAY', 'data_type' => 'STRING', 'type' => 2, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.date',
                'display_name' => 'Địa điểm.Ngày trả hàng',
                'merge_name' => '${Địa điểm.Ngày trả hàng}',
                'sort_order' => 38, 'field_type' => 'ARRAY', 'data_type' => 'DATE', 'type' => 2, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.time',
                'display_name' => 'Địa điểm.Giờ trả hàng',
                'merge_name' => '${Địa điểm.Giờ trả hàng}',
                'sort_order' => 39, 'field_type' => 'ARRAY', 'data_type' => 'TIME', 'type' => 2, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.date_reality',
                'display_name' => 'Địa điểm.Ngày trả hàng thực tế',
                'merge_name' => '${Địa điểm.Ngày trả hàng thực tế}',
                'sort_order' => 40, 'field_type' => 'ARRAY', 'data_type' => 'DATE', 'type' => 2, 'del_flag' => 0, 'allow_group' => 1
            ],
            [
                'table_name' => 'orders',
                'column_name' => 'listArrivalLocations.time_reality',
                'display_name' => 'Địa điểm.Giờ trả hàng thực tế',
                'merge_name' => '${Địa điểm.Giờ trả hàng thực tế}',
                'sort_order' => 41, 'field_type' => 'ARRAY', 'data_type' => 'TIME', 'type' => 2, 'del_flag' => 0, 'allow_group' => 1
            ]
        ];

        foreach ($items as $item) {
            TemplateLayout::firstOrCreate($item);
        }
    }
}
