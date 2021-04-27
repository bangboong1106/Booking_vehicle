import constant from "@/constant";

const OrderCustomer = [
    {
        title: "Hành động",
        key: "operation",
        fixed: "left",
        width: 110,
        isEditByUser: true,
        statusField: "status",
        actions: [
            {type: "edit", color: "green", status: [2]},
            {type: "delete", color: "red", status: [2]},
        ],
        scopedSlots: {customRender: "renderAction"},
    },
    {
        width: 150,
        title: "Số đơn hàng",
        fixed: "left",
        dataIndex: "order_no",
        isCodeIndex: true,
        isNameIndex: true,
        ellipsis: true,
    },
    {
        width: 200,
        title: "Trạng thái",
        dataIndex: "status",
        dataType: "list",
        dataSource: constant.orderCustomerStatus,
        ellipsis: true,

    },
    {
        width: 200,
        title: "Trạng thái xuất hàng",
        dataIndex: "status_goods",
        dataType: "list",
        dataSource: constant.goodsStatus,
        ellipsis: true,
    },
    {
        width: 150,
        title: "Ngày đặt hàng",
        dataIndex: "order_date",
        dataType: "date",
        ellipsis: true,
    },
    {
        width: 200,
        title: "Khách hàng",
        dataIndex: "name_of_client_id",
        originalIndex: "c.full_name",
        ellipsis: true,
        dataType: 'text'
    },
    {
        width: 200,
        title: "Điểm nhận hàng",
        dataIndex: "name_of_location_destination_id",
        originalIndex: "ld.title",
        ellipsis: true,
        dataType: 'text'
    },
    {
        width: 200,
        title: "Ngày nhận hàng",
        dataIndex: "ETD_date_time",
        dataType: "datetime",
        originalIndex: "ETD_date",
        ellipsis: true,
    },
    {
        width: 200,
        title: "Điểm trả hàng",
        dataIndex: "name_of_location_arrival_id",
        originalIndex: "la.title",
        ellipsis: true,
        dataType: 'text'
    },
    {
        width: 200,
        title: "Ngày trả hàng",
        dataIndex: "ETA_date_time",
        dataType: "datetime",
        originalIndex: "ETD_date",
        ellipsis: true,
    },
];

const OrderClient = [
    {
        title: "Hành động",
        key: "operation",
        fixed: "left",
        width: 110,
        isEditByUser: true,
        statusField: "status",
        actions: [
            {type: "edit", color: "green", status: [1, 4]},
            {type: "delete", color: "red", status: [1]},
        ],
        scopedSlots: {customRender: "renderAction"},
    },
    {
        title: "Số đơn hàng",
        fixed: "left",
        dataIndex: "order_no",
        isNameIndex: true,
        width: 200,
        isCodeIndex: true,
    },
    {
        title: "Trạng thái",
        dataIndex: "status",
        dataType: "list",
        dataSource: constant.orderCustomerStatus,
    },
    {
        title: "Trạng thái xuất hàng",
        dataIndex: "status_goods",
        dataType: "list",
        dataSource: constant.goodsStatus,
    },
    {
        title: "Ngày đặt hàng",
        dataIndex: "order_date",
        dataType: "date",
    },
    {
        title: "Điểm trả hàng",
        dataIndex: "name_of_location_arrival_id",
        originalIndex: "la.title",
    },
    {
        title: "Ngày trả hàng",
        dataIndex: "ETA_date",
        dataType: "date",
    },
    {
        title: "Giờ trả hàng",
        dataIndex: "ETA_time",
        dataType: "time",
    },
];

