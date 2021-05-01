<template>
<div>
 <a-menu
            theme="dark"
            mode="horizontal"
            :style="{ lineHeight: '64px', backgroundColor: 'transparent' }"
          v-if="!$auth.check()" 
          >
            <a-menu-item
              :style="{
                backgroundColor: 'transparent',
                textTransform: 'uppercase',
                fontWeight: 'bold',
              }"
              :key="menu.key"
              v-for="menu in routes.unlogged"
            >
              <router-link :to="menu.path">{{ menu.name }}</router-link>
            </a-menu-item>
 </a-menu>
 <a-menu
            theme="dark"
            mode="horizontal"
            :style="{ lineHeight: '64px', backgroundColor: 'transparent' }"
          v-if="$auth.check()"
          >
            <a-menu-item
              :style="{
                backgroundColor: 'transparent',
                textTransform: 'uppercase',
                fontWeight: 'bold',
              }"
            >
            </a-menu-item>
            <a-menu-item
          key="user"
          :style="{ padding: '0 8px' }"
          @click="showUserInfo"
      >
        <a-icon type="user" :style="{ marginRight: 0, fontSize: '20px' }"/>
        {{ this.$auth.user().username }}
      </a-menu-item>
 </a-menu>
 <a-drawer
        width="320"
        placement="right"
        :closable="false"
        :visible="visible"
        @close="onClose"
    >
      <a-row>
        <a-col :span="8"></a-col>
        <a-col :span="12">
          <a-avatar shape="square" :size="64" icon="user"/>
        </a-col>
      </a-row>
      <a-row>
        <a-col :span="24">
          <a-descriptions
              title="Thông tin người dùng"
              :column="1"
              :size="'middle'"
          >
            <a-descriptions-item label="Họ và tên">
              {{ this.$auth.user().full_name }}
            </a-descriptions-item>
            <a-descriptions-item label="Email">
              {{ this.$auth.user().email }}
            </a-descriptions-item>
            <a-descriptions-item label="Tên đăng nhập">
              {{ this.$auth.user().username }}
            </a-descriptions-item>
            <a-descriptions-item label="Số điện thoại">
              {{ this.$auth.user().mobile_no }}
            </a-descriptions-item>
          </a-descriptions>
        </a-col>
      </a-row
      >
      <a-button type="danger" block @click="logout"> Đăng xuất</a-button>
    </a-drawer>
        </div>
</template>
<script>
export default {
  data() {
    return {
      routes: {
        // UNLOGGED
        unlogged: [
          { name: "Đăng ký", path: "register" },
          { name: "Đăng nhập", path: "login" },
        ],
        // LOGGED USER
        user: [{ name: "Trang chủ", path: "home" }],
        // LOGGED ADMIN
        admin: [{ name: "Dashboard", path: "admin.dashboard" }],
      },
      visible:false
    };
  },
  mounted() {
    //
  },
  methods: {
      showUserInfo(){
          this.visible=true
      },
      onClose(){
          this.visible=false
      },
      logout() {
        this.$message.success('Bạn đã đăng xuất thành công!');
        this.visible=false
      this.$auth.logout();
      this.$router.push("login");
    },
  },
};
</script>

<style>
.navbar {
  margin-bottom: 30px;
}
.ant-menu.ant-menu-dark .ant-menu-item-selected, .ant-menu-submenu-popup.ant-menu-dark .ant-menu-item-selected{
    background-color:transparent
}
</style>