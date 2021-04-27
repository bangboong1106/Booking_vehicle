<?php

return [
    'common' => [
        'id' => 'ID',
        'ins_id' => 'Người tạo',
        'upd_id' => 'Người sửa',
        'upd_date' => 'Thời gian cập nhật',
        'ins_date' => 'Thời gian thêm',
        'del_flag' => 'Cờ xóa',
        'province' => 'Tỉnh/Thành phố',
        'district' => 'Quận/Huyện',
        'ward' => 'Xã/Phường',
        'address' => 'Địa chỉ',
        'address_entered' => 'Nhập địa chỉ',
        'address_pac' => 'Nhập địa chỉ',
        'address_search' => 'Tìm kiếm địa chỉ',
        'address_placeholder' => 'Dán hoặc nhập địa chỉ vào....',
        'vehicle_search' => 'Tìm kiếm xe',
        'note' => 'Ghi chú',
        'add_order' => 'Thêm đơn hàng',
        'add_route' => 'Thêm chuyến xe',
        'add_quota' => 'Thêm bảng định mức',
        'add_customer' => 'Thêm khách hàng',
        'add_vehicle' => 'Thêm xe',
        'add_driver' => 'Thêm tài xế',
        'delete_id' => 'Người xóa',
        'delete_date' => 'Ngày xóa',
        'selected_count' => 'Đã chọn',
        'partner_code' => 'Mã đối tác'
    ],
    'reportList' => [
        'name' => 'Danh sách báo cáo thống kê',
    ],
    'board' => [
        'name' => 'Tổng quan',
    ],
    'dashboard' => [
        'name' => 'Bảng điều khiển vận tải',
    ],
    'order_board' => [
        'name' => 'Bảng điều khiển vận tải',
    ],
    'order_customer_board' => [
        'name' => 'Bảng theo dõi đặt hàng',
    ],
    'route_board' => [
        'name' => 'Bảng theo dõi vận chuyển',
    ],
    'category' => [
        'name' => 'Danh mục',
        'attributes' => [
            'name' => 'Tên danh mục',
            'description' => 'Mô tả',
            'image' => 'Ảnh'
        ],
    ],
    'admin' => [
        'name' => 'Người dùng',
        'attributes' => [
            'username' => 'Tên đăng nhập',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'avatar' => 'Ảnh đại diện',
            'password_note_text' => 'Để trống nếu không thay đổi',
            'password_confirmation' => 'Xác nhận mật khẩu',
            'role' => 'Quyền',
            'active' => 'Trạng thái kích hoạt',
            'activate' => 'Kích hoạt',
            'disable' => 'Khóa',
            'driver_team' => 'Đội tài xế',
            'full_name' => 'Họ tên',
            'customer_groups' => 'Nhóm khách hàng',
            'partner_id' => 'Đối tác vận tải',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ],
    ],
    /* Tinh thanh */
    'province' => [
        'name' => 'Tỉnh - Thành phố',
        'attributes' => [
            'title' => 'Tên',
            'type' => 'Loại',
            'province_id' => 'Mã'
        ]
    ],

    'district' => [
        'name' => 'Quận - Huyện',
        'attributes' => [
            'title' => 'Tên',
            'type' => 'Loại',
            'province' => 'Tỉnh/Thành phố',
            'district_id' => 'Mã'
        ]
    ],

    'ward' => [
        'name' => 'Xã - Phường',
        'attributes' => [
            'title' => 'Tên',
            'type' => 'Loại',
            'province' => 'Tỉnh/Thành phố',
            'district' => 'Quận/Huyện',
            'ward_id' => 'Mã'
        ]
    ],
    /* End Tinh thanh */


    /* Vai tro */
    'role' => [
        'name' => 'Vai trò',
        'attributes' => [
            'title' => 'Tên vai trò',
            'name' => 'Mã vai trò',
            'users' => 'Danh sách người dùng',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'information' => 'Thông tin chung',
            'vehicleTeam' => 'Thông tin đội tài xế',
            'customerGroup' => 'Thông tin nhóm khách hàng'
        ],
        'permissions' => [
            'title' => 'Thông tin phân quyền',
            'name' => 'Quyền',
            'view' => 'Xem',
            'add' => 'Thêm',
            'edit' => 'Sửa',
            'delete' => 'Xóa',
            'import' => 'Nhập',
            'export' => 'Xuất',
            'lock' => 'Khoá',
            'unlock' => 'Mở khoá',
            'choose_all' => 'Chọn tất cả',
            'uncheck_all' => 'Bỏ chọn tất cả',
            'open_all' => 'Mở tất cả',
            'close_all' => 'Đóng tất cả',
        ]
    ],
    /* Lai xe */

    'driver' => [
        'name' => 'Tài xế',
        'attributes' => [
            'code' => 'Mã tài xế',
            'adminUser.username' => 'Tên đăng nhập',
            'adminUser.email' => 'Email',
            'adminUser.password' => 'Mật khẩu',
            'avatar' => 'Ảnh đại diện',
            'password_note_text' => 'Để trống nếu không thay đổi',
            'adminUser.password_confirmation' => 'Xác nhận mật khẩu',
            'full_name' => 'Họ tên',
            'mobile_no' => 'Số điện thoại',
            'address' => 'Nơi ở hiện tại',
            'sex' => 'Giới tính',
            'birth_date' => 'Ngày sinh',
            'working_status' => 'Tình trạng',
            'note' => 'Ghi chú',
            'hometown' => 'Quê quán',
            'vehicle_team_id' => 'Đội tài xế',
            'experience_drive' => 'Thâm niên lái xe từ ngày vào công ty (năm)',
            'experience_work' => 'Thâm niên tại công ty (năm)',
            'work_date' => 'Ngày vào công ty',
            'vehicle_id' => 'Xe đang lái',
            'vehicle_old' => 'Các xe đã từng lái',
            'evaluate' => 'Đánh giá của điều hành trực tiếp',
            'rank' => 'Xếp hạng lái xe',
            'work_description' => 'Mô tả quá trình công tác',
            'id_no' => 'Số CMT',
            'driver_license' => 'Bằng lái',
            'information' => 'Thông tin',
            'files_info' => 'Thông tin giấy tờ',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'username' => 'Tài khoản',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'vehicle-team' => 'Danh sách đội tài xế',
            'vehicle' => 'Danh sách xe',
            'vehicles_reg_no' => 'Danh sách xe',
            'partner_id' => 'Đối tác vận tải',
            'name_of_partner_id' => 'Đối tác vận tải',
            'ready_status' => 'Sẵn sàng nhận chuyến'
        ]
    ],

    'driver_config_file' => [
        'name' => 'Giấy tờ tài xế',
        'attributes' => [
            'driver_type' => 'Kiểu',
            'active' => 'Trạng thái',
            'file_name' => 'Tên',
            'is_required' => 'Bắt buộc',
            'is_show_expired' => 'Hiển thị ngày hết hạn',
            'is_show_register' => 'Hiển thị ngày đăng ký',
            'allow_extension' => 'Định dạng',
            'note' => 'Ghi chú',
            'register_date' => 'Ngày đăng ký',
            'expired_date' => 'Ngày hết hạn'
        ]
    ],
    /* Xe */

    'vehicle' => [
        'name' => 'Xe',
        'attributes' => [
            'reg_no' => 'Biển số',
            'group_id' => 'Chủng loại xe',
            'name_of_group_id' => 'Chủng loại xe',
            'current_location' => 'Vị trí hiện tại',
            'status' => 'Trạng thái',
            'type' => 'Loại xe',
            'active' => 'Tình trạng',
            'province' => 'Tỉnh/Thành phố',
            'district' => 'Quận/Huyện',
            'ward' => 'Xã/Phường',
            'address' => 'Địa chỉ',
            'placeholder' => 'Giá trị',
            'driver' => 'Danh sách tài xế',
            'listDriver' => 'Danh sách tài xế',
            'volume' => 'Dung tích',
            'weight' => 'Tải trọng',
            'bag_size' => 'Kích thước bao',
            'length_width_height' => 'Kích thước bao/Dài * Rộng * Cao',
            'length' => 'Kích thước bao - Dài',
            'height' => 'Kích thước bao - Cao',
            'width' => 'Kích thước bao - Rộng',
            'information' => 'Thông tin',
            'files_info' => 'Thông tin giấy tờ',
            'information_expand' => 'Thông tin bổ sung',
            'gps_company_id' => 'Công ty GPS',
            'name_of_gps_company_id' => 'Công ty GPS',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'driver_codes' => 'Mã tài xế',
            'driver_ids' => 'Danh sách tài xế',
            'register_year' => 'Năm sản xuất',
            'brand' => 'Nhãn hiệu',
            'weight_lifting_system' => 'Hệ thống nâng hạ',
            'max_fuel_header' => 'ĐMTTNL không hàng',
            'max_fuel_with_goods_header' => 'ĐMTTNL có hàng',
            'max_fuel' => 'Định mức tiêu thụ nhiên liệu không hàng',
            'max_fuel_with_goods' => 'Định mức tiêu thụ nhiên liệu có hàng',
            'category_of_barrel' => 'Chủng loại thùng',
            'repair_distance' => 'Số km bảo dưỡng',
            'repair_date' => 'Ngày bảo dưỡng gần nhất',
            'drivers_name' => 'Danh sách tài xế',
            'repair_ticket' => 'Danh sách phiếu sửa chữa',
            'partner_id' => 'Đối tác vận tải',
            'name_of_partner_id' => 'Đối tác vận tải'
        ]
    ],

    'vehicle_general_info' => [
        'name' => 'Thông số xe',
        'attributes' => [
            'register_year' => 'Năm sản xuất',
            'brand' => 'Nhãn hiệu',
            'weight_lifting_system' => 'Hệ thống nâng hạ',
            'max_fuel_header' => 'ĐMTTNL không hàng',
            'max_fuel_with_goods_header' => 'ĐMTTNL có hàng',
            'max_fuel' => 'Định mức tiêu thụ nhiên liệu không hàng',
            'max_fuel_with_goods' => 'Định mức tiêu thụ nhiên liệu có hàng',
            'category_of_barrel' => 'Chủng loại thùng'
        ]
    ],

    'vehicle_config_file' => [
        'name' => 'Giấy tờ xe',
        'attributes' => [
            'vehicle_type' => 'Kiểu xe',
            'active' => 'Trạng thái',
            'file_name' => 'Tên',
            'is_required' => 'Bắt buộc',
            'is_show_expired' => 'Hiển thị ngày hết hạn',
            'is_show_register' => 'Hiển thị ngày đăng ký',
            'allow_extension' => 'Định dạng',
            'note' => 'Ghi chú',
            'register_date' => 'Ngày đăng ký',
            'expired_date' => 'Ngày hết hạn'
        ]
    ],
    'vehicle_config_specification' => [
        'name' => 'Thông tin bổ sung xe',
        'attributes' => [
            'vehicle_type' => 'Kiểu xe',
            'active' => 'Trạng thái',
            'name' => 'Tên',
            'type' => 'Kiểu giá trị',
        ]
    ],
    /* Nhóm xe */
    'vehicle_group' => [
        'name' => 'Chủng loại xe',
        'attributes' => [
            'name' => 'Chủng loại xe',
            'parent_id' => 'Chủng loại xe cha',
            'code' => 'Mã chủng loại xe',
            'partner_id' => 'Đối tác vận tải',
            'name_of_partner_id' => 'Đối tác vận tải'
        ]
    ],
    /* Vai tro */


    /* Thu chi*/
    'receipt_payment' => [
        'name' => 'Thu chi',
        'attributes' => [
            'name' => 'Thu chi',
            'parent_id' => 'Thu chi cha',
            'type' => 'Loại',
            'name_thu' => 'Tên danh mục thu',
            'name_chi' => 'Tên danh mục chi',
            'parent_thu' => 'Tên danh mục thu cha',
            'parent_chi' => 'Tên danh mục chi cha',
            'is_display_driver' => 'Có hiển thị dưới ứng dụng dành cho tài xế không?',

        ]
    ],
    /* Thu chi */

    /* Đội xe*/
    'vehicle_team' => [
        'name' => 'Đội tài xế',
        'attributes' => [
            'name' => 'Tên đội',
            'capital_driver_id' => 'Đội trưởng',
            'name_of_capital_driver_id' => 'Đội trưởng',
            'driver_ids' => 'Danh sách tài xế',
            'code' => 'Mã đội tài xế',
            'drivers_name' => 'Danh sách tài xế',
            'vehicles_reg_no' => 'Danh sách xe',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'partner_id' => 'Đối tác vận tải',
            'name_of_partner_id' => 'Đối tác vận tải'
        ]
    ],
    /* Đội xe */

    // location
    'location' => [
        'name' => 'Địa Điểm',
        'attributes' => [
            'title' => 'Tên địa điểm',
            'code' => 'Mã địa điểm',
            'address' => 'Địa chỉ',
            'location' => 'Tọa độ',
            'full_address' => 'Địa chỉ đầy đủ',
            'display_address' => 'Địa chỉ hiển thị',
            'location_type_id' => 'Loại địa điểm',
            'province_id' => 'Tỉnh/Thành phố',
            'district_id' => 'Quận/Huyện',
            'ward_id' => 'Xã/Phường',
            'limited_day' => 'Số ngày giới hạn thu chứng từ',
            'location_group_id' => 'Nhóm địa điểm',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'name_of_customer_id' => 'Chủ hàng',
        ],
    ],

    // order
    'order' => [
        'name' => 'Đơn hàng vận tải',
        'attributes' => [
            'id' => 'Mã',
            'title' => 'Tên đơn hàng vận tải',
            'type' => 'Loại',
            'order_code' => 'Mã hệ thống',
            'order_no' => 'Số đơn hàng',
            'model_no' => 'Số model',
            'vin_no' => 'Số khung',
            'order_purchasing_no' => 'Số thứ tự đơn hàng vận tải',
            'order_date' => 'Ngày đặt hàng',
            'bill_no' => 'Số hóa đơn',
            'customer_id' => 'Chủ hàng',
            'name_of_customer_id' => 'Chủ hàng',
            'customer_name' => 'Tên người đại diện',
            'information' => 'Thông tin chung',
            'communication' => 'Thông tin',
            'customer_and_status' => 'Trạng thái đơn hàng vận tải',
            'etd' => 'Địa điểm nhận hàng',
            'eta' => 'Địa điểm trả hàng',
            'contact_name_destination' => 'Tên liên hệ nhận hàng',
            'contact_mobile_no_destination' => 'Điện thoại liên hệ nhận hàng',
            'contact_email_destination' => 'Email',
            'contact_name_arrival' => 'Tên liên hệ trả hàng',
            'contact_mobile_no_arrival' => 'Điện thoại liên hệ trả hàng',
            'contact_email_arrival' => 'Email',
            'goods_type' => 'Loại hàng hóa',
            'goods_unit' => 'Đơn vị',
            'good_unit_id' => 'Mã đơn vị hàng hóa',
            'ETD' => 'Ngày nhận hàng',
            'ETA' => 'Ngày trả hàng',
            'destination_info' => 'Thông tin nhận hàng',
            'arrival_destination_info' => 'Thông tin nhận trả hàng',
            'arrival_info' => 'Thông tin trả hàng',
            'informative_destination' => 'Thông tin bổ sung',
            'informative_arrival' => 'Thông tin bổ sung',
            'good_details' => 'Mô tả hàng hóa',

            'amount' => 'Cước phí vận chuyển',
            'quantity' => 'Tổng số lượng',
            'quantity_out' => 'Tổng số lượng đã xuất',
            'volume' => 'Tổng thể tích',
            'weight' => 'Tổng trọng lượng',
            'quantity_order_customer' => 'Tổng số lượng ĐHKH',
            'volume_order_customer' => 'Tổng thể tích ĐHKH',
            'weight_order_customer' => 'Tổng trọng lượng ĐHKH',
            'precedence' => 'Độ ưu tiên',
            'status' => 'Trạng thái',
            'route_create' => 'Tự động tạo chuyến xe mới',
            'vehicle' => 'Xe',
            'choose_vehicle_driver' => 'Thông tin xe và tài xế',
            'goods_info' => 'Thông tin hàng hóa',
            'files_info' => 'Thông tin đính kèm',
            'history_info' => 'Lịch sử đơn hàng vận tải',
            'order_review' => 'Đánh giá từ khách hàng',
            'note_info' => 'Thông tin bổ sung',
            'primary_driver' => 'Tài xế',
            'secondary_driver' => 'Phụ xe',
            'location_destination' => 'Điểm nhận hàng',
            'location_arrival' => 'Điểm trả hàng',
            'reason' => 'Chú thích',
            'ETD_date' => 'Ngày nhận hàng',
            'ETD_time' => 'Giờ nhận hàng',
            'ETA_date' => 'Ngày trả hàng',
            'ETA_time' => 'Giờ trả hàng',

            'ETD_date_reality' => 'Ngày nhận hàng thực tế',
            'ETD_time_reality' => 'Giờ nhận hàng thực tế',
            'ETA_date_reality' => 'Ngày trả hàng thực tế',
            'ETA_time_reality' => 'Giờ trả hàng thực tế',

            'ETD_location' => 'Địa điểm đi',
            'ETA_location' => 'Địa điểm đến',
            'customer_mobile_no' => 'Số điện thoại',
            'location_destination_id' => 'Điểm nhận hàng',
            'location_arrival_id' => 'Điểm trả hàng',
            'vehicle_id' => 'Xe',
            'primary_driver_id' => 'Tài xế',
            'insured_goods' => 'Hàng hoá có bảo hiểm',
            'acronym_insured_goods' => 'HH có bảo hiểm',
            'is_insured_goods' => 'Hàng hóa có bảo hiểm',
            'loading_destination' => 'Bốc xếp hàng hoá',
            'loading_arrival' => 'Bốc xếp hàng hoá',
            'loading_destination_fee' => 'Phí bốc xếp hàng hoá nhận hàng',
            'loading_arrival_fee' => 'Phí bốc xếp hàng hoá trả hàng',
            'code_config' => 'Dạng mã',
            'ETD_reality' => 'Ngày nhận hàng thực tế',
            'ETA_reality' => 'Ngày trả hàng thực tế',
            'date_reality' => 'Ngày thực tế',
            'time_reality' => 'Giờ thực tế',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'note' => 'Chú thích',
            'route' => 'Chuyến xe',
            'choose-route' => 'Chuyến xe',
            'quota' => 'Bảng định mức chi phí',
            'extend_cost' => 'Chi phí phát sinh',
            'currency_id' => 'Tiền tệ',
            'remark' => 'Ghi chú',

            'add_goods' => 'Thêm hàng hóa',
            'list_goods' => 'Danh sách hàng hóa',
            'total_weight' => 'Tổng khối lượng',
            'total_volume' => 'Tổng thể tích',
            'acronym_total_weight' => 'Khối lượng (kg)',
            'acronym_total_volume' => 'Thể tích (m3)',
            'goods_description' => 'Diễn giải',
            'goods_quantity' => 'Số lượng',
            'goods_insured' => 'Có bảo hiểm',
            'is_collected_documents' => 'Bắt buộc thu chứng từ',
            'status_collected_documents' => 'Tình trạng chứng từ',
            'time_collected_documents' => 'Giờ thu chứng từ',
            'date_collected_documents' => 'Ngày thu chứng từ',
            'time_collected_documents_reality' => 'Giờ thu chứng từ thực tế',
            'date_collected_documents_reality' => 'Ngày thu chứng từ thực tế',
            'document_type' => 'Loại chứng từ',
            'document_note' => 'Ghi chú',
            'num_of_document_page' => 'Số tờ chứng từ',
            'datetime_collected_documents' => 'Thời hạn thu chứng từ',
            'datetime_collected_documents_reality' => 'Thời gian thu chứng từ thực tế',
            'documents_info' => 'Thông tin chứng từ',
            'commission_amount' => 'Phí hoa hồng',
            'commission_type' => 'Loại hoa hồng',
            'commission_value' => 'Tỷ lệ hoa hồng',
            'order_customer' => 'Đơn đặt hàng',
            'cod_amount' => 'Tiền thu hộ khách hàng',
            'cod_currency_id' => 'Tiền tệ tiền thu hộ',

            'payment_info' => 'Thông tin thanh toán',
            'payment_type' => 'Hình thức thanh toán',
            'payment_user_id' => 'Người chịu trách nhiệm thanh toán',
            'name_of_payment_user_id' => 'Người chịu trách nhiệm thanh toán',
            'goods_amount' => 'Giá trị hàng hóa',
            'vat' => 'VAT',
            'anonymous_amount' => 'Cước gửi',
            'final_amount' => 'Doanh thu',
            'name_of_location_destination_code' => 'Điểm nhận hàng',
            'name_of_location_arrival_code' => 'Điểm trả hàng',
            'is_merge_item' => 'Là hàng ghép hay không',
            'gps_distance' => 'Khoảng cách GPS',
            'goods_order_customer' => 'Thông tin sản lượng ĐHKH',
            'route_id' => 'Chuyến xe',
            'order_customer_id' => 'Đơn đặt hàng',
            'number_of_delivery_points' => 'Số lượng điểm nhận hàng',
            'number_of_arrival_points' => 'Số lượng điểm trả hàng',
            'name_of_province_destination_id' => 'Tỉnh/thành phố điểm nhận hàng',
            'name_of_province_arrival_id' => 'Tỉnh/thành phố điểm trả hàng',

            'name_of_district_destination_id' => 'Quận/Huyện điểm nhận hàng',
            'name_of_district_arrival_id' => 'Quận/Huyện điểm trả hàng',

            'name_of_vehicle_group_id' => 'Chủng loại xe',
            'partner_id' => 'Đối tác vận tải',
            'name_of_partner_id' => 'Đối tác vận tải',
            'name_of_location_destination_id' => 'Điểm nhận hàng',
            'name_of_location_arrival_id' => 'Điểm trả hàng',
            'name_of_client_id' => 'Khách hàng',
            'reason' => 'Lý do'
        ]
    ],
    // order
    'merge_order' => [
        'name' => 'Ghép đơn hàng',
        'attributes' => [
            'id' => 'Mã',
            'title' => 'Tên đơn hàng',
            'type' => 'Loại',
            'order_code' => 'Mã hệ thống',
            'order_no' => 'Số đơn hàng',
            'order_purchasing_no' => 'Số thứ tự đơn hàng',
            'order_date' => 'Ngày đặt hàng',
            'bill_no' => 'Số hóa đơn',
            'customer_id' => 'Khách hàng',
            'name_of_customer_id' => 'Khách hàng',
            'customer_name' => 'Tên khách hàng/người đại diện',
            'information' => 'Thông tin chung',
            'communication' => 'Thông tin',
            'customer_and_status' => 'Trạng thái đơn hàng',
            'etd' => 'Địa điểm nhận hàng',
            'eta' => 'Địa điểm trả hàng',
            'contact_name_destination' => 'Tên liên hệ nhận hàng',
            'contact_mobile_no_destination' => 'Điện thoại liên hệ nhận hàng',
            'contact_email_destination' => 'Email',
            'contact_name_arrival' => 'Tên liên hệ trả hàng',
            'contact_mobile_no_arrival' => 'Điện thoại liên hệ trả hàng',
            'contact_email_arrival' => 'Email',
            'goods_type' => 'Loại hàng hóa',
            'goods_unit' => 'Đơn vị',
            'goods_unit_id' => 'Mã đơn vị hàng hóa',
            'ETD' => 'Ngày nhận hàng',
            'ETA' => 'Ngày trả hàng',
            'destination_info' => 'Thông tin nhận hàng',
            'arrival_destination_info' => 'Thông tin nhận trả hàng',
            'arrival_info' => 'Thông tin trả hàng',
            'informative_destination' => 'Thông tin bổ sung',
            'informative_arrival' => 'Thông tin bổ sung',
            'good_details' => 'Mô tả hàng hóa',

            'amount' => 'Cước phí vận chuyển',
            'quantity' => 'Tổng số lượng',
            'volume' => 'Tổng thể tích',
            'weight' => 'Tổng trọng lượng',
            'precedence' => 'Độ ưu tiên',
            'status' => 'Trạng thái',
            'route_create' => 'Tự động tạo chuyến xe mới',
            'vehicle' => 'Xe',
            'choose_vehicle_driver' => 'Thông tin xe và tài xế',
            'goods_info' => 'Thông tin hàng hóa',
            'files_info' => 'Thông tin đính kèm',
            'history_info' => 'Lịch sử đơn hàng',
            'order_review' => 'Đánh giá từ khách hàng',
            'note_info' => 'Thông tin bổ sung',
            'primary_driver' => 'Tài xế',
            'secondary_driver' => 'Phụ xe',
            'location_destination' => 'Điểm nhận hàng',
            'location_arrival' => 'Điểm trả hàng',
            'reason' => 'Chú thích',
            'ETD_date' => 'Ngày nhận hàng',
            'ETD_time' => 'Giờ nhận hàng',
            'ETA_date' => 'Ngày trả hàng',
            'ETA_time' => 'Giờ trả hàng',

            'ETD_date_reality' => 'Ngày nhận hàng thực tế',
            'ETD_time_reality' => 'Giờ nhận hàng thực tế',
            'ETA_date_reality' => 'Ngày trả hàng thực tế',
            'ETA_time_reality' => 'Giờ trả hàng thực tế',

            'ETD_location' => 'Địa điểm đi',
            'ETA_location' => 'Địa điểm đến',
            'customer_mobile_no' => 'Số điện thoại',
            'location_destination_id' => 'Điểm nhận hàng',
            'location_arrival_id' => 'Điểm trả hàng',
            'vehicle_id' => 'Xe',
            'primary_driver_id' => 'Tài xế',
            'insured_goods' => 'Hàng hoá có bảo hiểm',
            'acronym_insured_goods' => 'HH có bảo hiểm',
            'name_of_location_destination_id' => 'Điểm nhận hàng',
            'name_of_location_arrival_id' => 'Điểm trả hàng',
            'loading_destination' => 'Bốc xếp hàng hoá',
            'loading_arrival' => 'Bốc xếp hàng hoá',
            'loading_destination_fee' => 'Phí bốc xếp hàng hoá nhận hàng',
            'loading_arrival_fee' => 'Phí bốc xếp hàng hoá trả hàng',
            'code_config' => 'Dạng mã',
            'ETD_reality' => 'Ngày nhận hàng thực tế',
            'ETA_reality' => 'Ngày trả hàng thực tế',
            'date_reality' => 'Ngày thực tế',
            'time_reality' => 'Giờ thực tế',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'note' => 'Chú thích',
            'route' => 'Chuyến xe',
            'choose-route' => 'Chuyến xe',
            'quota' => 'Bảng định mức chi phí',
            'extend_cost' => 'Chi phí phát sinh',
            'currency_id' => 'Tiền tệ',
            'remark' => 'Ghi chú',

            'add_goods' => 'Thêm hàng hóa',
            'list_goods' => 'Danh sách hàng hóa',
            'total_weight' => 'Tổng khối lượng',
            'total_volume' => 'Tổng thể tích',
            'acronym_total_weight' => 'Khối lượng (kg)',
            'acronym_total_volume' => 'Thể tích (m3)',
            'goods_description' => 'Diễn giải',
            'goods_quantity' => 'Số lượng',
            'goods_insured' => 'Có bảo hiểm',
            'is_collected_documents' => 'Bắt buộc thu chứng từ',
            'status_collected_documents' => 'Tình trạng chứng từ',
            'time_collected_documents' => 'Giờ thu chứng từ',
            'date_collected_documents' => 'Ngày thu chứng từ',
            'time_collected_documents_reality' => 'Giờ thu chứng từ thực tế',
            'date_collected_documents_reality' => 'Ngày thu chứng từ thực tế',
            'document_type' => 'Loại chứng từ',
            'document_note' => 'Ghi chú',
            'num_of_document_page' => 'Số tờ chứng từ',
            'datetime_collected_documents' => 'Thời hạn thu chứng từ',
            'datetime_collected_documents_reality' => 'Thời gian thu chứng từ thực tế',
            'documents_info' => 'Thông tin chứng từ',
            'commission_amount' => 'Phí hoa hồng',
            'commission_type' => 'Loại hoa hồng',
            'commission_value' => 'Tỷ lệ hoa hồng',
            'order_customer' => 'Đơn đặt hàng',
            'cod_amount' => 'Tiền thu hộ khách hàng',
            'cod_currency_id' => 'Tiền tệ tiền thu hộ',

            'payment_info' => 'Thông tin thanh toán',
            'payment_type' => 'Hình thức thanh toán',
            'payment_user_id' => 'Người chịu trách nhiệm thanh toán',
            'name_of_payment_user_id' => 'Người chịu trách nhiệm thanh toán',
            'goods_amount' => 'Giá trị hàng hóa',
            'vat' => 'VAT',
            'anonymous_amount' => 'Cước gửi',
            'final_amount' => 'Doanh thu',
            'name_of_location_destination_code' => 'Điểm nhận hàng',
            'name_of_location_arrival_code' => 'Điểm trả hàng',
            'is_merge_item' => 'Là hàng ghép hay không',
            'gps_distance' => 'Khoảng cách GPS',
            'vin_no' => 'Số khung',
            'model_no' => 'Số model'

        ]
    ],
    // Chủ hàng
    'customer' => [
        'name' => 'Chủ hàng',
        'attributes' => [
            'adminUser.username' => 'Tên đăng nhập',
            'adminUser.email' => 'Email',
            'adminUser.password' => 'Mật khẩu',
            'avatar' => 'Ảnh đại diện',
            'password_note_text' => 'Để trống nếu không thay đổi',
            'adminUser.password_confirmation' => 'Xác nhận mật khẩu',
            'customer_code' => 'Mã chủ hàng',
            'full_name' => 'Tên chủ hàng',
            'mobile_no' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'current_address' => 'Địa chỉ',
            'sex' => 'Giới tính',
            'birth_date' => 'Ngày sinh',
            'note' => 'Ghi chú',
            'location' => 'Địa điểm',
            'type' => 'Loại khách hàng',
            'delegate' => 'Người đại diện',
            'tax_code' => 'Mã số thuế',
            'province_id' => 'Tỉnh - Thành phố',
            'district_id' => 'Quận - Huyện',
            'ward_id' => 'Xã - Phường',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'username' => 'Tài khoản',
            'email' => 'Email',
            'customer_group' => 'Nhóm khách hàng',
            'parent_id' => 'Chủ hàng',
            'name_of_parent_id' => 'Chủ hàng'

        ],
    ],
    // Khách hàng
    'client' => [
        'name' => 'Khách hàng',
        'attributes' => [
            'adminUser.username' => 'Tên đăng nhập',
            'adminUser.email' => 'Email',
            'adminUser.password' => 'Mật khẩu',
            'avatar' => 'Ảnh đại diện',
            'password_note_text' => 'Để trống nếu không thay đổi',
            'adminUser.password_confirmation' => 'Xác nhận mật khẩu',
            'customer_code' => 'Mã khách hàng',
            'full_name' => 'Tên khách hàng',
            'mobile_no' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'current_address' => 'Địa chỉ',
            'sex' => 'Giới tính',
            'birth_date' => 'Ngày sinh',
            'note' => 'Ghi chú',
            'location' => 'Địa điểm',
            'type' => 'Loại khách hàng',
            'delegate' => 'Người đại diện',
            'tax_code' => 'Mã số thuế',
            'province_id' => 'Tỉnh - Thành phố',
            'district_id' => 'Quận - Huyện',
            'ward_id' => 'Xã - Phường',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'username' => 'Tài khoản',
            'email' => 'Email',
            'customer_group' => 'Nhóm khách hàng',
            'parent_id' => 'Chủ hàng',
            'name_of_parent_id' => 'Chủ hàng'

        ],
    ],

    // Hợp đồng
    'contract' => [
        'name' => 'Hợp đồng',
        'attributes' => [
            'contract_no' => 'Số hợp đồng',
            'customer_id' => 'Khách hàng',
            'name_of_customer_id' => 'Khách hàng',

            'issue_date' => 'Ngày ký',
            'expired_date' => 'Ngày hết hiệu lực',
            'type' => 'Loại hợp đồng',
            'status' => 'Trạng thái',
            'note' => 'Ghi chú',
            'file' => 'File',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ],
    ],

    // Danh bạ
    'contact' => [
        'name' => 'Danh bạ',
        'attributes' => [
            'contact_name' => 'Tên liên hệ',
            'phone_number' => 'Số điện thoại',
            'email' => 'Email',
            'active' => 'Đang hoạt động',
            'location_id' => 'Địa chỉ',
            'full_address' => 'Địa chỉ',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ],
    ],

    // Tiền tệ
    'currency' => [
        'name' => 'Tiền tệ',
        'attributes' => [
            'currency_code' => 'Mã đơn vị tiền tệ',
            'currency_name' => 'Tên đơn vị tiền tệ',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ],
    ],
    // good type
    'goods_type' => [
        'name' => 'Phương tiện',
        'attributes' => [
            'title' => 'Tên phương tiện',
            'note' => 'Ghi chú',
            'code' => 'Mã phương tiện',
            'goods_group_id' => 'Nhóm phương tiện',
            'name_of_goods_group_id' => 'Nhóm phương tiện',
            'volume' => 'Dung tích (m3)',
            'weight' => 'Tải trọng (kg)',
            'goods_unit_id' => 'Đơn vị phương tiện',
            'name_of_goods_unit_id' => 'Đơn vị phương tiện',
            'file' => 'Tệp tin',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'name_of_customer_id' => 'Chủ hàng',
            'in_amount' => 'Giá nhập',
            'out_amount' => 'Giá bán'
        ]
    ],
    // good unit
    'goods_unit' => [
        'name' => 'Đơn vị hàng hóa',
        'attributes' => [
            'title' => 'Tên đơn vị hàng hóa',
            'note' => 'Ghi chú',
            'code' => 'Mã đơn vị hàng hoá',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'name_of_customer_id' => 'Chủ hàng',
        ]
    ],
    //Contract type
    'contract_type' => [
        'name' => 'Loại hợp đồng',
        'attributes' => [
            'name' => 'Tên',
            'description' => 'Mô tả',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ],
    ],
    //Alert logs
    'alert_log' => [
        'name' => 'Thông báo',
        'attributes' => [
            'name' => 'Tên',
            'title' => 'Tiêu đề',
            'content' => 'Nội dung',
            'alert_type' => 'Loại thông báo',
            'date_to_send' => 'Ngày gửi',
            'time_to_send' => 'Thời gian gửi',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ],
    ],
    'notification_log' => [
        'name' => 'Thông báo',
        'attributes' => [
            'name' => 'Tên',
            'title' => 'Tiêu đề',
            'content' => 'Nội dung',
            'alert_type' => 'Loại thông báo',
            'date_to_send' => 'Ngày gửi',
            'time_to_send' => 'Thời gian gửi',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ],
    ],
    'notification' => [
        'name' => 'Thông báo của bạn',
        'attributes' => [
            'name' => 'Tên',
            'title' => 'Tiêu đề',
            'content' => 'Nội dung',
            'alert_type' => 'Loại thông báo',
            'date_to_send' => 'Ngày gửi',
            'time_to_send' => 'Thời gian gửi'
        ],
    ],
    'system_code_config' => [
        'name' => 'Mã hệ thống',
        'attributes' => [
            'prefix' => 'Tiền tố',
            'suffix_length' => 'Độ dài phần số',
            'preview' => 'Hiển thị',
            'type' => 'Loại',
            'is_generate_time' => 'Có sinh thời gian không',
            'time_format' => 'Định dạng ngày',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ],
    ],
    // route
    'route' => [
        'name' => 'Chuyến xe',
        'attributes' => [
            'route_code' => 'Mã chuyến',
            'name' => 'Tên chuyến',
            'vehicle' => 'Xe',
            'order' => 'Đơn hàng',
            'locations' => 'Lộ trình',
            'ETD' => 'TG dự kiến xuất phát',
            'ETD_date' => 'Ngày nhận hàng',
            'ETD_time' => 'Giờ trả hàng',
            'ETA' => 'TG dự kiến đến',
            'ETA_date' => 'Ngày trả hàng',
            'ETA_time' => 'Giờ trả hàng',
            'gps_distance' => 'Km GPS',
            'primary_driver' => 'Tài xế',
            'cost' => 'Chi phí',
            'total_cost' => 'Định mức chi phí',
            'order_cost' => 'Chi phí phát sinh từ đơn hàng',
            'order_cost_compact' => 'CP phát sinh từ ĐH',
            'other_cost' => 'Chi phí khác',
            'final_cost' => 'Tổng chi phí',
            'quota_id' => 'Bảng định mức chi phí',
            'vehicle_id' => 'Xe',
            'driver_id' => 'Tài xế',
            'ETD_date_reality' => 'Ngày nhận hàng thực tế',
            'ETA_date_reality' => 'Ngày trả hàng thực tế',
            'ETD_reality' => 'TG nhận hàng thực tế',
            'ETA_reality' => 'TG trả hàng thực tế',
            'route_status' => 'Trạng thái',
            'amount_admin' => 'Chi phí theo định mức',
            'amount_driver' => 'Chi phí thực tế',
            'amount_approval' => 'Chi phí cuối cùng',
            'route_note' => 'Chú thích',
            'route_file' => 'Đính kèm',
            'location_destination_id' => 'Điểm đầu',
            'location_arrival_id' => 'Điểm cuối',
            'name_of_location_destination_id' => 'Điểm đầu',
            'name_of_location_arrival_id' => 'Điểm cuối',

            'name_of_province_destination_id' => 'Tỉnh/thành phố điểm đầu',
            'name_of_province_arrival_id' => 'Tỉnh/thành phố điểm cuối',

            'name_of_district_destination_id' => 'Quận/Huyện điểm đầu',
            'name_of_district_arrival_id' => 'Quận/Huyện điểm cuối',

            'is_approved' => 'Trạng thái phê duyệt',
            'approved_id' => 'Người phê duyệt',
            'approved_date' => 'Thời gian phê duyệt',
            'approved_history' => 'Lịch sử phê duyệt',
            'approved_note' => 'Ghi chú phê duyệt',
            'description' => 'Diễn giải',
            'total_quota' => 'Tổng định mức chi phí (VND)',
            'quota' => 'Bảng định mức chi phí',
            'cost_info' => 'Thông tin chi phí',
            'route_info' => 'Thông tin lộ trình',
            'capacity_weight_ratio' => 'Tỉ lệ loading khối lượng',
            'capacity_volume_ratio' => 'Tỉ lệ loading thể tích',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'total_amount' => 'Tổng doanh thu',
            'count_order' => 'Tổng số đơn hàng',
            'order_codes' => 'Danh sách đơn hàng',
            'total_cost_reality' => 'Tổng chi phí thực tế',
            'order_notes' => 'Chú thích đơn hàng',
            'name_of_partner_id' => 'Đối tác vận tải'
        ],
        'price' => [
            "vehicle_group_id" => 'Chủng loại xe',
            'weight' => 'Tổng khối lượng đơn hàng',
            "volume" => "Tổng thể tích đơn hàng",
            "operator" => 'Toán tử',
            "location_group_destination_title" => 'Nhóm địa điểm nhận',
            "location_group_arrival_title" => 'Nhóm địa điểm trả',
            "amount" => "Số tiền",
            "equal" => "bằng",
            "not
            ual" => "không bằng",
            "greater" => "lớn hơn",
            "less" => "nhỏ hơn",
            "greater_equal" => "lớn hơn hoặc bằng",
            "less_equal" => "nhỏ hơn hoặc bằng",
            "in" => "trong khoảng",
            'goods_type_id' => 'Hàng hoá',
            'quantity' => 'Số lượng',
            'distance' => ' Khoảng cách',
            'point_charge' => "Phí rớt điểm",
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ]
    ],
    // DHKH
    'order_customer' => [
        'name' => 'Đơn đặt hàng',
        'attributes' => [
            'code' => 'Mã đơn đặt hàng',
            'name' => 'Tên đơn đặt hàng',
            'order_no' => 'Số đơn hàng',
            'order_date' => 'Ngày đặt hàng',
            'order' => 'Đơn hàng',
            'locations' => 'Lộ trình',
            'customer_id' => 'Chủ hàng',
            'name_of_customer_id' => 'Chủ hàng',
            'customer_code' => 'Chủ hàng',
            'customer_name' => 'Tên người đại diện',
            'customer_mobile_no' => 'Số điện thoại chủ hàng',
            'ETD_date' => 'Ngày nhận hàng',
            'ETA_date' => 'Ngày trả hàng',
            'ETD_time' => 'Giờ nhận hàng',
            'ETA_time' => 'Giờ trả hàng',
            'ETD_date_reality' => 'Ngày nhận hàng thực tế',
            'ETA_date_reality' => 'Ngày hoàn thành thực tế',
            'status' => 'Trạng thái',
            'destination_location_title' => 'Điểm nhận hàng',
            'arrival_location_title' => 'Điểm trả hàng',
            'location_destination' => 'Điểm nhận hàng',
            'name_of_location_destination_id' => 'Điểm nhận hàng',
            'name_of_location_arrival_id' => 'Điểm trả hàng',

            'location_arrival' => 'Điểm trả hàng',
            'amount' => 'Doanh thu (VND)',
            'commission_amount' => 'Phí hoa hồng (VND)',
            'commission_type' => 'Loại hoa hồng',
            'commission_value' => 'Tỷ lệ hoa hồng',
            'final_amount' => 'Doanh thu  (VND)',
            'vehicle_group_id' => 'Chủng loại xe',
            'vehicle_number' => 'Số lượng xe',
            'route_number' => 'Số lượng chuyến',
            'quantity' => 'Tổng số lượng',
            'quantity_out' => 'Tổng số lượng đã xuất',
            'weight' => 'Tổng khối lượng (kg)',
            'volume' => 'Tổng dung tích (m3)',
            'distance' => 'Quãng đường (km)',
            'customer_full_name' => 'Chủ hàng',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',

            'payment_type' => 'Hình thức thanh toán',
            'payment_user_id' => 'Người chịu trách nhiệm thanh toán',
            'name_of_payment_user_id' => 'Người chịu trách nhiệm thanh toán',
            'goods_amount' => 'Giá trị hàng hóa',
            'vat' => 'VAT',
            'anonymous_amount' => 'Cước gửi',
            'total_order' => 'Tổng số đơn hàng',
            'count_order' => 'Tổng số đơn hàng',
            'vin_nos' => 'Số khung',
            'model_nos' => 'Số model',

            'name_of_province_destination_id' => 'Tỉnh/thành phố điểm nhận hàng',
            'name_of_province_arrival_id' => 'Tỉnh/thành phố điểm trả hàng',

            'name_of_district_destination_id' => 'Quận/Huyện điểm nhận hàng',
            'name_of_district_arrival_id' => 'Quận/Huyện điểm trả hàng',
            'goods_detail' => 'Mô tả hàng hóa',
            'status_goods' => 'Tình trạng xuất hàng',
            'name_of_client_id' => 'Khách hàng',
            'amount_estimate' => 'Doanh thu tự động (VND)'
        ]
    ],
    'order-customer' => [
        'name' => 'Đơn hàng đặt hàng',
    ],

    // quota
    'quota' => [
        'name' => 'Bảng định mức chi phí',
        'attributes' => [
            'quota_code' => 'Mã định mức',
            'name' => 'Tên',
            'vehicle_group_id' => 'Chủng loại xe',
            'name_of_vehicle_group_id' => 'Chủng loại xe',
            'locations' => 'Lộ trình',
            'cost' => 'Chi phí',
            'total_cost' => 'Tổng chi phí (VND)',
            'location_destination_id' => 'Điểm nhận hàng',
            'location_arrival_id' => 'Điểm trả hàng',
            'name_of_location_destination_id' => 'Điểm nhận hàng',
            'name_of_location_arrival_id' => 'Điểm trả hàng',
            'title' => 'Lộ trình',
            'distance' => 'Khoảng cách (km)',
            'location_destination_group_id' => 'Nhóm điểm nhận hàng',
            'location_arrival_group_id' => 'Nhóm điểm trả hàng',
            'name_of_location_destination_group_id' => 'Nhóm điểm nhận hàng',
            'name_of_location_arrival_group_id' => 'Nhóm điểm trả hàng',
        ]
    ],
    'report' => [
        'name' => 'Báo cáo',
        'attributes' => [
            'operator_report' => 'Tình trạng hoạt động',
            'journey_report' => 'Hành trình xe',
            'customer_report' => 'Doanh thu theo KH',
            'vehicle_report' => 'Năng suất xe',
            'vehicle_team_report' => 'Hoạt động theo đội TX',

        ]
    ],
    'report_vehicle' => [
        'name' => 'Xe',
        'attributes' => [
            'title' => 'Báo cáo theo Xe',
            'reg_no' => 'Biển số',
            'driver_names' => 'Tài xế',
            'total_order' => 'Tổng số đơn',
            'total_route' => 'Tổng số chuyến',
            'total_route_average_per_day' => 'Số chuyến trung bình/ngày',
            'total_order_on_time' => 'Số đơn đúng giờ',
            'total_order_late' => 'Số đơn muộn giờ',
            'ratio_order' => 'Tỷ trọng (Đúng giờ/Tổng số đơn hoàn thành)',
            'distance' => 'Tổng km',
            'distance_average_per_day' => 'Số km trung bình',
            'total_amount' => 'Doanh thu',
            'total_cost' => 'Tổng chi phí',
            'total_commission' => 'Tổng hoa hồng',
            'total_cod' => 'Tổng COD',
            'revenue' => 'Lợi nhuận',
            'ratio_revenue' => 'Tỷ suất lợi nhuận (Lợi nhuận/Doanh thu)',
        ]
    ],
    'report_driver' => [
        'name' => 'Tài xế',
        'attributes' => [
            'title' => 'Báo cáo theo Tài xế'
        ]
    ],
    'report_client' => [
        'name' => 'Khách hàng',
        'attributes' => [
            'title' => 'Báo cáo theo Khách hàng'
        ]
    ],

    // auditing
    'auditing' => [
        'name' => 'Nhật ký hoạt động',
        'attributes' => [
            'username' => 'Tên Quản trị',
            'time' => 'Thời gian',
            'old_values' => 'Giá trị cũ',
            'new_values' => 'Giá trị mới',
        ],
        'actions' => [
            'created' => 'Thêm',
            'updated' => 'Sửa',
            'deleted' => 'Xóa',
        ],
    ],
    //Schedule report
    'report_schedule' => [
        'name' => 'Đặt lịch Thông báo',
        'attributes' => [
            'description' => 'Mô tả',
            'schedule_type' => 'Loại lịch thông báo',
            'time_to_send' => 'Thời gian gửi',
            'date_from' => 'Ngày có hiệu lực',
            'date_to' => 'Ngày kết thúc',
            'email' => 'Email'
        ],
    ],
    'system_config' => [
        'name' => 'Cấu hình hệ thống',
        'attributes' => [
            'key' => 'Khóa',
            'value' => 'Giá trị',
            'description' => 'Mô tả',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ],
    ],

    // journey
    'journey' => [
        'name' => 'Hành trình',
        'attributes' => [
            'map' => 'Bản đồ',
            'set' => 'Thiết đặt',
            'current' => 'Vị trí hiện tại',
            'description' => 'Click chuột phải để thiết lập mặc định',
        ],
    ],

    // document
    'document' => [
        'name' => 'Chứng từ',
        'attributes' => [
            'order_code' => 'Mã đơn hàng',
            'order_no' => 'Số đơn hàng',
            'customer' => 'Khách hàng',
            'vehicle' => 'Xe',
            'driver' => 'Tài xế',
            'is_collected_documents' => 'Bắt buộc thu chứng từ',
            'status_collected_documents' => 'Tình trạng chứng từ',
            'time_collected_documents' => 'Giờ thu chứng từ',
            'date_collected_documents' => 'Ngày thu chứng từ',
            'datetime_collected_documents' => 'Thời hạn thu chứng từ',
            'time_collected_documents_reality' => 'Giờ thu chứng từ thực tế',
            'date_collected_documents_reality' => 'Ngày thu chứng từ thực tế',
            'num_of_document_page' => 'Số tờ chứng từ',
            'document_type' => 'Loại chứng từ',
            'document_note' => 'Ghi chú',
            'order_review' => 'Đánh giá từ khách hàng',

            'order_date' => 'Ngày đặt hàng',
            'commission_amount' => 'Phí hoa hồng',
            'commission_type' => 'Loại hoa hồng',
            'commission_value' => 'Tỷ lệ hoa hồng',
            'final_amount' => 'Doanh thu',
            'order_customer' => 'Đơn đặt hàng',
            'cod_amount' => 'Tiền thu hộ khách hàng',
            'contact_name_destination' => 'Tên liên hệ nhận hàng',
            'contact_mobile_no_destination' => 'Điện thoại liên hệ nhận hàng',
            'contact_email_destination' => 'Email',
            'contact_name_arrival' => 'Tên liên hệ trả hàng',
            'contact_mobile_no_arrival' => 'Điện thoại liên hệ trả hàng',
            'contact_email_arrival' => 'Email',
            'amount' => 'Cước phí vận chuyển',
            'quantity' => 'Số lượng',
            'volume' => 'Thể tích',
            'weight' => 'Trọng lượng',
            'precedence' => 'Độ ưu tiên',
            'status' => 'Trạng thái',
            'ETD' => 'Ngày nhận hàng',
            'ETA' => 'Ngày trả hàng',
            'bill_no' => 'Số hóa đơn',
            'customer_id' => 'Khách hàng',
            'name_of_customer_id' => 'Khách hàng',
            'customer_name' => 'Tên khách hàng/người đại diện',
            'note_info' => 'Thông tin bổ sung',
            'primary_driver' => 'Tài xế',
            'secondary_driver' => 'Phụ xe',
            'location_destination' => 'Điểm nhận hàng',
            'location_arrival' => 'Điểm trả hàng',
            'reason' => 'Chú thích',
            'ETD_date' => 'Ngày nhận hàng',
            'ETD_time' => 'Giờ nhận hàng',
            'ETA_date' => 'Ngày trả hàng',
            'ETA_time' => 'Giờ trả hàng',

            'ETD_date_reality' => 'Ngày nhận hàng thực tế',
            'ETD_time_reality' => 'Giờ nhận hàng thực tế',
            'ETA_date_reality' => 'Ngày trả hàng thực tế',
            'ETA_time_reality' => 'Giờ trả hàng thực tế',

            'ETD_location' => 'Địa điểm đi',
            'ETA_location' => 'Địa điểm đến',
            'customer_mobile_no' => 'Số điện thoại',
            'location_destination_id' => 'Điểm nhận hàng',
            'location_arrival_id' => 'Điểm trả hàng',
            'name_of_location_destination_id' => 'Điểm nhận hàng',
            'name_of_location_arrival_id' => 'Điểm trả hàng',
            'vehicle_id' => 'Xe',
            'primary_driver_id' => 'Tài xế',
            'insured_goods' => 'Hàng hoá có bảo hiểm',
            'acronym_insured_goods' => 'HH có bảo hiểm',

            'loading_destination' => 'Bốc xếp hàng hoá',
            'loading_arrival' => 'Bốc xếp hàng hoá',
            'loading_destination_fee' => 'Phí bốc xếp hàng hoá nhận hàng',
            'loading_arrival_fee' => 'Phí bốc xếp hàng hoá trả hàng',
            'code_config' => 'Dạng mã',
            'ETD_reality' => 'Ngày nhận hàng thực tế',
            'ETA_reality' => 'Ngày trả hàng thực tế',
            'date_reality' => 'Ngày thực tế',
            'time_reality' => 'Giờ thực tế',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'note' => 'Chú thích',
            'route' => 'Chuyến xe',
        ]
    ],

    // Mẫu
    'template' => [
        'name' => 'Mẫu',
        'attributes' => [
            'title' => 'Tên',
            'type' => 'Loại',
            'export_type' => 'Kiểu xuất',
            'file' => 'Tệp tin',
            'file_id' => 'Tệp tin',
            'description' => 'Mô tả',
            'is_print_empty_cost' => 'Có in chi phí rỗng hay không?',
            'is_print_empty_goods' => 'Có in hàng hóa rỗng hay không?',
            'download' => 'Tải',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'list_item' => 'Danh sách đối tượng muốn in'
        ]
    ],

    // Loại địa điểm
    'location_type' => [
        'name' => 'Loại địa điểm',
        'attributes' => [
            'title' => 'Tên',
            'code' => 'Mã',
            'description' => 'Mô tả',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'name_of_customer_id' => 'Chủ hàng'
        ]
    ],

    // Loại địa điểm
    'location_group' => [
        'name' => 'Nhóm địa điểm',
        'attributes' => [
            'title' => 'Tên',
            'code' => 'Mã',
            'description' => 'Mô tả',
            'location_ids' => 'Danh sách địa điểm',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'name_of_customer_id' => 'Chủ hàng',
        ]
    ],

    /* Nhóm khách hàng*/
    'customer_group' => [
        'name' => 'Nhóm khách hàng',
        'attributes' => [
            'name' => 'Tên nhóm khách hàng',
            'customer_ids' => 'Danh sách khách hàng',
            'code' => 'Mã nhóm khách hàng',
            'customer_names' => 'Khách hàng',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ]
    ],
    /* Đội xe */

    /* Lịch sử import */
    'import_history' => [
        'name' => 'Lịch sử nhập',
        'attributes' => [
            'file_name' => 'Tên file',
            'type' => 'Kiểu nhập',
            'success_record' => 'Thành công',
            'error_record' => 'Không thành công',
            'memo' => 'Ghi chú',
            'type_create' => 'Thêm mới',
            'type_update' => 'Cập nhật',
            'module' => 'Tính năng',
            'username' => 'Người cập nhật',
            'download' => 'Tải',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa'
        ]
    ],
    /* Báo giá*/
    'price_quote' => [
        'name' => 'Báo giá',
        'attributes' => [
            'code' => 'Mã báo giá',
            'name' => 'Tên báo giá',
            'description' => 'Mô tả',
            'date_from' => 'Ngày bắt đầu',
            'date_to' => 'Ngày kết thúc',
            'type' => 'Loại báo giá',
            'customerApply' => 'Khách hàng áp dụng',
            'isDefault' => 'Là báo giá mặc định',
            'customer_groups' => 'Nhóm khách hàng',
            'isApplyAll' => 'Áp dụng tất cả khách hàng',
            'point_charge_info' => 'Thông tin phí rớt điểm',
            'formula_info' => 'Thông tin công thức',
            'system_info' => 'Thông tin hệ thống',
            'general_info' => 'Thông tin chung',
            'empty_formula' => 'Không có công thức',
            'empty_point_charge' => 'Không có phí rớt điểm',
            'location_group_destination' => 'Nhóm địa điểm nhận',
            'location_group_arrival' => 'Nhóm địa điểm trả',
            'operator' => 'Điều kiện',
            'value' => 'Giá trị',
            'price' => 'Số tiền',
            'isDistance' => 'Áp dụng công thức dựa theo số km',
        ]
    ],

    // Doanh thu
    'revenue' => [
        'name' => 'Doanh thu'
    ],

    // Chi phí
    'cost' => [
        'name' => 'Chi phí'
    ],

    // Nhật ký login
    'activity_log' => [
        'name' => 'Nhật ký hoạt động',
        'attributes' => [
            'username' => 'Tên đăng nhập',
            'email' => 'Email',
            'description' => 'Mô tả',
            'created_at' => 'Thời gian',
        ],
    ],

    // Mẫu
    'template_payment' => [
        'name' => 'Mẫu chi phí',
        'attributes' => [
            'title' => 'Tên',
            'description' => 'Mô tả',
            'matching_column_index' => 'Cột mã chuyến',
            'header_row_index' => 'Dòng bắt đầu dữ liệu',
            'file' => 'Tệp tin',
            'receipt_payment' => 'Chi phí',
            'column_index' => 'Cột',
            'empty_mapping' => 'Bạn chưa nhập ánh xạ chi phí với tệp',
            'download' => 'Tải',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ],
    ],
    /* Tính lương*/
    'payroll' => [
        'name' => 'Bảng tính lương TX',
        'attributes' => [
            'code' => 'Mã bảng tính lương',
            'name' => 'Tên bảng tính lương',
            'description' => 'Mô tả',
            'date_from' => 'Ngày bắt đầu',
            'date_to' => 'Ngày kết thúc',
            'type' => 'Loại bảng tính lương',
            'customerApply' => 'Khách hàng áp dụng',
            'isDefault' => 'Là bảng tính lương mặc định',
            'customer_groups' => 'Nhóm khách hàng',
            'isApplyAll' => 'Áp dụng tất cả khách hàng',
            'formula_info' => 'Thông tin công thức',
            'system_info' => 'Thông tin hệ thống',
            'general_info' => 'Thông tin chung',
            'empty_formula' => 'Không có công thức',
            'location_group_destination' => 'Nhóm địa điểm nhận',
            'location_group_arrival' => 'Nhóm địa điểm trả',
            'price' => 'Số tiền',
            'vehicle_group' => 'Chủng loại xe'
        ]
    ],
    /* Đơn giá đơn hàng*/
    'order_price' => [
        'name' => 'Đơn giá đơn hàng',
        'attributes' => [
            'order_code' => 'Mã đơn hàng',
            'order_id' => 'Mã đơn hàng',
            'name_of_price_quote_id' => 'Báo giá',
            'price_quote_id' => 'Báo giá',
            'description' => 'Mô tả',
            'is_approved' => 'Đã phê duyệt',
            'amount' => 'Cước phí vận chuyển',
            'location_destination_id' => 'Điểm nhận hàng',
            'location_arrival_id' => 'Điểm trả hàng',
            'name_of_location_destination_id' => 'Điểm nhận hàng',
            'name_of_location_arrival_id' => 'Điểm trả hàng',


            'download' => 'Tải',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa'
        ]
    ],

    // Chi phí
    'company_info' => [
        'name' => 'Thông tin công ty',
        'attributes' => [
            'name' => 'Tên',
            'address' => 'Địa chỉ',
            'mobile_no' => 'Số điện thoại',
            'email' => 'Email',
        ]
    ],

    // Phụ tùng
    'accessory' => [
        'name' => 'Quản lý phụ tùng',
        'attributes' => [
            'name' => 'Tên phụ tùng',
            'description' => 'Mô tả',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa'
        ]
    ],
    // Phiếu sửa chữa
    'repair_ticket' => [
        'name' => 'Phiếu sửa chữa',
        'attributes' => [
            'code' => 'Mã phiếu',
            'name' => 'Tên phiếu',
            'driver_id' => 'Tài xế',
            'vehicle_id' => 'Xe',
            'name_of_driver_id' => 'Tài xế',
            'name_of_vehicle_id' => 'Xe',
            'description' => 'Mô tả',
            'repair_date' => 'Ngày sửa chữa',
            'amount' => 'Tổng tiền',
            'is_approved' => 'Đã phê duyệt',
            'approved_id' => 'Người phê duyệt',
            'approved_date' => 'Ngày phê duyệt',
            'approved_note' => 'Ghi chú phê duyệt',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',

            'item' => [
                'accessory_id' => 'Phụ tùng',
                'quantity' => 'Số lượng',
                'price' => 'Đơn giá',
                'amount' => 'Thành tiền',
                'next_repair_type' => 'Loại bảo dưỡng',
                'next_repair_date' => 'Ngày bảo dưỡng tiếp theo',
                'next_repair_distance' => 'Số km bảo dưỡng',
            ]
        ]
    ],
    // Dữ liệu mặc định của KH
    'customer_default_data' => [
        'name' => 'Dữ liệu mặc định KH',
        'attributes' => [
            'code' => 'Mã',
            'customer_id' => 'Chủ hàng',
            'name_of_customer_id' => 'Chủ hàng',
            'system_code_config_id' => 'Áp dụng cho Mã đơn',
            'name_of_system_code_config_id' => 'Áp dụng cho Mã đơn',
            'location_destination_ids' => 'Địa điểm nhận hàng',
            'location_arrival_ids' => 'Địa điểm trả hàng',
            'location_destination_id' => 'Địa điểm nhận hàng',
            'location_arrival_id' => 'Địa điểm trả hàng',
            'name_of_location_destination_ids' => 'Địa điểm nhận hàng',
            'name_of_location_arrival_ids' => 'Địa điểm trả hàng',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
            'client_id' => 'Khách hàng',
        ]
    ],
    // Mẫu
    'template_excel_converter' => [
        'name' => 'Mẫu chuyển đổi Excel',
        'attributes' => [
            'title' => 'Tên',
            'description' => 'Mô tả',
            'header_row_index' => 'Dòng bắt đầu dữ liệu',
            'max_row' => 'Số dòng tối đa',
            'is_use_convert_sheet' => 'Có dùng sheet chuyển đổi không',
            'field' => 'Trường',
            'file' => 'Tệp tin',
            'formula' => 'Công thức',
            'column_index' => 'Cột',
            'empty_mapping' => 'Bạn chưa nhập ánh xạ chi phí với tệp',
            'download' => 'Tải',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',
        ]
    ],
    /* Nhóm hàng hoá */
    'goods_group' => [
        'name' => 'Hãng xe',
        'attributes' => [
            'code' => 'Mã hãng xe',
            'name' => 'Tên hãng xe',
            'parent_id' => 'Nhóm hãng xe',
        ]
    ],

    'get_started' => [
        'name' => 'Bắt đầu sử dụng',
        'header_page' => 'Vui lòng khai báo thông tin theo các bước dưới để bắt đầu sử dụng chương trình',
        'attributes' => [
            'driver' => [
                'create_driver' => 'Khai báo tài xế',
                'text' => 'Bạn có thể thêm mới tài xế bằng cách nhập từ file excel hoặc tự khai báo.'
            ],
            'vehicle' => [
                'create_vehicle' => 'Khai báo xe',
                'text' => 'Bạn có thể thêm mới xe bằng cách nhập từ file excel hoặc tự khai báo.'
            ],
            'order' => [
                'create_order' => 'Khai báo đơn hàng',
                'text' => 'Bạn có thể thêm mới đơn hàng bằng cách nhập từ file excel hoặc tự khai báo.'
            ],
            'app' => [
                'install_app' => 'Cài đặt ứng dụng',
                'name_app' => [
                    '0' => config('constant.APP_NAME') . ' Quản trị',
                    '1' => config('constant.APP_NAME') . ' Điều hành',
                    '2' => config('constant.APP_NAME') . ' Khách hàng',
                    '3' => config('constant.APP_NAME') . ' Tài xế',
                ]
            ]
        ]
    ],

    'partner_order' => [
        'name' => 'Đơn hàng vận tải'
    ],

    'partner_vehicle' => [
        'name' => 'Xe'
    ],

    'partner_vehicle_team' => [
        'name' => 'Đội tài xế'
    ],

    'partner_driver' => [
        'name' => 'Tài xế'
    ],
    'partner_admin' => [
        'name' => 'Người dùng'
    ],
    'partner' => [
        'name' => 'Đối tác',
        'attributes' => [
            'code' => 'Mã đối tác',
            'full_name' => 'Tên đối tác',
            'mobile_no' => 'Số điện thoại',
            'email' => 'Email',
            'address' => 'Địa chỉ',
            'current_address' => 'Địa chỉ',
            'note' => 'Ghi chú',
            'location' => 'Địa điểm',
            'delegate' => 'Người đại diện',
            'tax_code' => 'Mã số thuế',
            'province_id' => 'Tỉnh - Thành phố',
            'district_id' => 'Quận - Huyện',
            'ward_id' => 'Xã - Phường',
            'ins_id' => 'Người tạo',
            'upd_id' => 'Người sửa',
            'name_of_ins_id' => 'Người tạo',
            'name_of_upd_id' => 'Người sửa',
            'ins_date' => 'Ngày tạo',
            'upd_date' => 'Ngày sửa',

        ],
    ],
    'customer_calendar' => [
        'name' => 'Lịch biểu',
    ],
    'customer_order_customer' => [
        'name' => 'Đơn hàng',
    ],
    'customer_order' => [
        'name' => 'Đơn hàng vận tải',
    ],
    'customer_route' => [
        'name' => 'Chuyến xe',
    ],
    'customer_client' => [
        'name' => 'Khách hàng',
    ],
    'customer_staff' => [
        'name' => 'Nhân viên',
    ],
    'customer_role' => [
        'name' => 'Vai trò',
        'name_in_admin' => 'Vai trò chủ hàng',
    ],
    'customer_location' => [
        'name' => 'Sổ địa chỉ',
    ],
    'customer_location_group' => [
        'name' => 'Nhóm địa điểm',
    ],
    'customer_location_type' => [
        'name' => 'Loại địa điểm',
    ],
    'customer_goods' => [
        'name' => 'Hàng hoá',
    ],
    'customer_goods_unit' => [
        'name' => 'Đơn vị hàng hoá',
    ],

    'partner_template' => [
        'name' => 'Mẫu'
    ],

    'partner_get_started' => [
        'name' => 'Bắt đầu sử dụng',
        'header_page' => 'Vui lòng khai báo thông tin theo các bước dưới để bắt đầu sử dụng chương trình',
        'attributes' => [
            'driver' => [
                'create_driver' => 'Khai báo tài xế',
                'text' => 'Bạn có thể thêm mới tài xế bằng cách nhập từ file excel hoặc tự khai báo.'
            ],
            'vehicle' => [
                'create_vehicle' => 'Khai báo xe',
                'text' => 'Bạn có thể thêm mới xe bằng cách nhập từ file excel hoặc tự khai báo.'
            ],
            'order' => [
                'create_order' => 'Tiếp nhận đơn hàng',
                'text' => 'Bạn có thể xác nhận và gán đơn hàng cho tài xế'
            ],
            'app' => [
                'install_app' => 'Cài đặt ứng dụng',
                'text_1' => 'Bộ ứng dụng hiện đã có trên 2 hệ điều hành: Android và IOS.',
                'text_2' => 'Hãy quét mã QR bên dưới để tải ngay.',
                'text_3' => 'Ứng dụng cho điện thoại, máy tính bảng cho từng bộ phận.',
                'label_input_send_mail' => 'Gửi đường dẫn tải ứng dụng qua email:',
                'name_app' => [
                    '0' => config('constant.APP_NAME') . ' Quản trị',
                    '1' => config('constant.APP_NAME') . ' Điều hành',
                    '2' => config('constant.APP_NAME') . ' Khách hàng',
                    '3' => config('constant.APP_NAME') . ' Tài xế',
                ]
            ]
        ]
    ],
];