const Order = [
    {
        dataIndex: "order_code",
        title: "Mã",
        fixed: "left",
        width: 150,
        ellipsis: true,
        isCodeIndex: true,
    },
    {
        width: 150,
        ellipsis: true,
        title: "Số đơn hàng",
        fixed: "left",
        dataIndex: "order_no",
        isNameIndex: true,
    },
    {
        width: 150,
        ellipsis: true,
        title: "Trạng thái",
        dataIndex: "status",
        dataType: "list",
        dataSource: constant.orderStatus,
    },
    {
        width: 150,
        ellipsis: true,
        title: "Ngày đặt hàng",
        dataIndex: "order_date",
        dataType: "date",
    },
    {
        width: 200,
        ellipsis: true,
        title: "Khách hàng",
        dataIndex: "name_of_client_id",
        originalIndex: "c.full_name",
    },
    {
        width: 200,
        ellipsis: true,
        title: "Điểm nhận hàng",
        dataIndex: "name_of_location_destination_id",
        originalIndex: "ld.title",

    },
    {
        width: 200,
        ellipsis: true,
        title: "Ngày nhận hàng",
        dataIndex: "ETD_date",
        dataType: "date",
    },
    {
        width: 200,
        ellipsis: true,
        title: "Điểm trả hàng",
        dataIndex: "name_of_location_arrival_id",
        originalIndex: "la.title",

    },
    {
        width: 150,
        ellipsis: true,
        title: "Ngày trả hàng",
        dataIndex: "ETA_date",
        dataType: "date",
    },
    {
        width: 150,
        ellipsis: true,
        title: "Xe",
        dataIndex: "name_of_vehicle_id",
        originalIndex: "v.reg_no",

    },
    {
        width: 200,
        ellipsis: true,
        title: "Tài xế",
        dataIndex: "name_of_primary_driver_id",
        originalIndex: "d.full_name",

    },
];

const Staff = [
    {
        title: "Hình ảnh",
        dataIndex: "path_of_avatar_id",
        dataType: "file",
        fixed: "left",
        width: 120,
    },
    {
        title: "Mã nhân viên",
        dataIndex: "customer_code",
        fixed: "left",
        width: 150,
    },
    {
        width: 200,
        title: "Tên",
        dataIndex: "full_name",
        isNameIndex: true,
        ellipsis: true,
    },
    {
        width: 150,
        title: "Tài khoản",
        dataIndex: "username",
        originalIndex: "admin_users.username",
    },
    {
        width: 150,
        title: "Email",
        dataIndex: "email",
        dataType: "email",
        originalIndex: "admin_users.email",
        ellipsis: true,

    },
    {
        width: 120,
        title: "Số điện thoại",
        dataIndex: "mobile_no",
        dataType: "phone",
    },
    {
        width: 120,

        title: "CMND/CCCD",
        dataIndex: "identity_no",
    },
    {
        width: 120,

        title: "Ngày sinh",
        dataIndex: "birth_date",
        dataType: "date",
    },
];

const Goods = [
    {
        title: "Hình ảnh",
        dataIndex: "path_of_file_id",
        dataType: "file",
        fixed: "left",
        width: 120,
    },
    {
        title: "Mã hàng hoá",
        dataIndex: "code",
        fixed: "left",
        width: 150,
    },
    {
        title: "Tên hàng hoá",
        dataIndex: "title",
        isNameIndex: true,
    },
    {
        title: "Đơn vị",
        dataIndex: "name_of_goods_unit_id",
        originalIndex: "goods_unit.title",
        width: 150,
    },
    {
        title: "Dung tích",
        dataIndex: "volume",
        dataType: "number",
        width: 150,
    },
    {
        title: "Tải trọng",
        dataIndex: "weight",
        dataType: "number",
        width: 150,
    },
];

const GoodsUnit = [
    {
        title: "Mã đơn vị hàng hoá",
        dataIndex: "code",
        fixed: "left",
        width: 200,
    },
    {
        title: "Tên đơn vị hàng hoá",
        dataIndex: "title",
        isNameIndex: true,
    },
];

const Location = [
    {
        title: "Mã",
        dataIndex: "code",
        fixed: "left",
        width: 150,
    },
    {
        width: 200,

        title: "Tên",
        dataIndex: "title",
        isNameIndex: true,
        ellipsis: true,
    },
    {
        width: 200,
        ellipsis: true,
        title: "Địa chỉ",
        dataIndex: "address",

    },
    {
        width: 150,
        ellipsis: true,

        title: "Tỉnh/Thành phố",
        dataIndex: "name_of_province_id",
        originalIndex: "m_province.title",

    },
    {
        width: 150,
        ellipsis: true,

        title: "Quận/Huyện",
        dataIndex: "name_of_district_id",
        originalIndex: "m_district.title",

    },
    {
        width: 150,

        title: "Xã/Phường",
        dataIndex: "name_of_ward_id",
        originalIndex: "m_ward.title",

    },
];

