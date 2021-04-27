<template>
  <card class="card edit-profile-form">
    <div>
      <form @submit.prevent>
        <input-text
          :fieldName="'customer_code'"
          :label="'Mã khách hàng'"
          :value="user.customer_code"
          :placeholder="'Mã khách hàng'"
        ></input-text>

        <input-text
          :fieldName="'full_name'"
          :label="'Khách hàng'"
          :value="user.full_name"
          :placeholder="'Khách hàng'"
        ></input-text>

        <input-text
          :fieldName="'delegate'"
          :label="'Người đại diện'"
          :value="user.delegate"
          :placeholder="'Người đại diện'"
        ></input-text>

         <input-text
          :fieldName="'mobile_no'"
          :label="'Số điện thoại'"
          :value="user.mobile_no"
          :placeholder="'Số điện thoại'"
        ></input-text>

        <input-text
          :fieldName="'tax_code'"
          :label="'Mã số thuế'"
          :value="user.tax_code"
          :placeholder="'Mã số thuế'"
        ></input-text>

        <input-text
          :fieldName="'current_address'"
          :label="'Địa chỉ'"
          :value="user.current_address"
          :placeholder="'Địa chỉ'"
        ></input-text>

        <input-text
          v-if="!isShowChangePassword"
          @changePassword="showChangePasswordForm()"
          :label="'Mật khẩu'"
          inputType="password"
          :value="'********'"
          :placeholder="'Mật khẩu'"
        ></input-text>

        <div v-if="isShowChangePassword">
          <h6>Đổi mật khẩu</h6>
          <label>Mật khẩu hiện tại</label>
          <input ref="currentPassword" type="password" />
          <label>Mật khẩu mới</label>
          <input
            ref="newPassword"
            type="password"
            placeholder="Nhập mật khẩu mới có ít nhất 6 ký tự"
          />
          <label>Xác nhận mật khẩu mới</label>
          <input ref="reNewPassword" type="password" placeholder="Nhập lại mật khẩu mới" />

          <div class="button-change-password-container">
            <button @click="cancelChangePassword()" class="cancel-button">Hủy</button>
            <button @click="changePassword()" class="save-button">Lưu</button>
            <!-- <span class="ti-check" @click="changePassword()"></span>
            <span class="ti-close" @click="cancelChangePassword()"></span>-->
          </div>
        </div>
        <div class="clearfix"></div>
      </form>
    </div>
  </card>
</template>
<script>
import axios from "axios";
import UserChangePasswordModal from "@/pages/UserProfile/UserChangePasswordModal";
import InputText from "@/pages/UserProfile/InputText";

export default {
  components: {
    UserChangePasswordModal,
    InputText
  },
  data() {
    return {
      user: {},
      isShowChangePassword: false
    };
  },
  methods: {
    updateProfile() {},
    showChangePasswordForm() {
      this.isShowChangePassword = true;
    },
    cancelChangePassword() {
      this.isShowChangePassword = false;
    },
    changePassword() {
      const params = {
        current_password: this.$refs.currentPassword.value,
        password: this.$refs.newPassword.value
      };
      const reNewPassword = this.$refs.reNewPassword.value;
      this.$notifications.setOptions({
        type: "String",
        timeout: 3000,
        horizontalAlign: "center",
        verticalAlign: "top"
      });
      if (reNewPassword !== params.password) {
        this.$notify({
          message: "Mật khẩu mới và Xác nhận mật khẩu mới phải khớp nhau",
          type: "danger"
        });
        return;
      }
      axios.post("c-user/change-password", params).then(response => {
        if (response.data.status == "success") {
          this.isShowChangePassword = false;
          this.$notify({
            message: response.data.msg,
            type: "success"
          });
        } else {
          this.$notify({
            message: response.data.msg,
            type: "danger"
          });
        }
      });
    },
    getUserInfo() {
      let loader = this.$loading.show();
      axios
        .get("c-user/detail", {})
        .then(response => {
          if (response && response.status === 200) {
            this.user = response.data[0];
          }
        })
        .finally(() => {
          loader.hide();
        });
    },
    logout() {
      var app = this;
      this.$auth.logout();
      this.$router.push("login");
    }
  },
  created() {
    this.getUserInfo();
  }
};
</script>
<style scoped lang="scss">
.edit-profile-form {
  box-shadow: none;
  .cancel-button {
    border: none;
    color: #37474f;
    font-weight: 500;
    font-size: 13px;
    padding: 3px 10px;
    border-radius: 5px;
  }

  .save-button {
    background-color: #1976d2;
    font-size: 13px;
    font-weight: 500;
    color: #ffffff;
    border: 1px solid #1976d2;
    padding: 3px 10px;
    border-radius: 5px;
    margin-left: 12px;
  }
  input {
    width: 100%;
    padding: 2px 5px;
    border: 1px solid #cccccc;
    &:focus {
      border-color: #1e88e5;
    }
  }
  h6 {
    margin: 15px 0;
    font-weight: bold;
  }

  label {
    font-weight: bold !important;
    font-size: 13px;
    color: #000000;
    margin-top: 10px;
  }

  .button-change-password-container {
    float: right;
    margin-top: 10px;
    span {
      cursor: pointer;
      &.ti-check {
        margin-right: 10px;
        &::before {
          color: blue;
        }
      }
    }
  }
}
</style>

<style lang="scss">
.edit-profile-form {
  .card-body {
    padding: 0;
  }
}
</style>
