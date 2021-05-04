<template>
  <div class="container">
    <div class="row justify-content-md-center" style="margin-top: 100px">
      <div class="col-6">
        <div class="card card-default">
          <h4 class="card-header">Đăng Ký</h4>
          <div class="card-body">
            <div class="alert alert-danger" v-if="has_error && !success">
              <p v-if="error == 'registration_validation_error'">
                Lỗi xác thực.
              </p>
              <p v-else>
                Lỗi, không thể đăng ký vào lúc này. Nếu sự cố vẫn tiếp diễn, vui
                lòng liên hệ với quản trị viên.
              </p>
            </div>
            <form
              autocomplete="off"
              @submit.prevent="register"
              v-if="!success"
              method="post"
            >
              <div
                class="form-group"
                v-bind:class="{ 'has-error': has_error && errors.name }"
              >
                <label for="name">Họ tên</label>
                <input
                  type="text"
                  id="fullname"
                  class="form-control"
                  placeholder="Họ tên"
                  v-model="full_name"
                />
                <span class="help-block" v-if="has_error && errors.full_name">{{
                  errors.full_name
                }}</span>
              </div>
              <div
                class="form-group"
                v-bind:class="{ 'has-error': has_error && errors.email }"
              >
                <label for="email">Email</label>
                <input
                  type="email"
                  id="email"
                  class="form-control"
                  placeholder="user@example.com"
                  v-model="email"
                />
                <span class="help-block" v-if="has_error && errors.email">{{
                  errors.email
                }}</span>
              </div>
              <div
                class="form-group"
                v-bind:class="{ 'has-error': has_error && errors.name }"
              >
                <label for="username">Tài khoản</label>
                <input
                  type="text"
                  id="username"
                  class="form-control"
                  placeholder="Tài khoản"
                  v-model="username"
                />
                <span class="help-block" v-if="has_error && errors.username">{{
                  errors.username
                }}</span>
              </div>
              <div
                class="form-group"
                v-bind:class="{ 'has-error': has_error && errors.password }"
              >
                <label for="password">Mật khẩu</label>
                <input
                  type="password"
                  id="password"
                  class="form-control"
                  v-model="password"
                />
                <span class="help-block" v-if="has_error && errors.password">{{
                  errors.password
                }}</span>
              </div>
              <div
                class="form-group"
                v-bind:class="{ 'has-error': has_error && errors.password }"
              >
                <label for="password_confirmation">Xác nhận mật khẩu</label>
                <input
                  type="password"
                  id="password_confirmation"
                  class="form-control"
                  v-model="password_confirmation"
                />
              </div>
              <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Đăng ký</button>
               <router-link :to="'login'"> <button class="btn btn-primary">Đăng nhập</button> </router-link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      full_name: "",
      email: "",
      username,
      password: "",
      password_confirmation: "",
      has_error: false,
      error: "",
      errors: {},
      success: false,
    };
  },
  methods: {
    register() {
      var app = this;
      this.$auth.register({
        data: {
          full_name: app.full_name,
          email: app.email,
          username:app.username,
          password: app.password,
          password_confirmation: app.password_confirmation,
        },
        success: function () {
          app.success = true;
            this.$message.success('Bạn đã đang ký thành công');
          this.$router.push({
            name: "login"
          });
        },
        error: function (res) {
          // console.log(res.response.data.errors)
          app.has_error = true;
          app.error = res.response.data.error;
          app.errors = res.response.data.errors || {};
        },
      });
    },
  },
};
</script>