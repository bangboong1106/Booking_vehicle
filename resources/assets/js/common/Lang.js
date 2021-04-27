import constant from "@/constant";

export default {
  OrderCustomer: {
    name: "Đơn đặt hàng",
    attribute: {
      code: "Mã",
      order_no: "Số đơn hàng",
      client_id: "Khách hàng",
      client: "Khách hàng",
      order_date: "Ngày đặt hàng",
      location_destination: "Điểm nhận hàng",
      location_arrival: "Điểm trả hàng",
      location_destination_id: "Điểm nhận hàng",
      location_arrival_id: "Điểm trả hàng",
    },
  },
  OrderClient: {
    name: "Đơn đặt hàng",
    attribute: {
      code: "Mã",
      order_no: "Số đơn hàng",
      client_id: "Khách hàng",
      client: "Khách hàng",
      order_date: "Ngày đặt hàng",
      location_destination: "Điểm nhận hàng",
      location_arrival: "Điểm trả hàng",
      location_destination_id: "Điểm nhận hàng",
      location_arrival_id: "Điểm trả hàng",
    },
  },
  Order: { name: "Đơn hàng vận tải" },
  Client: {
    name: "Khách hàng",
    attribute: {
      tax_code: "Mã số thuế",
      delegate: "Người đại diện",
      customer_code: "Mã khách hàng",
      full_name: "Họ tên",
      email: "Email",
      username: "Tên đăng nhập",
      password: "Mật khẩu",
      confirm_password: "Xác nhận mật khẩu",
      mobile_no: "Số điện thoại",
    },
  },
  Staff: {
    name: "Nhân viên",
    attribute: {
      type: "Loại",
      user_id: "Người dùng",
      avatar_id: "Hình ảnh",
      customer_code: "Mã nhân viên",
      full_name: "Họ tên",
      identity_no: "CMND/CCCD",
      birth_date: "Ngày sinh",
      email: "Email",
      username: "Tên đăng nhập",
      password: "Mật khẩu",
      mobile_no: "Số điện thoại",
    },
  },
  Goods: {
    name: "Hàng hoá",
    attribute: {
      code: "Mã",
      title: "Tên",
      goods_unit: "Đơn vị",
      volume: "Thể tích",
      weight: "Trọng lượng",
    },
  },
  GoodsUnit: {
    name: "Đơn vị",
    attribute: {
      code: "Mã",
      title: "Tên",
      description: "Mô tả",
    },
  },
  Location: {
    name: "Địa điểm",
    attribute: {
      code: "Mã",
      title: "Tên",
      province: "Tỉnh/Thành phố",
      district: "Quận/Huyện",
      ward: "Phường/Xã",
    },
  },
  LocationGroup: {
    name: "Nhóm địa điểm",
    attribute: {
      code: "Mã",
      title: "Tên",
      description: "Mô tả",
    },
  },
  LocationType: {
    name: "Loại địa điểm",
    attribute: {
      code: "Mã",
      title: "Tên",
      description: "Mô tả",
    },
  },
  Role: {
    name: "Vai trò",
  },
  DefaultData: {
    name: "Dữ liệu mặc định",
    attribute: {
      client: "Khách hàng",
      location_destination: "Điểm nhận hàng",
      location_destination_ids: "Điểm nhận hàng",
      location_arrival: "Điểm nhận hàng",
      location_arrival_ids: "Điểm trả hàng",
      code: "Mã dữ liệu mặc định",
    },
  },
};
