const orderCustomerStatus = {
  CHO_CHU_HANG_XAC_NHAN: 1,
  CHU_HANG_XAC_NHAN: 2,
  CHU_HANG_HUY: 3,
  CHU_HANG_YEU_CAU_SUA_DOI: 4,
  DA_XUAT_HANG: 5,
  DANG_VAN_CHUYEN: 6,
  HOAN_THANH: 7,
  TRUNG_TAM_DIEU_HANH_HUY: 8,
};
const goodsExportStatus = {
  REMAIN: 1,
  EMPTY: 2,
};
const customerType = {
  CUSTOMER: 1,
  STAFF: 2,
  CLIENT: 3,
};
const vehicleType = {
  CAR : 1 ,
  MOTORBIKE : 2
};
const orderStatus = {
  KHOI_TAO: 1,
  SAN_SANG: 2,
  CHO_NHAN_HANG: 3,
  DANG_VAN_CHUYEN: 4,
  HOAN_THANH: 5,
  HUY: 6,
  TAI_XE_XAC_NHAN: 7,
};
export default {
  customerType,
  goodsExportStatus,
  vehicleType,
  orderCustomerStatusConstant: orderCustomerStatus,
  orderCustomerStatus: [
    {
      value: orderCustomerStatus.CHO_CHU_HANG_XAC_NHAN,
      text: "Chờ chủ hàng xác nhận",
    },
    {
      value: orderCustomerStatus.CHU_HANG_XAC_NHAN,
      text: "Chủ hàng xác nhận",
    },
    {
      value: orderCustomerStatus.CHU_HANG_HUY,
      text: "Chủ hàng huỷ",
    },
    {
      value: orderCustomerStatus.CHU_HANG_YEU_CAU_SUA_DOI,
      text: "Chủ hàng yêu cầu sửa đổi",
    },
    {
      value: orderCustomerStatus.DA_XUAT_HANG,
      text: "Đã xuất hàng",
    },
    {
      value: orderCustomerStatus.DANG_VAN_CHUYEN,
      text: "Đang vận chuyển",
    },
    {
      value: orderCustomerStatus.HOAN_THANH,
      text: "Hoàn thành",
    },
    {
      value: orderCustomerStatus.TRUNG_TAM_DIEU_HANH_HUY,
      text: "Trung tâm điều hành huỷ",
    },
  ],
  orderStatus: [
    {
      value: orderStatus.KHOI_TAO,
      text: "Khởi tạo",
    },
    {
      value: orderStatus.SAN_SANG,
      text: "Sẵn sàng",
    },
    {
      value: orderStatus.CHO_NHAN_HANG,
      text: "Chờ nhận hàng",
    },
    {
      value: orderStatus.DANG_VAN_CHUYEN,
      text: "Đang vận chuyển",
    },
    {
      value: orderStatus.HOAN_THANH,
      text: "Hoàn thành",
    },
    {
      value: orderStatus.HUY,
      text: "Hủy",
    },
    {
      value: orderStatus.TAI_XE_XAC_NHAN,
      text: "Tài xế xác nhận",
    },
  ],
  goodsStatus: [
    {
      value: goodsExportStatus.REMAIN,
      text: "Chưa xuất hết",
    },
    {
      value: goodsExportStatus.EMPTY,
      text: "Đã xuất hết",
    },
  ],
  allowExportGoodsStatus: [
    orderCustomerStatus.DA_XUAT_HANG,
    orderCustomerStatus.DANG_VAN_CHUYEN,
    orderCustomerStatus.HOAN_THANH,
  ],
  entityFieldCode: {
    "client": 'customer_code',
    "staff": 'customer_code',
  }
};
