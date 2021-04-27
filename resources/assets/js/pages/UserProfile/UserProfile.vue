<template>
  <div class="row userinfo-container">
    <h3 class="col-md-12">Thông tin tài khoản</h3>
    <span @click="closeUserInfo()" class="close-form">X</span>
    <div class="col-md-12">
    </div>
    <div class="col-md-12">
      <edit-profile-form @changePassword="changePassword()"></edit-profile-form>
    </div>
  </div>
</template>
<script>
import EditProfileForm from "@/pages/UserProfile/EditProfileForm.vue";

export default {
  components: {
    EditProfileForm,
  },
  methods: {
    changePassword: function() {
      this.$emit("changePassword");
    },
    closeUserInfo: function() {
      this.$emit("closeUserInfo");
    }
  }
};
</script>
<style scoped>
.userinfo-container {
  margin: 0;
  padding: 0 0 12px 0;
  position: fixed;
  right: 0;
  top: 0;
  overflow: auto;
  z-index: 100;
  background: #ffffff;
  height: 100vh;
}

.close-form {
  position: absolute;
  top: 12px;
  right: 15px;
  font-weight: bolder;
  width: 20px;
  height: 20px;
  text-align: center;
  border: 1px solid #414b5b;
  border-radius: 50%;
  line-height: 20px;
  background-color: #414b5b;
  color: #ffffff;
  cursor: pointer;
}

.userinfo-container h3 {
  font-size: 16px;
  margin-bottom: 12px;
  padding: 14px 20px;
  background: #11509b;
  color: #ffffff;
  font-weight: bold;
  height: 50px;
}
</style>
