<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | following language lines contain default error messages used by
    | validator class. Some of these rules have multiple versions such
    | as size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute phải được chấp nhận.',
    'active_url'           => ':attribute không là URL.',
    'after'                => ':attribute phải lớn hơn ngày :date.',
    'after_or_equal'       => ':attribute phải lớn hơn hoặc bằng ngày :date.',
    'alpha'                => ':attribute chỉ bao gồm ký tự.',
    'alpha_dash'           => ':attribute chỉ bao gồm ký tự, số và dấu gach ngang.',
    'alpha_num'            => ':attribute chỉ bao gồm ký tự và số.',
    'array'                => ':attribute phải dưới dạng mảng.',
    'before'               => ':attribute phải nhỏ hơn ngày :date.',
    'before_or_equal'      => ':attribute phải nhỏ hơn hoặc bằng ngày :date.',
    'between'              => [
        'numeric' => ':attribute phải nằm trong khoảng :min - :max.',
        'file'    => ':attribute kích thước phải từ :min - :max mb.',
        'string'  => ':attribute chỉ bao gồm từ :min đến :max ký tự.',
        'array'   => ':attribute phải từ :min đến :max phần tử.',
    ],
    'boolean'              => ':attribute phải dưới dạng true hoặc false.',
    'confirmed'            => ':attribute xác nhận không trùng khớp.',
    'date'                 => ':attribute không chính xác.',
    'date_format'          => ':attribute phải có kiểu dữ liệu dạng :format.',
    'different'            => ':attribute và :other phải khác nhau.',
    'digits'               => ':attribute phải có :digits chữ số.',
    'digits_between'       => ':attribute phải có từ :min đến :max chữ số.',
    'dimensions'           => ':attribute kích thước không đúng.',
    'distinct'             => ':attribute đã bị trùng.',
    'email'                => ':attribute cần có định dang kiểu email.',
    'exists'               => ':attribute không có trong hệ thống.',
    'file'                 => ':attribute phải có dạng file.',
    'filled'               => ':attribute không được bỏ trống.',
    'image'                => ':attribute phải là dạng ảnh.',
    'in'                   => ':attribute không có hiệu lực.',
    'in_array'             => ':attribute không tồn tại trong :other.',
    'integer'              => ':attribute phải dưới dạng số.',
    'ip'                   => ':attribute phải dưới dạng địa chỉ IP.',
    'json'                 => ':attribute phải dưới dạng JSON.',
    'max'                  => [
        'numeric' => ':attribute không được lớn hơn :max.',
        'file'    => ':attribute không được lớn hơn :max mb.',
        'string'  => ':attribute không được lớn hơn :max ký tự.',
        'array'   => ':attribute không được nhiều hơn :max phần tử.',
    ],
    'mimes'                => ':attribute phải có dạng :values.',
    'mimetypes'            => ':attribute phải có dạng :values.',
    'min'                  => [
        'numeric' => ':attribute không được nhỏ hơn :min.',
        'file'    => ':attribute không được nhỏ hơn :min kilobytes.',
        'string'  => ':attribute không được nhỏ hơn :min ký tự.',
        'array'   => ':attribute có ít nhất :min phần tử.',
    ],
    'not_in'               => ':attribute không tồn tại.',
    'numeric'              => ':attribute phải là dạng số.',
    'present'              => ':attribute phải có hiệu lực.',
    'regex'                => ':attribute định dạng không đúng.',
    'not_empty'             => ':attribute không được bỏ trống.',
    'required'             => ':attribute là bắt buộc.',
    'required_if'          => ':attribute là bắt buộc nếu :other là :value.',
    'required_unless'      => ':attribute là bắt buộc trừ khi :other có giá trị :values.',
    'required_with'        => ':attribute là bắt buộc nếu :values được chọn.',
    'required_with_all'    => ':attribute là bắt buộc nếu :values được chọn.',
    'required_without'     => ':attribute là bắt buộc nếu :values không được chọn.',
    'required_without_all' => ':attribute là bắt buộc nếu tất cả :values không được chọn ',
    'same'                 => ':attribute và :other phải trùng nhau',
    'size'                 => [
        'numeric' => ':attribute phải có :size chữ số.',
        'file'    => ':attribute phải có :size kilobytes.',
        'string'  => ':attribute phải có :size ký tự.',
        'array'   => ':attribute phải gồm :size phần tử.',
    ],
    'string'               => ':attribute phải dưới dạng ký tự.',
    'timezone'             => ':attribute khung giờ không đúng.',
    'unique'               => ':attribute đã tồn tại.',
    'uploaded'             => ':attribute lỗi upload.',
    'url'                  => ':attribute định dạng không đúng.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    'out_of_weight' => 'Vượt quá tải trọng của xe: :weight/:vehicle',
    'out_of_volume' => 'Vượt quá thể tích của xe: :volume/:vehicle',
    'group_exist' => 'Địa điểm :name đã có nhóm.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
