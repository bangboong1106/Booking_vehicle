// const LayoutIndex = () => import("@/layout/landing/LayoutIndex.vue");
import LayoutIndex from "@/layout/landing/LayoutIndex.vue";

// GeneralViews
const NotFound = () => import("@/pages/System/NotFoundPage.vue");
const AccessDenied = () => import("@/pages/System/AccessDenied.vue");

// Landing pages
const Dashboard = () => import("@/pages/Dashboard/Dashboard.vue");
const DashboardClient = () => import("@/pages/Dashboard/DashboardClient.vue");


const Calendar = () => import("@/pages/Calendar/Calendar.vue");
const CalendarClient = () => import("@/pages/Calendar/CalendarClient.vue");



const Login = () => import("@/pages/Login");
import Home from "@/pages/Home.vue";



const Order = () => import("@/pages/Order/List.vue");
import OrderSetup from"@/pages/OrderSetup/Index";
import OrderVehicle from "@/pages/OrderVehice/Index";

const OrderCustomer = () => import("@/pages/OrderCustomer/List.vue");
const OrderClient = () => import("@/pages/OrderClient/List.vue");

const Location = () => import("@/pages/Location/List.vue");
const LocationGroup = () => import("@/pages/LocationGroup/List.vue");
const LocationType = () => import("@/pages/LocationType/List.vue");



const Notifications = () => import("@/pages/Notifications/Notifications");
const Client = () => import("@/pages/Client/List.vue");
const Goods = () => import("@/pages/Goods/List.vue");
const GoodsUnit = () => import("@/pages/GoodsUnit/List.vue");
const Staff = () => import("@/pages/Staff/List.vue");
const Role = () => import("@/pages/Role/List.vue");
const DefaultData = () => import("@/pages/DefaultData/List.vue");



const appName = " | Ceta Booking";

const routes = [
  {
    path: "/",
    component: LayoutIndex,
    redirect: "/home",
    meta: {
      auth: true,
    },
    children: [
      {
        path: "home",
        name: "Trang chủ",
        component: Home,
        meta: {
          auth: true,
        },
      },
      {
        path: "dashboard",
        name: "Tổng quan",
        component: Dashboard,
        meta: {
          auth: true,
          title: "Tổng quan " + appName,
        },
      },
      {
        path: "order-vehicle",
        name: "Đặt xe",
        component: OrderVehicle,
        meta: {
          auth: true,
          title: "Đặt xe " + appName,
        },
      },
      {
        path: "order-setup",
        name: "Thiết lập đơn hàng",
        component: OrderSetup ,
        meta: {
          auth: true,
          title: "Thiết lập đơn hàng " + appName,
        },
      },
      {
        path: "calendar",
        name: "Lịch biểu",
        component: Calendar,
        meta: {
          auth: true,
          title: "Lịch biểu " + appName,
        },
      },
      {
        path: "order-customer",
        name: "Đơn hàng",
        component: OrderCustomer,
        meta: {
          auth: true,
          title: "Đơn hàng " + appName,
        },
      },
      {
        path: "order",
        name: "Đơn hàng vận tải",
        component: Order,
        meta: {
          auth: true,
          title: "Đơn hàng vận tải " + appName,
        },
      },
      {
        path: "location",
        name: "Địa chỉ",
        component: Location,
        meta: {
          auth: true,
          title: "Địa chỉ " + appName,
        },
      },
      {
        path: "location-group",
        name: "Nhóm địa điểm",
        component: LocationGroup,
        meta: {
          auth: true,
          title: "Nhóm địa điểm " + appName,
        },
      },
      {
        path: "location-type",
        name: "Loại địa điểm",
        component: LocationType,
        meta: {
          auth: true,
          title: "Loại địa điểm " + appName,
        },
      },
      {
        path: "notifications",
        name: "Thông báo",
        component: Notifications,
        meta: {
          auth: true,
          title: "Thông báo" + appName,
        },
      },
      {
        path: "client",
        name: "Khách hàng",
        component: Client,
        meta: {
          auth: true,
          title: "Khách hàng" + appName,
        },
      },
      {
        path: "staff",
        name: "Nhân viên",
        component: Staff,
        meta: {
          auth: true,
          title: "Nhân viên" + appName,
        },
      },
      {
        path: "default-data",
        name: "Dữ liệu mặc định",
        component: DefaultData,
        meta: {
          auth: true,
          title: "Dữ liệu mặc định" + appName,
        },
      },
      {
        path: "goods",
        name: "Hàng hoá",
        component: Goods,
        meta: {
          auth: true,
          title: "Hàng hoá" + appName,
        },
      },
      {
        path: "goods-unit",
        name: "Đơn vị hàng hoá",
        component: GoodsUnit,
        meta: {
          auth: true,
          title: "Đơn vị hàng hoá" + appName,
        },
      },
      {
        path: "role",
        name: "Vai trò",
        component: Role,
        meta: {
          auth: true,
          title: "Vai trò" + appName,
        },
      },

      {
        path: "order-client",
        name: "Đơn hàng khách hàng",
        component: OrderClient,
        meta: {
          auth: true,
          title: "Đơn hàng " + appName,
        },
      },
      {
        path: "dashboard-client",
        name: "Tổng quan khách hàng",
        component: DashboardClient,
        meta: {
          auth: true,
          title: "Đơn hàng " + appName,
        },
      },
      {
        path: "calendar-client",
        name: "Lịch biểu khách hàng",
        component: CalendarClient,
        meta: {
          auth: true,
          title: "Lịch biểu " + appName,
        },
      },
    ],
  },
  {
    path: "/login",
    name: "Đăng nhập",
    component: Login,
    meta: {
      auth: false,
      title: "Đăng nhập" + appName,
    },
  },
  { path: "*", component: NotFound },
  { path: "/access-denied", component: AccessDenied },
];

export default routes;
