import constant from "@/constant";
const OrderCustomer = {
  code: {},
  order_no: {},
  status: {},
  note: {},
  goods_amount: {
    dataType: "number",
  },
  order_date: {
    dataType: "date",
    defaultValue: "now",
  },
  client_id: {
    relation: "client",
  },
  location_arrival_id: {
    relation: "location_arrival",
  },
  location_destination_id: {
    relation: "location_destination",
  },
  ETD_date: { dataType: "date", defaultValue: "now" },
  ETD_time: {
    dataType: "time",
    defaultValue: "now",
  },
  ETA_date: { dataType: "date", defaultValue: "now", increment: 1 },
  ETA_time: {
    dataType: "time",
    defaultValue: "now",
  },
  list_goods: {
    dataType: "array",
    dataRelation: ["goods_type", "goods_unit"],
  },
  ETA_date_desired: { dataType: "date", defaultValue: "now", increment: 1 },
  ETA_time_desired: {
    dataType: "time",
    defaultValue: "now",
  },
  distance: 0,
  amount_estimate: 0,
  total_weight: 0,
};
const OrderClient = {
  code: {},
  reason: {},
  order_no: {},
  status: {},
  note: {},
  goods_amount: {
    dataType: "number",
  },
  order_date: {
    dataType: "date",
    defaultValue: "now",
  },
  // location_destination_id: {
  //   relation: "location_destination",
  // },
  // ETD_date: { dataType: "date", defaultValue: "now" },
  // ETD_time: {
  //   dataType: "time",
  //   defaultValue: "now",
  // },
  location_arrival_id: {
    relation: "location_arrival",
  },
  ETA_date: { dataType: "date", defaultValue: "now", increment: 1 },
  ETA_time: {
    dataType: "time",
    defaultValue: "now",
  },
  list_goods: {
    dataType: "array",
    dataRelation: ["goods_type", "goods_unit"],
  },
};
const Order = {};
const Client = {
  type: {
    dataType: "number",
    defaultValue: 1,
  },
  user_id: {},
  avatar_id: {},
  path_of_avatar_id: {},
  customer_code: {},
  tax_code: {},
  delegate: {},
  mobile_no: {},
  full_name: {},
  email: {},
  username: {},
  password: {},
  sex: {},
  birth_date: {},
};
const Goods = {
  code: {},
  title: {},
  file_id: {},
  path_of_file_id: {},
  goods_unit_id: {
    relation: "goods_unit",
  },
  in_amount: {
    dataType: "number",
  },
  out_amount: {
    dataType: "number",
  },
  weight: {
    dataType: "number",
  },
  volume: {
    dataType: "number",
  },
  note: {
    field: "note",
  },
};
const GoodsUnit = {
  code: {},
  title: {},
};
const Location = {
  code: {},
  title: {},
  location_type_id: {
    relation: "location_type",
  },
  province_id: {
    relation: "province",
  },
  district_id: {
    relation: "district",
  },
  ward_id: {
    relation: "ward",
  },
};
const LocationGroup = {
  code: {},
  title: {},
  description: {},
};
const LocationType = {
  code: {},
  title: {},
  description: {},
};
const Role = {
  code: {},
  title: {},
  description: {},
};
const Staff = {
  type: {},
  user_id: {},
  avatar_id: {},
  path_of_avatar_id: {},
  customer_code: {},
  full_name: {},
  identity_no: {},
  birth_date: {},
  email: {},
  username: {},
  password: {},
  mobile_no: {},
};
const DefaultData = {
  code: {},
  client_id: {
    relation: "client",
  },
  location_destination_id: {
    relation: "location_destination",
  },
  location_arrival_id: {
    relation: "location_arrival",
  },
};
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
};
