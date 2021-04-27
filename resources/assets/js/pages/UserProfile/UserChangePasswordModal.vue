<template>
  <transition name="modal">
    <div v-if="showModal">
      <div class="modal modal-mask" style="display: block">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="title">
                Thay đổi mật khẩu
                <span class="extra-title muted"></span>
              </h5>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="current_password">Mật khẩu hiện tại</label>
                <input
                  v-validate="'required'"
                  name="current_password"
                  type="password"
                  class="form-control"
                  id="current_password"
                />
                <p v-show="errors.has('current_password')" class="text-danger">
                  <small>{{ errors.first('current_password') }}</small>
                </p>
              </div>
              <div class="form-group">
                <label for="password">Mật khẩu mới</label>
                <input
                  v-validate="'required|min:6'"
                  type="password"
                  class="form-control"
                  id="password"
                  ref="password"
                />
                <p class="help-block" style="font-size: 95% !important;">
                  <small>Mật khẩu phải có ít nhất 6 ký tự và trùng với trường xác nhận.</small>
                </p>
                <p v-show="errors.has('password')" class="text-danger">
                  <small>{{ errors.first('password') }}</small>
                </p>
              </div>
              <div class="form-group">
                <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                <input
                  v-validate="'required|confirmed:password'"
                  name="password_confirmation"
                  type="password"
                  class="form-control"
                  id="password_confirmation"
                />
                <p v-show="errors.has('password_confirmation')" class="text-danger">
                  <small>{{ errors.first('password_confirmation') }}</small>
                </p>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-cancel" @click="hide()">Đóng</button>
              <button type="submit" class="btn btn-save" @click="validateBeforeSubmit()">Lưu</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>
<script>
import axios from "axios";
// import { Validator } from "vee-validate";

// const dict = {
//   custom: {
//     current_password: {
//       required: "Mật khẩu mới là bắt buộc"
//     },
//     password: {
//       required: "Mật khẩu mới là bắt buộc",
//       min: "Mật khẩu mới có ít nhất 6 ký tự"
//     },
//     password_confirmation: {
//       required: "Xác nhận mật khẩu mới là bắt buộc",
//       confirmed: "Xác nhận mật khẩu mới và mật khẩu mới phải trùng nhau"
//     }
//   }
// };

// Validator.localize("en", dict);

export default {
  data: function() {
    return {
      showModal: false
    };
  },
  props: {},
  computed: {},
  methods: {
    changePassword() {
      var vm = this;
      let loader = this.$loading.show();
      axios
        .post("c-user/change-password", {
          current_password: $("#current_password").val(),
          password: $("#password").val()
        })
        .then(response => {
          loader.hide();
          this.$notifications.setOptions({
            type: "String",
            timeout: 3000,
            horizontalAlign: "center",
            verticalAlign: "top"
          });
          if (response.data.status == "success") {
            this.$notify({
              message: response.data.msg,
              type: "success"
            });
            vm.hide();
          } else {
            this.$notify({
              message: response.data.msg,
              type: "danger"
            });
          }
        })
        .catch(error => {
          loader.hide();
        });
    },
    show() {
      this.showModal = true;
    },
    hide() {
      this.showModal = false;
    },
    validateBeforeSubmit() {
      var vm = this;
      this.$validator
        .validateAll()
        .then(function(response) {
          vm.changePassword();
        })
        .catch(function(e) {
        });
    }
  }
};
</script>
<style lang="scss" scoped>
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

.modal-header {
  .title {
    font-size: 20px;
    font-weight: bold;
  }
}

.modal-body {
  .form-group {
    label {
      font-size: 1rem;
    }
  }
}

.modal-footer {
  padding: 0;
  padding-right: 1rem;
  .btn-save {
    background-color: #1976d2;
    border: none;
  }
  .btn-cancel {
    background-color: transparent;
    border: none;
    color: #000000;
  }
}

.limiter {
  width: 100%;
  margin: 0 auto;
}
label {
  font-weight: bold;
}
</style>
