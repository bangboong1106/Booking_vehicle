<template>
  <a-layout id="components-layout-demo-top-side-2" style="min-height: 100vh">
    <a-layout-header class="header">
      <div class="logo">
        <a href="#" class="simple-text">
            <span class="ceta-logo-text">
            <img :src="logo" style="height: 42px"/> </span
            ></a
        >
      </div>
      <top-navbar ref="topNavbar"></top-navbar>
    </a-layout-header>
    <a-layout>

      <a-layout-sider
          width="200"
          v-model="collapsed"
          collapsible
          :trigger="null"

      >
        <a-menu
            theme="dark"
            mode="inline"
            :default-selected-keys="[this.currentRouterName]"
            :default-open-keys="selectedKey"
            :style="{ height: '100%', borderRight: 0  }"
        >
          <template
              v-if="this.$auth.user().customer_type == customerType.CUSTOMER"
          >

            <a-sub-menu :key="menu.key" v-for="menu in customerMenu">
              <span slot="title"
              ><a-icon :type="menu.icon"/><span>{{ menu.title }}</span></span
              >
              <a-menu-item
                  :key="child.key"
                  v-for="child in menu.children"
                  @click="({ item, key, keyPath }) => router(child.path)"
              >
                <a class="dashboard-link">{{ child.title }}</a>
              </a-menu-item>
            </a-sub-menu>
          </template>
          <template
              v-if="this.$auth.user().customer_type == customerType.STAFF"
          >
            <a-sub-menu :key="menu.key" v-for="menu in staffMenu">
              <span slot="title"
              ><a-icon :type="menu.icon"/><span>{{ menu.title }}</span></span
              >
              <a-menu-item
                  :key="child.key"
                  v-for="child in menu.children"
                  @click="({ item, key, keyPath }) => router(child.path)"
              >
                <a class="dashboard-link">{{ child.title }}</a>
              </a-menu-item>
            </a-sub-menu>
          </template>
          <template
              v-if="this.$auth.user().customer_type == customerType.CLIENT"
          >
            <a-sub-menu :key="menu.key" v-for="menu in clientMenu">
              <span slot="title"
              ><a-icon :type="menu.icon"/><span>{{ menu.title }}</span></span
              >
              <a-menu-item
                  :key="child.key"
                  v-for="child in menu.children"
                  @click="({ item, key, keyPath }) => router(child.path)"
              >
                <a class="dashboard-link">{{ child.title }}</a>
              </a-menu-item>
            </a-sub-menu>
          </template>
          <a-icon
              :style="widthCollapse"
              class="trigger"
              :type="collapsed ? 'right' : 'left'"
              @click="changeSider"
          />
        </a-menu>
      </a-layout-sider>
      <a-layout style="">
        <a-layout-content :style="{ background: '#fff' }">
          <dashboard-content @click.native="toggleSidebar"></dashboard-content>
        </a-layout-content>
      </a-layout>
    </a-layout>
  </a-layout>
</template>
<style lang="scss">
.dashboard-link {
  text-decoration: none;
  color: #000;
}

.ant-layout-header.header {
  background: #11509b;
  height: 42px;
  padding: 0 18px;
}

.logo {
  width: 120px;
  height: 100%;
  background: #11509b;
  float: left;
  display: flex;
  align-items: center;
}

.simple-text {
  color: #fff;
}

ul.ant-menu.ant-menu-horizontal.ant-menu-root.ant-menu-light {
  display: flex;
  justify-content: flex-end;
}
.trigger{
  position: fixed;
  bottom: 0;
  z-index: 1;
  height: 48px;
  color: #fff;
  line-height: 48px!important;
  text-align: center!important;
  background: #002140;
  cursor: pointer;
  transition: all 0.2s;
}