const LocationGroup = [
    {
        title: "Mã",
        dataIndex: "code",
        fixed: "left",
        width: 200,
    },
    {
        title: "Tên",
        dataIndex: "title",
        isNameIndex: true,
    },
    {
        title: "Mô tả",
        dataIndex: "description",
    },
];

const LocationType = [
    {
        title: "Mã",
        dataIndex: "code",
        fixed: "left",
        width: 200,
    },
    {
        title: "Tên",
        dataIndex: "title",
        isNameIndex: true,
    },
    {
        title: "Mô tả",
        dataIndex: "description",
    },
];

const Client = [
    {
        title: "Hình ảnh",
        dataIndex: "path_of_avatar_id",
        dataType: "file",
        fixed: "left",
        width: 120,
    },
    {
        title: "Mã khách hàng",
        dataIndex: "customer_code",
        width: 150,
        fixed: "left",
        dataType: 'text',
    },
    {
        width: 200,
        title: "Tên khách hàng",
        dataIndex: "full_name",
        dataType: 'text',
        isNameIndex: true,
        ellipsis: true,
    },
    {
        title: "Mã số thuế",
        dataIndex: "tax_code",
        width: 120,
        ellipsis: true,
    },
    {
        width: 150,
        title: "Tài khoản",
        dataIndex: "username",
        originalIndex: "admin_users.username",

    },
    {
        width: 150,
        title: "Email",
        dataIndex: "email",
        dataType: "email",
        originalIndex: "admin_users.email",
        ellipsis: true,


    },
    {
        width: 150,
        title: "Người đại diện",
        dataIndex: "delegate",
        ellipsis: true,
        dataType: 'text'
    },
    {
        width: 150,
        title: "Số điện thoại",
        dataIndex: "mobile_no",
        dataType: "phone",
    },
];

const Role = [
    {
        dataIndex: "code",
        key: "code",
        title: "Mã vai trò",
    },
    {
        title: "Tên vai trò",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Mô tả",
        dataIndex: "description",
        key: "description",
    },
];
const DefaultData = [
    {
        width: 300,
        dataIndex: "name_of_client_id",
        key: "name_of_client_id",
        title: "Khách hàng",
        isNameIndex: true,
        originalIndex: "c.full_name",
        fixed: "left",
        ellipsis: true,


    },
    {

        ellipsis: true,
        title: "Điểm nhận hàng",
        dataIndex: "name_of_location_destination_id",
        originalIndex: "ld.title",

    },
    {

        ellipsis: true,
        title: "Điểm trả hàng",
        dataIndex: "name_of_location_arrival_id",
        originalIndex: "la.title",

    },
];
const OrderDetail = [
    {
        title:'Ảnh',
        key:'avatar',
        dataIndex : 'path',
        scopedSlots: { customRender: 'avatar' },
    },
    {
        title: 'Tên xe',
        key:'title',
        dataIndex: 'title',
    },
    {
        title: 'Trạng thái',
        key:'status',
        dataIndex: 'vehicleStatus',
    },
    {
        title: 'Số lượng',
        key:'quantity',
        dataIndex: 'quantity',
    },
    {
        title: 'Đơn giá',
        key:'price',
        dataIndex: 'price',
    },
    {
        title: 'Thành  tiền',
        key:'totalprice',
        dataIndex: 'totalprice',
    },
    {
        key:'action',
        scopedSlots: { customRender: 'action' },
    }
];
export default {
    OrderCustomer,
    OrderClient,
    Order,
    Client,
    Staff,
    Goods,
    GoodsUnit,
    Location,
    LocationGroup,
    LocationType,
    Role,
    DefaultData,
    OrderDetail
};
