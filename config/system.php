<?php
return [
    // migrate // set empty if have no using
    'default_auth_id' => 1,
    'created_at_column' => ['field' => 'ins_date', 'comment' => 'Created at column'],
    'updated_at_column' => ['field' => 'upd_date', 'comment' => 'Updated at column'],
    'deleted_at_column' => [],
    'del_flag_column' => ['field' => 'del_flag', 'comment' => 'Delete flag column', 'active' => 0, 'deleted' => 1],
    'created_by_column' => ['field' => 'ins_id', 'comment' => 'Created by column'],
    'updated_by_column' => ['field' => 'upd_id', 'comment' => 'Updated by column'],
    'deleted_by_column' => [],
    'status_column' => [],
    // route
    'backend_alias' => env("BACKEND_ALIAS", 'management'),
    'backend_domain' => env('BACKEND_DOMAIN', ''),
    'frontend_alias' => '/',
    'frontend_domain' => env('FRONTEND_DOMAIN', ''),
    'api_alias' => env('API_ALIAS', ''),
    'api_domain' => env('API_DOMAIN', ''),
    'back_url_limit' => 200,
    //auth
    'backend_guard' => 'admins',
    'frontend_guard' => 'users',
    'api_guard' => 'api',
    'guest_guard' => 'guest',
    //log
    'log_info_filename' => 'info',
    'log_error_filename' => 'errors',
    'log_warning_filename' => 'warning',
    'log_debug_filename' => 'debug',
    'log_api_filename' => 'api',
    'sql_log' => env('SQL_LOG', true),
    'log_viewer_password' => env('LOG_VIEWER_PASSWORD', 'AsEgxSuP'),

    // static version
    'static_version' => strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' || strtoupper(substr(PHP_OS, 0, 3)) === 'DAR' ? time() : env('STATIC_VERSION', '0.0.1'),
    // upload
    'tmp_upload_dir' => 'tmp_uploads',
    'media_dir' => 'media',
    // class default
    'form_class_default' => ' form-control ',
    'form_open_class_default' => ' form-horizontal ',

    // per page option
    'per_page_list' => [
        '10' => 10,
        '20' => 20,
        '50' => 50,
        '100' => 100,
    ],

    /* Tinh thanh */
    'province' => [
        '1' => 'Tỉnh',
        '2' => 'Thành phố'
    ],
    'district' => [
        '1' => 'Quận',
        '2' => 'Huyện',
        '3' => 'Thị xã',
        '4' => 'Thành phố'
    ],
    'ward' => [
        '1' => 'Xã',
        '2' => 'Phường',
        '3' => 'Thị trấn'
    ],
    /* End Tinh thanh */

    /* Tinh thanh */
    'receipt_payment' => [
        1 => 'Thu',
        2 => 'Chi'
    ],
    /* End Tinh thanh */

    'sex' => [
        'male' => 'Nam',
        'female' => 'Nữ'
    ],

    'option' => [
        '1' => 'Có',
        '0' => 'Không'
    ],

    'active' => [
        '1' => 'Kích hoạt',
        '0' => 'Không kích hoạt'
    ],

    'file_type' => [
        '1' => 'Ảnh(.jpeg,.jpg,.png,.gif)',
        '2' => 'Excel(.xls,.xlsx)',
        '3' => 'Word(.doc,.docx)',
        '4' => 'PDF(.pdf)'
    ],
    'column_type' => [
        '1' => 'Văn bản',
        '2' => 'Số',
        '3' => 'Thời gian'
    ],
    // Xe
    'vehicle_status' => [
        '1' => 'Xe trống, đang chờ việc',
        '2' => 'Xe đang vận chuyển hàng công ty',
        '3' => 'Xe đang vận chuyển hàng khác',
        '4' => 'Xe hỏng'
    ],
    'vehicle_active' => [
        '1' => 'Hoạt động',
        '0' => 'Không hoạt động'
    ],
    'vehicle_unit' => [
        '1' => [
            '1' => 'Tấn',
            '2' => 'Kilogram'
        ],
        '2' => [
            '1' => 'Met',
            '2' => 'Centimet'
        ],
        '3' => [
            '1' => 'Khối'
        ]
    ],
    'vehicle_type' => [
        '1' => 'Xe công ty',
        '2' => 'Xe ngoài công ty',
        '3' => 'Xe thuê'
    ],
    // Đơn hàng
    'order_precedences' => [
        3 => 'Đặc biệt',
        4 => 'Bình thường',
        5 => 'Thấp',
    ],
    'order_status' => [
        1 => 'Khởi tạo',
        2 => 'Sẵn sàng',
        7 => 'Chờ tài xế xác nhận',
        3 => 'Chờ nhận hàng',
        4 => 'Đang vận chuyển',
        5 => 'Hoàn thành',
        6 => 'Hủy',
    ],
    'order_status_partner' => [
        0 => 'Chờ giao cho đối tác vận tải',
        1 => 'Chờ đối tác vận tải xác nhận',
        2 => 'Đối tác vận tải xác nhận',
        3 => 'Đối tác vận tải hủy',
        4 => 'Đối tác vận tải yêu cầu sửa',
    ],
    'c20_order_status_search' => [
        8 => 'Chờ giao cho đối tác vận tải',
        9 => 'Chờ đối tác vận tải xác nhận',
        10 => 'Đối tác vận tải yêu cầu sửa',
        2 => 'Sẵn sàng',
        7 => 'Chờ tài xế xác nhận',
        3 => 'Chờ nhận hàng',
        4 => 'Đang vận chuyển',
        5 => 'Hoàn thành',
        6 => 'Hủy',
    ],
    'partner_order_status_search' => [
        8 => 'Chờ đối tác vận tải xác nhận',
        9 => 'Đối tác vận tải yêu cầu sửa',
        2 => 'Sẵn sàng',
        3 => 'Chờ tài xế xác nhận',
        4 => 'Chờ nhận hàng',
        5 => 'Đang vận chuyển',
        6 => 'Hoàn thành',
        7 => 'Hủy',
    ],
    'order_status_collected_documents' => [
        1 => 'Chưa thu đủ',
        2 => 'Đã thu đủ'
    ],
    'order_status_file' => [
        [
            'id' => 99,
            'name' => "Chứng từ"
        ],
        [
            'id' => config("constant.KHOI_TAO"),
            'name' => "Thông tin chung"
        ], [
            'id' => config("constant.DANG_VAN_CHUYEN"),
            'name' => "Nhận hàng"
        ], [
            'id' => config("constant.HOAN_THANH"),
            'name' => "Trả hàng"
        ], [
            'id' => config("constant.HUY"),
            'name' => "Hủy"
        ],
        [
            'id' => config("constant.FILE_REVIEW_ORDER_TYPE"),
            'name' => "Đánh giá từ khách hàng"
        ]
    ],
    'order_payment_type' => [
        1 => 'Chuyển khoản',
        2 => 'Tiền mặt',
    ],
    'order_vat' => [
        '1' => 'Có',
        '0' => 'Không'
    ],
    'order_is_merge_item' => [
        '1' => 'Có',
        '0' => 'Không'
    ],
    'order_is_insured_goods' => [
        '1' => 'Có',
        '0' => 'Không'
    ],
    'order_commission_type' => [
        2 => 'Số tiền',
        1 => 'Phần trăm',
    ],
    // Loại khách hàng
    'customer_type' => [
        1 => 'Khách hàng doanh nghiệp',
        2 => 'Khách hàng cá nhân'
    ],

    // Hợp đồng
    'contract_type' => [
        1 => 'Nhanh',
        2 => 'Khung',
        3 => 'Chính thức',
    ],
    'contract_status' => [
        1 => 'Thêm mới',
        2 => 'Chưa hoàn thành',
        3 => 'Hoàn thành',
    ],

    // Tài xế
    'driver_license' => [
        'B1' => 'B1',
        'B2' => 'B2',
        'C' => 'C',
        'D' => 'D',
        'E' => 'E',
        'F' => 'F',
        'FB2' => 'FB2',
        'FC' => 'FC',
        'FD' => 'FD',
        'FE' => 'FE'
    ],

    // Báo cáo định kỳ
    'alert_logs_type' => [
        '0' => 'Gửi một lần',
        '1' => 'Gửi hàng ngày'
    ],
    'schedule_type' => [
        '0' => 'Hằng ngày',
        '1' => 'Hằng tuần',
        '2' => 'Hằng tháng',
        '3' => 'Hằng quý',
        '4' => 'Hằng năm',
    ],
    'scheduler_report_type' => [
        1 => 'Thống kê tương tác vận tải',
        2 => 'Báo cáo năng suất xe',
        3 => 'Báo cáo doanh thu , chi phí theo khách hàng',
        4 => 'Báo cáo hoạt động theo đội xe và tài xế',
        5 => 'Thống kê tương tác tài xế',
    ],

    // Mã hệ thống
    'system_code_type' => [
        '1' => 'Đơn hàng',
        '2' => 'Khách hàng',
        '3' => 'Tài xế',
        '4' => 'Loại hàng hóa',
        '5' => 'Đơn vị hàng hóa',
        '6' => 'Địa điểm',
        '7' => 'Đội tài xế',
        '8' => 'Chủng loại xe',
        '9' => 'Chuyến xe',
        '10' => 'Bảng định mức chi phí',
        '11' => 'Đơn hàng khách hàng',
        '12' => 'Nhóm khách hàng',
        '13' => 'Báo giá',
        '14' => 'Tính lương tài xế',
        '15' => 'Nhóm địa điểm',
        '16' => 'Phiếu sửa chữa',
        '17' => 'Nhóm hàng hóa',
        '18' => 'Đối tác',
    ],

    // Chứng từ
    'collected_documents_status' => [
        1 => 'Chưa thu đủ',
        2 => 'Đã thu đủ',
        3 => 'Quá hạn',
        4 => 'Đến hạn vào ngày hôm sau',
        5 => 'Đến hạn vào ngày hôm nay'
    ],
    'collected_documents_combo' => [
        1 => 'Chưa thu đủ',
        2 => 'Đã thu đủ'
    ],

    // Đơn hàng khách hàng
    'order_customer_commission_type' => [
        1 => 'Phần trăm',
        2 => 'Số tiền'
    ],
    'order_customer_status' => [
//        1 => 'Chờ chủ hàng xác nhận',
//        2 => 'Chủ hàng xác nhận',
//        3 => 'Chủ hàng hủy',
//        4 => 'Chủ hàng yêu cầu sửa đổi',
        5 => 'Đã xuất hàng',
        6 => 'Đang vận chuyển',
        7 => 'Hoàn thành',
        8 => 'Trung tâm điều hành hủy',
    ],
    'order_customer_payment_type' => [
        1 => 'Chuyển khoản',
        2 => 'Tiền mặt',
    ],
    'order_customer_vat' => [
        '1' => 'Có',
        '0' => 'Không'
    ],
    'order_customer_status_goods' => [
        '1' => 'Còn hàng',
        '2' => 'Hết hàng'
    ],
    // Chuyến
    'route_status' => [
        0 => 'Chưa hoàn thành',
        1 => 'Hoàn thành',
        2 => 'Hủy'
    ],
    'route_is_approved' => [
        '1' => 'Đã phê duyệt',
        '0' => 'Chưa phê duyệt'
    ],

    // Mẫu
    'template_type' => [
        '1' => 'Đơn hàng vận tải',
        '2' => 'Chứng từ',
        '3' => 'Chuyến xe',
        '4' => 'Bảng định mức chi phí',
        '5' => 'Khách hàng',
        '6' => 'Tài xế',
        '7' => 'Xe',
        '8' => 'Đơn đặt hàng'

    ],
    'template_export_type' => [
        2 => 'Xuất dữ liệu trên một trang (dạng bảng)',
        1 => 'Xuất dữ liệu trên nhiều trang/nhiều sheet',

    ],

    'partner_template_type' => [
        '1' => 'Đơn hàng vận tải',
        '2' => 'Chứng từ',
        '3' => 'Chuyến xe',
        '6' => 'Tài xế',
        '7' => 'Xe',
    ],

    //Địa điểm
    'location_type' => [
        1 => 'Khó',
        2 => 'Bình thường',
        3 => 'Dễ',
    ],


    'document_block' => 'Biểu mẫu',

    'price_quote_type' => [
        1 => 'Chủng loại xe',
        2 => 'Tổng khối lượng đơn hàng',
        3 => 'Tổng thể tích đơn hàng',
        4 => 'Loại hàng hóa'
    ],
    'customer_customer_type' => [
        1 => 'Chủ hàng',
        2 => 'Người dùng',
        3 => 'Khách hàng',
    ],
];