.ant-menu-dark .ant-menu-submenu-selected,
{
  background-color: #1e456b!important;
}
</style>
<script>
import TopNavbar from "./TopNavbar.vue";
import ContentFooter from "./ContentFooter.vue";
import DashboardContent from "./Content.vue";
import MobileMenu from "./MobileMenu";
import constant from "@/constant";
import EventBus from "@/event-bus";
export default {
  components: {
    TopNavbar,
    ContentFooter,
    DashboardContent,
    MobileMenu,
  },
  data() {
    let selectedKey =
        this.$auth.user().customer_type == constant.customerType.CLIENT
            ? ["dashboard-client"]
            : ["dashboard"];
    return {
      widthCollapse:'width : 200px',
      logo: require("@/assets/img/logo_2.png"),
      logoCollapsed: require("@/assets/img/McLean-logo-3.png"),
      collapsed: false,
      customerType: constant.customerType,
      selectedKey,
      clientMenu: [
        {
          key: "overview",
          icon: "pie-chart",
          title: "Tổng quan",
          children: [
            {
              key: "dashboard-client",
              title: "Tổng quan",
              path: "dashboard-client",
            },
            {
              key: "calendar-client",
              title: "Lịch biểu",
              path: "calendar-client",
            },
          ],
        },
        {
          key: "order",
          icon: "dropbox",
          title: "Đơn hàng",
          children: [
            {
              key: "order-client",
              title: "Đơn hàng",
              path: "order-client",
            },
          ],
        },
      ],
      staffMenu: [
        {
          key: "overview",
          icon: "pie-chart",
          title: "Tổng quan",
          children: [
            {
              key: "clock-circle",
              title: "Tổng quan",
              path: "dashboard",
            },
            {
              key: "calendar",
              title: "Lịch biểu",
              path: "calendar",
            },
          ],
        },
        {
          key: "order",
          icon: "dropbox",
          title: "Đơn hàng",
          children: [
            {
              key: "order-customer",
              title: "Đơn hàng",
              path: "order-customer",
            },
            {
              key: "order",
              title: "Đơn hàng vận tải",
              path: "order",
            },
          ],
        },
        {
          key: "user",
          icon: "user",
          title: "Người dùng",

          children: [
            {
              key: "client",
              title: "Khách hàng",
              path: "client",
            },
          ],
        },
      ],
      customerMenu: [
        {
          key: "overview",
          icon: "pie-chart",
          title: "Tổng quan",
          children: [
            {
              key: "dashboard",
              title: "Tổng quan",
              path: "dashboard",
            },
            {
              key: "calendar",
              title: "Lịch biểu",
              path: "calendar",
            },
          ],
        },
        {
          key: "order",
          icon: "dropbox",
          title: "Đơn hàng",
          children: [
            {
              key: "order-customer",
              title: "Đơn hàng",
              path: "order-customer",
            },
            {
              key: "order",
              title: "Đơn hàng vận tải",
              path: "order",
            },
          ],
        },
        {
          key: "user",
          icon: "user",
          title: "Người dùng",

          children: [
            {
              key: "client",
              title: "Khách hàng",
              path: "client",
            },
            {
              key: "staff",
              title: "Nhân viên",
              path: "staff",
            },
            {
              key: "default-data",
              title: "Dữ liệu mặc định",
              path: "default-data",
            },
          ],
        },
        {
          key: "category",
          icon: "environment",
          title: "Danh mục",
          children: [
            {
              key: "location",
              title: "Địa điểm",
              path: "location",
            },
            {
              key: "location-group",
              title: "Nhóm địa điểm",
              path: "location-group",
            },
            {
              key: "location-type",
              title: "Loại địa điểm",
              path: "location-type",
            },
            {
              key: "goods",
              title: "Hàng hoá",
              path: "goods",
            },
            {
              key: "goods-unit",
              title: "Đơn vị",
              path: "goods-unit",
            },
          ],
        },
      ],
    };
  },
  methods: {
    changeSider() {
      if (this.logo == require("@/assets/img/logo_2.png")) {
        this.logo = this.logoCollapsed;
        this.widthCollapse = 'width : 80px'
      } else {
        this.logo = require("@/assets/img/logo_2.png");
        this.widthCollapse = 'width : 200px'
      }
      this.collapsed = !this.collapsed;
      EventBus.$emit('renderFullCalendar')
    },
    toggleSidebar() {
      if (this.$sidebar.showSidebar) {
        this.$sidebar.displaySidebar(false);
      }
    },
    router(path) {
      this.$router.push({path: path});
    },
  },
  computed:{
    currentRouterName(){
      return this.$route.path.replace('/','');
    }
  }
};
</script>
