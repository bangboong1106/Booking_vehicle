<?php

use App\Model\Entities\ExcelColumnMappingConfig;
use App\Model\Entities\ExcelColumnConfig;
use Illuminate\Database\Seeder;

class ExcelColumnConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ExcelColumnConfig::truncate();
        ExcelColumnMappingConfig::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->order();
        $this->route();
        $this->orderCustomer();
        $this->repairTicket();
    }

    public function order()
    {
        $data = [
            'model' => 'order',
            'is_system' => 1,
            'user_id' => null,
            'header_index' => 10,
            'max_row' => 500,
        ];
        $excelColumnConfig = ExcelColumnConfig::firstOrCreate($data);

        $items = [
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Mã hệ thống',
                'field' => 'order_code',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin đơn hàng',
                'comment' => '- Mã hệ thống sử dụng để phân biệt các đơn hàng và các công việc quản lý liên quan.
                - Mã hệ thống là thông tin  bắt buộc nhập.
                - Mã hệ thống không được trùng nhau và trùng với các mã đã có trong hệ thống.',
                'is_key' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số đơn hàng (delivery note)',
                'field' => 'order_no',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin đơn hàng',
                'comment' => '- Thông tin về Số đơn hàng.
                - Thông tin này có thể bỏ trống nếu không có hoặc chưa có thông tin',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số hoá đơn (invoice number)',
                'field' => 'bill_no',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin đơn hàng',
                'comment' => '- Thông tin về Số hóa đơn của đơn hàng 
                - Thông tin có thể nhập hoặc bỏ trống nếu không có hoặc chưa có thông tin',
                'width' => 20
            ],
        ];
        $supportCarTransportation = env('SUPPORT_CAR_TRANSPORTATION', false);
        if ($supportCarTransportation) {
            array_push(
                $items,
                [
                    'excel_column_config_id' => $excelColumnConfig->id,
                    'column_name' => 'Số model',
                    'field' => 'model_no',
                    'default_value' => null,
                    'data_type' => 'string',
                    'function' => null,
                    'header_group' => 'Thông tin đơn hàng',
                    'comment' => '- Thông tin về Số model của xe 
                - Thông tin có thể nhập hoặc bỏ trống nếu không có hoặc chưa có thông tin',
                    'width' => 20
                ],
                [
                    'excel_column_config_id' => $excelColumnConfig->id,
                    'column_name' => 'Số khung',
                    'field' => 'vin_no',
                    'default_value' => null,
                    'data_type' => 'string',
                    'function' => null,
                    'header_group' => 'Thông tin đơn hàng',
                    'comment' => '- Thông tin về Số khung của xe
                - Thông tin có thể nhập hoặc bỏ trống nếu không có hoặc chưa có thông tin',
                    'width' => 20
                ]
            );
        }

        array_push(
            $items,
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Ngày đặt hàng',
                'field' => 'order_date',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'header_group' => 'Thông tin đơn hàng',
                'comment' => '- Thông tin ngày đặt hàng của đơn hàng. 
                - Thông tin có thể nhập hoặc không nhập.
                - Nhập ngày tháng theo định dạng: dd-mm-yyyy, hoặc dd/mm/yyyy.
                - Có thể điền hoặc không.
                Ví dụ: 09-01-2019 hoặc 20-11-2019',
                'collapse' => 1,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Mức độ ưu tiên',
                'field' => 'precedence',
                'data' => 'order_precedences',
                'default_value' => 'ORDER_PRECEDENCE_NORMAL',
                'data_type' => 'list',
                'function' => 'convertPrecedence',
                'header_group' => 'Thông tin đơn hàng',
                'comment' => '- Thông tin về mức độ ưu tiên của đơn hàng.
                - Nhập thông tin bằng cách: chọn giá trị tương ứng trong danh sách đã liệt kê khi click vào các ô trong cột.
                - Có thể điền hoặc không.',
                'collapse' => 1,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Trạng thái',
                'field' => 'status',
                'data' => 'order_status',
                'default_value' => 'KHOI_TAO',
                'data_type' => 'list',
                'function' => 'convertStatus',
                'header_group' => 'Thông tin đơn hàng',
                'comment' => '- Thông tin về trạng thái của đơn hàng.
                - Nhập vào trạng thái của đơn hàng bằng cách chọn giá trị tương ứng trong danh sách khi chọn vào các ô trong cột.',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Cước phí vận chuyển (VNĐ)',
                'field' => 'amount',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => null,
                'comment' => '- Thông tin về tổng cước phí vận chuyển của đơn hàng.
                - Yêu cầu: Chỉ nhập các ký tự số
                - Đơn vị tính mặc định: VNĐ (Việt Nam Đồng)
                - Nếu không nhập, số tiền mặc định bằng 0 (VNĐ)',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Khách hàng',
                'field' => 'customer_code',
                'original_field' => 'customer_id',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'header_group' => 'Thông tin khách hàng',
                'comment' => '- Nhập mã khách hàng bằng cách chọn giá trị tương ứng từ danh sách khi chọn vào ô tương ứng trong cột.
                - Sau khi chọn, dữ liệu cho Tên khách hàng và Số điện thoại sẽ được cập nhật tương ứng với Mã khách hàng đã chọn.',
                'entity' => 'customer',
                'code' => 'customer_code',
                'title' => 'full_name',
                'width' => 40
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tên khách hàng/người đại diện',
                'field' => 'customer_name',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin khách hàng',
                'comment' => '- Thông tin nếu không nhập sẽ tự động lấy thông tin từ khách hàng.
                - Cũng có thể sửa thông tin này nếu cần cập nhật, bổ sung',
                'collapse' => 1,
                'width' => 40
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số điện thoại khách hàng',
                'field' => 'customer_mobile_no',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin khách hàng',
                'comment' => '- Thông tin nếu không nhập sẽ tự động lấy thông tin từ khách hàng.
                - Cũng có thể sửa thông tin này nếu cần cập nhật, bổ sung',
                'collapse' => 1,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Địa điểm nhận hàng',
                'field' => 'location_destination_code',
                'original_field' => 'location_destination_id',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'header_group' => 'Thông tin nhận hàng',
                'comment' => '- Nhập thông tin bằng cách chọn địa điểm tương ứng trong danh sách hiển thị ra khi click vào ô tương ứng trong cột.
                - Đối với địa điểm không có trên hệ thống, nhập theo định dạng : Số nhà , Xã/phường , Quận/huyện , Tỉnh/thành phố.
                ',
                'is_multiple' => 1,
                'entity' => 'location',
                'code' => 'code',
                'title' => 'title',
                'mapping_data' => 'listLocationDestinations',
                'mapping_field' => 'location_id',
                'width' => 40

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Quận/Huyện điểm nhận hàng',
                'field' => 'name_of_district_destination_id',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin nhận hàng',
                'comment' => null,
                'width' => 20,
                'is_import' => 0
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tỉnh/Thành phố điểm nhận hàng',
                'field' => 'name_of_province_destination_id',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin nhận hàng',
                'comment' => null,
                'width' => 20,
                'is_import' => 0
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Giờ nhận hàng',
                'field' => 'ETD_time',
                'default_value' => null,
                'data_type' => 'time',
                'function' => null,
                'header_group' => 'Thông tin nhận hàng',
                'comment' => '- Thông tin về thời gian nhận hàng của đơn hàng.
                - Yêu cầu: Nhập thông tin theo định dạng giờ:phút (hh:mm). Ví dụ: 10:30 hoặc 15:00',
                'is_multiple' => 1,
                'mapping_data' => 'listLocationDestinations',
                'mapping_field' => 'time',
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Ngày nhận hàng',
                'field' => 'ETD_date',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'header_group' => 'Thông tin nhận hàng',
                'comment' => '- Thông tin về thời gian nhận hàng của đơn hàng
                - Nhập ngày tháng theo định dạng: ngày-tháng-năm (dd-mm-yyyy).
                Ví dụ: 09-01-2019 hoặc 20-11-2019.',
                'is_multiple' => 1,
                'mapping_data' => 'listLocationDestinations',
                'mapping_field' => 'date',
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Giờ nhận hàng thực tế',
                'field' => 'ETD_time_reality',
                'default_value' => null,
                'data_type' => 'time',
                'function' => null,
                'header_group' => 'Thông tin nhận hàng',
                'comment' => '- Thông tin về thời gian nhận hàng thực tế của đơn hàng.
                - Yêu cầu: Nhập thông tin theo định dạng giờ:phút (hh:mm). Ví dụ: 10:30 hoặc 15:00',
                'collapse' => 1,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Ngày nhận hàng thực tế',
                'field' => 'ETD_date_reality',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'header_group' => 'Thông tin nhận hàng',
                'comment' => '- Thông tin về thời gian nhận hàng thực tế của đơn hàng
                - Nhập ngày tháng theo định dạng: ngày-tháng-năm (dd-mm-yyyy).
                Ví dụ: 09-01-2019 hoặc 20-11-2019.',
                'collapse' => 1,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Thông tin bổ sung nhận hàng',
                'field' => 'informative_destination',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin nhận hàng',
                'comment' => '- Thông tin thêm về việc nhận hàng',
                'collapse' => 1,
                'width' => 40

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tên liên hệ nhận hàng',
                'field' => 'contact_name_destination',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin nhận hàng',
                'comment' => '- Thông tin về tên người liên hệ khi nhận hàng',
                'collapse' => 1,
                'width' => 40
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số điện thoại liên hệ nhận hàng',
                'field' => 'contact_mobile_no_destination',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin nhận hàng',
                'comment' => '- Thông tin về số điện thoại của người liên hệ khi nhận hàng. Hoặc số điện thoại có thể liên hệ được khi nhận hàng',
                'collapse' => 1,
                'width' => 20


            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Email liên hệ nhận hàng',
                'field' => 'contact_email_destination',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin nhận hàng',
                'comment' => '- Thông tin về địa chỉ email của người liên hệ nhận hàng
                - Thông tin không bắt buộc, có thể nhập hoặc bỏ qua.',
                'collapse' => 1,
                'width' => 30
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Phí bốc xếp hàng hoá (Nhận hàng)(VND)',
                'field' => 'loading_destination_fee',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin nhận hàng',
                'comment' => '- Thông tin về phí bốc xếp hàng hóa khi nhận hàng (nếu có).
                - Yêu cầu: Chỉ nhập các ký tự số
                - Đơn vị tính mặc định: VNĐ (Việt Nam Đồng)
                - Nếu không nhập, mặc định không có phí bốc xếp và số tiền mặc định bằng 0 (VNĐ)',
                'collapse' => 1,
                'width' => 30
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Địa điểm trả hàng',
                'field' => 'location_arrival_code',
                'original_field' => 'location_arrival_id',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'header_group' => 'Thông tin trả hàng',
                'comment' => '- Nhập thông tin bằng cách chọn địa điểm tương ứng trong danh sách hiển thị ra khi click vào ô tương ứng trong cột.
                - Đối với địa điểm không có trên hệ thống, nhập theo định dạng : Số nhà , Xã/phường , Quận/huyện , Tỉnh/thành phố.',
                'is_multiple' => 1,
                'entity' => 'location',
                'code' => 'code',
                'title' => 'title',
                'mapping_data' => 'listLocationArrivals',
                'mapping_field' => 'location_id',
                'width' => 40
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Quận/Huyện điểm trả hàng',
                'field' => 'name_of_district_arrival_id',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin trả hàng',
                'comment' => null,
                'width' => 20,
                'is_import' => 0
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tỉnh/Thành phố điểm trả hàng',
                'field' => 'name_of_province_arrival_id',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin trả hàng',
                'comment' => null,
                'width' => 20,
                'is_import' => 0
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Giờ trả hàng',
                'field' => 'ETA_time',
                'default_value' => null,
                'data_type' => 'time',
                'function' => null,
                'header_group' => 'Thông tin trả hàng',
                'comment' => '- Thông tin về thời gian trả hàng của đơn hàng.
                - Yêu cầu: Nhập thông tin theo định dạng giờ:phút (hh:mm). Ví dụ: 10:30 hoặc 15:00',
                'is_multiple' => 1,
                'mapping_data' => 'listLocationArrivals',
                'mapping_field' => 'time',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Ngày trả hàng',
                'field' => 'ETA_date',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'header_group' => 'Thông tin trả hàng',
                'comment' => '- Thông tin về thời gian trả hàng của đơn hàng
                - Nhập ngày tháng theo định dạng: ngày-tháng-năm (dd-mm-yyyy).
                Ví dụ: 09-01-2019 hoặc 20-11-2019.',
                'is_multiple' => 1,
                'mapping_data' => 'listLocationArrivals',
                'mapping_field' => 'date',
                'width' => 20


            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Giờ trả hàng thực tế',
                'field' => 'ETA_time_reality',
                'default_value' => null,
                'data_type' => 'time',
                'function' => null,
                'header_group' => 'Thông tin trả hàng',
                'comment' => '- Thông tin về thời gian trả hàng thực tế của đơn hàng.
                - Yêu cầu: Nhập thông tin theo định dạng giờ:phút (hh:mm). Ví dụ: 10:30 hoặc 15:00
                ',
                'collapse' => 1,
                'width' => 20


            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Ngày trả hàng thực tế',
                'field' => 'ETA_date_reality',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'header_group' => 'Thông tin trả hàng',
                'comment' => '- Thông tin về thời gian trả hàng thực tế của đơn hàng
                - Nhập ngày tháng theo định dạng: ngày-tháng-năm (dd-mm-yyyy).
                Ví dụ: 09-01-2019 hoặc 20-11-2019.',
                'collapse' => 1,
                'width' => 20


            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Thông tin bổ sung trả hàng',
                'field' => 'informative_arrival',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin trả hàng',
                'collapse' => 1,
                'width' => 40,
                'comment' => '- Thông tin thêm về việc trả hàng'

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tên liên hệ trả hàng',
                'field' => 'contact_name_arrival',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin trả hàng',
                'comment' => '- Thông tin về tên người liên hệ khi trả hàng',
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số điện thoại liên hệ trả hàng',
                'field' => 'contact_mobile_no_arrival',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin trả hàng',
                'comment' => '- Thông tin về địa chỉ email của người liên hệ trả hàng
                - Thông tin không bắt buộc, có thể nhập hoặc bỏ qua.',
                'collapse' => 1,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Email liên hệ trả hàng',
                'field' => 'contact_email_arrival',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin trả hàng',
                'comment' => '- Thông tin về địa chỉ email của người liên hệ trả hàng
                - Thông tin không bắt buộc, có thể nhập hoặc bỏ qua.',
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Phí bốc xếp hàng hoá trả hàng',
                'field' => 'loading_arrival_fee',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin trả hàng',
                'comment' => '- Thông tin về phí bốc xếp hàng hóa khi trả hàng (nếu có).
                - Yêu cầu: Chỉ nhập các ký tự số
                - Đơn vị tính mặc định: VNĐ (Việt Nam Đồng)
                - Nếu không nhập, mặc định không có phí bốc xếp và số tiền mặc định bằng 0 (VNĐ)',
                'collapse' => 1,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Xe',
                'field' => 'vehicle',
                'original_field' => 'vehicle_id',
                'default_value' => null,
                'data_type' => 'list',
                'function' => null,
                'header_group' => 'Thông tin xe và tài xế',
                'comment' => '- Thông tin về xe được gán
                - Yêu cầu: Nhập biển số xe được lưu trong hệ thống',
                'entity' => 'vehicle',
                'code' => 'reg_no',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tài xế',
                'field' => 'primary_driver',
                'original_field' => 'primary_driver_id',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'header_group' => 'Thông tin xe và tài xế',
                'comment' => '- Thông tin về tài xế
                - Yêu cầu: Nhập mã của lái xe được lưu trong hệ thống',
                'entity' => 'driver',
                'code' => 'code',
                'title' => 'full_name',
                'width' => 20
            ]
        );
        if (env('COMPANY_CODE', '') == "PHUONGANH") {
            array_push(
                $items,
                [
                    'excel_column_config_id' => $excelColumnConfig->id,
                    'column_name' => 'Ghép chuyến xe',
                    'field' => 'route_name',
                    'default_value' => null,
                    'data_type' => 'string',
                    'function' => null,
                    'header_group' => 'Thông tin xe và tài xế',
                    'comment' => '- Nhập mã đơn hàng muốn ghép cùng chuyến',
                    'width' => 20
                ]
            );
        }
        array_push(
            $items,
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tài phụ',
                'field' => 'secondary_driver',
                'original_field' => 'secondary_driver_id',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'header_group' => 'Thông tin xe và tài xế',
                'comment' => '- Thông tin về phụ xe.
                - Yêu cầu: Nhập mã của lái xe được lưu trong hệ thống',
                'entity' => 'driver',
                'code' => 'code',
                'title' => 'full_name',
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Ghi chú',
                'field' => 'note',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => null,
                'comment' => '- Lưu ý hiển thị trên apps tài xế',
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Có là hàng ghép hay không',
                'field' => 'is_merge_item',
                'data' => 'order_is_merge_item',
                'default_value' => 'no',
                'data_type' => 'list',
                'function' => 'convertYesNo',
                'header_group' => null,
                'comment' => 'Nếu là hàng ghép vui lòng chọn có',
                'collapse' => 1,
                'width' => 20
            ]
        );
        if (env('COMPANY_CODE', '') != "PHUONGANH") {
            array_push(
                $items,
                [
                    'excel_column_config_id' => $excelColumnConfig->id,
                    'column_name' => 'Ghép chuyến xe',
                    'field' => 'route_name',
                    'default_value' => null,
                    'data_type' => 'string',
                    'function' => null,
                    'header_group' => null,
                    'comment' => '- Nhập mã đơn hàng muốn ghép cùng chuyến',
                    'collapse' => 1,
                    'width' => 20
                ]
            );
        }

        array_push(
            $items,
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Trạng thái chứng từ',
                'field' => 'status_collected_documents',
                'data' => 'order_status_collected_documents',
                'default_value' => 'CHUA_THU_DU',
                'data_type' => 'list',
                'function' => 'convertStatusCollectedDocuments',
                'header_group' => null,
                'comment' => null,
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Loại chiết khấu',
                'field' => 'commission_type',
                'data' => 'order_commission_type',
                'default_value' => 'TONG_TIEN_HOA_HONG',
                'data_type' => 'list',
                'function' => 'convertCommissionType',
                'header_group' => null,
                'comment' => null,
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tỷ lệ hoa hồng',
                'field' => 'commission_value',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => null,
                'comment' => null,
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tiền nhận từ khách hàng (VND)',
                'field' => 'cod_amount',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => null,
                'comment' => null,
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Hình thức thanh toán',
                'field' => 'payment_type',
                'data' => 'order_payment_type',
                'default_value' => 'CHUYEN_KHOAN',
                'data_type' => 'list',
                'function' => 'convertPaymentType',
                'header_group' => 'Thông tin thanh toán',
                'comment' => null,
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Người chịu trách nhiệm thanh toán',
                'field' => 'payment_user_id',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'header_group' => 'Thông tin thanh toán',
                'comment' => null,
                'collapse' => 1,
                'entity' => 'adminUser',
                'code' => 'username',
                'title' => 'full_name',
                'width' => 40
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Giá trị hàng hoá (VND)',
                'field' => 'goods_amount',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin thanh toán',
                'comment' => null,
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'VAT',
                'field' => 'vat',
                'data' => 'order_vat',
                'default_value' => 'no',
                'data_type' => 'list',
                'function' => 'convertYesNo',
                'header_group' => 'Thông tin thanh toán',
                'comment' => null,
                'collapse' => 1,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Cước gửi VND)',
                'field' => 'anonymous_amount',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin thanh toán',
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng số lượng',
                'field' => 'quantity',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin hàng hoá',
                'comment' => null,
                'collapse' => 1,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng khối lượng (kg)',
                'field' => 'weight',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin hàng hoá',
                'comment' => null,
                'collapse' => 1,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng thể tích (m3)',
                'field' => 'volume',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin hàng hoá',
                'comment' => null,
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Hàng hoá có bảo hiểm hay không',
                'field' => 'is_insured_goods',
                'data' => 'order_is_insured_goods',
                'default_value' => 'no',
                'data_type' => 'list',
                'function' => 'convertYesNo',
                'header_group' => 'Thông tin hàng hoá',
                'comment' => 'Nếu là hàng hoá có bảo hiểm vui lòng chọn có',
                'collapse' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng số lượng ĐHKH',
                'field' => 'quantity_order_customer',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Sản lượng đơn hàng khách hàng',
                'comment' => null,
                'collapse' => 1,
                'width' => 22

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng khối lượng ĐHKH (kg)',
                'field' => 'weight_order_customer',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Sản lượng đơn hàng khách hàng',
                'comment' => null,
                'collapse' => 1,
                'width' => 22

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng thể tích ĐHKH (m3)',
                'field' => 'volume_order_customer',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Sản lượng đơn hàng khách hàng',
                'comment' => null,
                'collapse' => 1,
                'width' => 22
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số lượng điểm nhận hàng',
                'field' => 'number_of_delivery_points',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => null,
                'comment' => null,
                'collapse' => 1,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số lượng điểm trả hàng',
                'field' => 'number_of_arrival_points',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => null,
                'comment' => null,
                'collapse' => 1,
                'width' => 20
            ]
        );

        if (env('COMPANY_CODE', '') != "NGUYENNGOC") {
            $ignores = [
                'name_of_province_destination_id',
                'name_of_province_arrival_id',
                'name_of_district_destination_id',
                'name_of_district_arrival_id'
            ];
            $items = collect($items)->reject(function ($item) use ($ignores) {
                return in_array($item['field'], $ignores);
            })->filter()->toArray();
        }
        $idx = 0;
        foreach ($items as $index => $item) {
            $item["column_index"] = PHPExcel_Cell::stringFromColumnIndex($idx);
            ExcelColumnMappingConfig::firstOrCreate($item);
            $idx++;
        }
    }

    public function route()
    {
        // Chuyến xe
        $data = [
            'model' => 'route',
            'is_system' => 1,
            'user_id' => null,
            'header_index' => 12,
            'max_row' => 500,
        ];
        $excelColumnConfig = ExcelColumnConfig::firstOrCreate($data);

        $items = [
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Mã hệ thống',
                'field' => 'route_code',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'is_key' => 1,
                'width' => 20,
                'is_import' => 1
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tên chuyến',
                'field' => 'name',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 1
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Xe',
                'field' => 'reg_no',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tài xế',
                'field' => 'primary_driver_name',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
            ]
        ];

        $supportCarTransportation = env('SUPPORT_CAR_TRANSPORTATION', false);
        if ($supportCarTransportation) {

            array_push(
                $items,
                [
                    'excel_column_config_id' => $excelColumnConfig->id,
                    'column_name' => 'Số model',
                    'field' => 'model_no',
                    'default_value' => null,
                    'data_type' => 'string',
                    'function' => null,
                    'comment' => null,
                    'width' => 20,
                    'is_import' => 0,
                    'is_group' => 1,
                ],
                [
                    'excel_column_config_id' => $excelColumnConfig->id,
                    'column_name' => 'Số khung',
                    'field' => 'vin_no',
                    'default_value' => null,
                    'data_type' => 'string',
                    'function' => null,
                    'comment' => null,
                    'width' => 20,
                    'is_import' => 0,
                    'is_group' => 1,
                ]
            );
        }
        array_push(
            $items,
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Thời gian dự kiến xuất phát',
                'field' => 'ETD_date',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Thời gian dự kiến đến',
                'field' => 'ETA_date',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Thời gian nhận hàng thực tế',
                'field' => 'ETD_date_reality',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Thời gian trả hàng thực tế',
                'field' => 'ETA_date_reality',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng số lượng',
                'field' => 'quantity',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng khối lượng',
                'field' => 'weight',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng thể tích',
                'field' => 'volume',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Danh sách đơn hàng',
                'field' => 'list_orders',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Khối lượng theo đơn hàng',
                'field' => 'list_weight',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số lượng theo đơn hàng',
                'field' => 'list_quantity',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Thể tích theo đơn hàng',
                'field' => 'list_volume',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Điểm đầu',
                'field' => 'location_destination',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 30,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Quận/Huyện điểm đầu',
                'field' => 'district_destination',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 25,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tỉnh/Thành phố điểm đầu',
                'field' => 'province_destination',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 25,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Điểm cuối',
                'field' => 'location_arrival',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 30,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Quận/Huyện điểm cuối',
                'field' => 'district_arrival',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 25,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tỉnh/Thành phố điểm cuối',
                'field' => 'province_arrival',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 25,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Chú thích đơn hàng',
                'field' => 'list_order_note',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0,
                'is_group' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số km thực tế (GPS)',
                'field' => 'gps_distance',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 0
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Trạng thái phê duyệt',
                'field' => 'is_approved',
                'default_value' => null,
                'data' => 'route_is_approved',
                'data_type' => 'list',
                'function' => 'convertIsApproval',
                'comment' => null,
                'width' => 20,
                'background_color' => 'ffff00'
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Ghi chú phê duyệt',
                'field' => 'approved_note',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 1,
                'background_color' => 'ffff00'
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Định mức',
                'field' => 'amount_admin',
                'default_value' => null,
                'data_type' => 'nested',
                'nested_data_type' => 'number',
                'nested_field' => 'costs',
                'nested_name' => 'name',
                'nested_match' => 'receipt_payment_id',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 1
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Thực tế',
                'field' => 'amount_driver',
                'default_value' => null,
                'data_type' => 'nested',
                'nested_data_type' => 'number',
                'nested_field' => 'costs',
                'nested_name' => 'name',
                'nested_match' => 'receipt_payment_id',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 1
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Cuối cùng',
                'field' => 'amount',
                'default_value' => null,
                'data_type' => 'nested',
                'nested_data_type' => 'number',
                'nested_field' => 'costs',
                'nested_name' => 'name',
                'nested_match' => 'receipt_payment_id',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 1,
                'background_color' => 'ffff00'
            ]
        );

        if (env('COMPANY_CODE', '') != "NGUYENNGOC") {
            $ignores = [
                'district_destination',
                'province_destination',
                'district_arrival',
                'province_arrival',
                'list_orders',
                'list_weight',
                'list_volume',
                'list_quantity',
                'weight',
                'volume',
                'quantity'
            ];
            $items = collect($items)->reject(function ($item) use ($ignores) {
                return in_array($item['field'], $ignores);
            })->toArray();
        }
        $idx = 0;
        foreach ($items as $index => $item) {
            $item["column_index"] = PHPExcel_Cell::stringFromColumnIndex($idx);
            ExcelColumnMappingConfig::firstOrCreate($item);
            $idx++;
        }
    }

    public function orderCustomer()
    {
        $data = [
            'model' => 'order_customer',
            'is_system' => 1,
            'user_id' => null,
            'header_index' => 10,
            'max_row' => 500,
        ];
        $excelColumnConfig = ExcelColumnConfig::firstOrCreate($data);

        $items = [
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Mã ĐHKH',
                'field' => 'code',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => null,
                'is_key' => 1,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tên ĐHKH',
                'field' => 'name',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => null,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số đơn hàng',
                'field' => 'order_no',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => null,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Ngày đặt hàng',
                'field' => 'order_date',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'header_group' => null,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Danh sách đơn hàng',
                'field' => 'order_codes',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => null,
                'width' => 20
            ],
        ];
        $supportCarTransportation = env('SUPPORT_CAR_TRANSPORTATION', false);
        if ($supportCarTransportation) {
            array_push(
                $items,
                [
                    'excel_column_config_id' => $excelColumnConfig->id,
                    'column_name' => 'Số model',
                    'field' => 'model_nos',
                    'default_value' => null,
                    'data_type' => 'string',
                    'function' => null,
                    'header_group' => null,
                    'width' => 20,
                    'is_import' => 0
                ],
                [
                    'excel_column_config_id' => $excelColumnConfig->id,
                    'column_name' => 'Số khung',
                    'field' => 'vin_nos',
                    'default_value' => null,
                    'data_type' => 'string',
                    'function' => null,
                    'header_group' => null,
                    'width' => 20,
                    'is_import' => 0
                ]
            );
        }

        array_push(
            $items,
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Khách hàng',
                'field' => 'customer_code',
                'default_value' => null,
                'data_type' => 'list',
                'entity' => 'customer',
                'code' => 'customer_code',
                'title' => 'full_name',
                'function' => 'getCode',
                'header_group' => 'Thông tin khách hàng',
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tên khách hàng/Người đại diện',
                'field' => 'customer_name',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin khách hàng',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số điện thoại khách hàng',
                'field' => 'customer_mobile_no',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin khách hàng',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Địa điểm nhận hàng',
                'field' => 'location_destination_code',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'header_group' => 'Thông tin lộ trình',
                'entity' => 'location',
                'code' => 'code',
                'title' => 'title',
                'width' => 40

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Giờ nhận hàng',
                'field' => 'ETD_time',
                'default_value' => null,
                'data_type' => 'time',
                'function' => null,
                'header_group' => 'Thông tin lộ trình',
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Ngày nhận hàng',
                'field' => 'ETD_date',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'header_group' => 'Thông tin lộ trình',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Địa điểm trả hàng',
                'field' => 'location_arrival_code',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'header_group' => 'Thông tin lộ trình',
                'entity' => 'location',
                'code' => 'code',
                'title' => 'title',
                'width' => 40
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Giờ trả hàng',
                'field' => 'ETA_time',
                'default_value' => null,
                'data_type' => 'time',
                'function' => null,
                'header_group' => 'Thông tin lộ trình',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Ngày trả hàng',
                'field' => 'ETA_date',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'header_group' => 'Thông tin lộ trình',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Quãng đường (km)',
                'field' => 'distance',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin lộ trình',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng số lượng (CBM)',
                'field' => 'quantity',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin hàng hoá',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng dung tích (m3)',
                'field' => 'volume',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin hàng hoá',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng khối lượng (kg)',
                'field' => 'weight',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin hàng hoá',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Chủng loại xe',
                'field' => 'vehicle_group_code',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'entity' => 'vehicleGroup',
                'code' => 'code',
                'title' => 'name',
                'header_group' => 'Thông tin hàng hoá',
                'width' => 20,
                'is_multiple' => 1,
                'mapping_data' => 'listVehicleGroups',
                'mapping_field' => 'vehicle_group_id',
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số lượng xe',
                'field' => 'vehicle_number',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin hàng hoá',
                'width' => 20,
                'is_multiple' => 1,
                'mapping_data' => 'listVehicleGroups',
                'mapping_field' => 'vehicle_number',
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số lượng chuyến',
                'field' => 'route_number',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin hàng hoá',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Cước phí vận chuyển',
                'field' => 'amount',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin hàng hoá',
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Loại chiết khấu',
                'field' => 'commission_type',
                'data' => 'order_customer_commission_type',
                'default_value' => 'TONG_TIEN_HOA_HONG',
                'data_type' => 'list',
                'function' => 'convertCommissionType',
                'header_group' => 'Thông tin hàng hoá',
                'comment' => null,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tỷ lệ hoa hồng',
                'field' => 'commission_value',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin hàng hoá',
                'comment' => null,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Hình thức thanh toán',
                'field' => 'payment_type',
                'data' => 'order_customer_payment_type',
                'default_value' => 'CHUYEN_KHOAN',
                'data_type' => 'list',
                'function' => 'convertPaymentType',
                'header_group' => 'Thông tin thanh toán',
                'comment' => null,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Người chịu trách nhiệm thanh toán',
                'field' => 'payment_user_id',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'header_group' => 'Thông tin thanh toán',
                'comment' => null,
                'entity' => 'adminUser',
                'code' => 'username',
                'title' => 'full_name',
                'width' => 40
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Giá trị hàng hoá (VND)',
                'field' => 'goods_amount',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin thanh toán',
                'comment' => null,
                'width' => 20
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'VAT',
                'field' => 'vat',
                'data' => 'order_customer_vat',
                'default_value' => 'no',
                'data_type' => 'list',
                'function' => 'convertYesNo',
                'header_group' => 'Thông tin thanh toán',
                'comment' => null,
                'width' => 20

            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Cước gửi VND)',
                'field' => 'anonymous_amount',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin thanh toán',
                'width' => 20
            ]
        );
        foreach ($items as $index => $item) {
            $item["column_index"] = PHPExcel_Cell::stringFromColumnIndex($index);
            ExcelColumnMappingConfig::firstOrCreate($item);
        }
    }

    public function repairTicket()
    {

        //-- Phiếu sửa chữa -----
        $data = [
            'model' => 'repair_ticket',
            'is_system' => 1,
            'user_id' => null,
            'header_index' => 10,
            'max_row' => 500,
        ];
        $excelColumnConfig = ExcelColumnConfig::firstOrCreate($data);

        $items = [
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Mã phiếu sửa chữa',
                'field' => 'code',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'comment' => null,
                'header_group' => 'Thông tin chung',
                'is_key' => 1,
                'width' => 20,
                'is_import' => 1
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tên phiếu sửa chữa',
                'field' => 'name',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin chung',
                'comment' => null,
                'width' => 20,
                'is_import' => 1
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Xe',
                'field' => 'vehicle',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'header_group' => 'Thông tin chung',
                'entity' => 'vehicle',
                'code' => 'reg_no',
                'function' => null,
                'comment' => null,
                'width' => 20,
                'is_import' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tài xế',
                'field' => 'driver',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'header_group' => 'Thông tin chung',
                'entity' => 'driver',
                'code' => 'code',
                'title' => 'full_name',
                'width' => 20,
                'is_import' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Thời gian sửa chữa',
                'field' => 'repair_date',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'header_group' => 'Thông tin chung',
                'comment' => null,
                'width' => 20,
                'is_import' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Tổng tiền',
                'field' => 'amount',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin chung',
                'comment' => null,
                'width' => 20,
                'is_import' => 1,
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Mô tả',
                'field' => 'description',
                'default_value' => null,
                'data_type' => 'string',
                'function' => null,
                'header_group' => 'Thông tin chung',
                'comment' => null,
                'width' => 20,
                'is_import' => 1
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Phụ tùng',
                'field' => 'accessory_code',
                'default_value' => null,
                'data_type' => 'list',
                'function' => 'getCode',
                'comment' => null,
                'header_group' => 'Thông tin sửa chữa',
                'entity' => 'accessory',
                'code' => 'name',
                'width' => 15,
                'is_import' => 1,
                'is_multiple' => 1,
                'mapping_data' => 'repairTicketItems',
                'mapping_field' => 'accessory_id',
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số lượng',
                'field' => 'quantity',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'comment' => null,
                'header_group' => 'Thông tin sửa chữa',
                'width' => 15,
                'is_import' => 1,
                'is_multiple' => 1,
                'mapping_data' => 'repairTicketItems',
                'mapping_field' => 'quantity',
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Đơn giá',
                'field' => 'price',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin sửa chữa',
                'comment' => null,
                'width' => 15,
                'is_import' => 1,
                'is_multiple' => 1,
                'mapping_data' => 'repairTicketItems',
                'mapping_field' => 'price',
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Thành tiền',
                'field' => 'amount',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin sửa chữa',
                'comment' => null,
                'width' => 15,
                'is_import' => 1,
                'is_multiple' => 1,
                'mapping_data' => 'repairTicketItems',
                'mapping_field' => 'amount',
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Ngày bảo dưỡng tiếp theo',
                'field' => 'next_repair_date',
                'default_value' => null,
                'data_type' => 'date',
                'function' => null,
                'header_group' => 'Thông tin sửa chữa',
                'comment' => null,
                'width' => 15,
                'is_import' => 1,
                'is_multiple' => 1,
                'mapping_data' => 'repairTicketItems',
                'mapping_field' => 'next_repair_date',
            ],
            [
                'excel_column_config_id' => $excelColumnConfig->id,
                'column_name' => 'Số km bảo dưỡng',
                'field' => 'next_repair_distance',
                'default_value' => null,
                'data_type' => 'number',
                'function' => null,
                'header_group' => 'Thông tin sửa chữa',
                'comment' => null,
                'width' => 15,
                'is_import' => 1,
                'is_multiple' => 1,
                'mapping_data' => 'repairTicketItems',
                'mapping_field' => 'next_repair_distance',
            ],
        ];
        foreach ($items as $index => $item) {
            $item["column_index"] = PHPExcel_Cell::stringFromColumnIndex($index);
            ExcelColumnMappingConfig::firstOrCreate($item);
        }
    }
}
