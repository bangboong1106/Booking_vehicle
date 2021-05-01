<template>
  <div class="container">
    <a-card
      title="Đăng nhập"
      :bordered="true"
      style="width: 600px; margin: auto"
    >
      <a-form
        id="components-form-demo-normal-login"
        :form="form"
        class="login-form"
        @submit.prevent="login"
        :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }"
      >
        <a-form-item label="Tài khoản">
          <a-input
            v-decorator="[
              'username',
              {
                rules: [{ required: true, message: 'Yêu cầu nhập tài khoản!' }],
              },
            ]"
            v-model="username"
            placeholder="Username"
          >
            <a-icon
              slot="prefix"
              type="user"
              style="color: rgba(0, 0, 0, 0.25)"
            />
          </a-input>
        </a-form-item>
        <a-form-item label="Mật khẩu">
          <a-input-password
            v-decorator="[
              'password',
              {
                rules: [{ required: true, message: 'Yêu cầu nhập mật khẩu!' }],
              },
            ]"
            v-model="password"
            type="password"
            placeholder="Password"
          >
            <a-icon
              slot="prefix"
              type="lock"
              style="color: rgba(0, 0, 0, 0.25)"
            />
          </a-input-password>
        </a-form-item>
        <a-form-item :wrapper-col="{ span: 24 }">
          <a-button type="primary" html-type="submit" class="login-form-button" style="margin:0 30px">
            Đăng Nhập
          </a-button >
          <router-link :to="'/register'">
          <a-button type="primary" class="login-form-button">
            Đăng Ký
          </a-button></router-link>
        </a-form-item>
      </a-form>
    </a-card>
  </div>
</template>

<script>
import axios from "axios";
export default {
  beforeCreate() {
    this.form = this.$form.createForm(this, {
      name: "normal_login",
    });
  },
  data() {
    return {
      loader: false,
      username: null,
      password: null,
      error: false,
    };
  },
  methods: {
    login() {
      var app = this;
      this.$auth.login({
        params: {
          username: app.username,
          password: app.password,
        },
        rememberMe: true,
        // redirect: '/order',
        fetchUser: true,
      });
      this.form.validateFields((err, values) => {
        if (!err) {
          const params = {
            username: values.username,
            password: values.password,
          };
          
          axios.post("auth/login", params).then((response) => {
            if (response.data.status == "success") {
              this.$message.loading({
                content: "Đang đăng nhập...",
              });
              setTimeout(() => {
                this.$message.success({
                  content: "Đăng nhập thành công!",
                  duration: 2,
                });
                this.$router.push("home");
              }, 500);
            } else {
              this.$message.warning("Mật khẩu của bạn không chính xác");
            }
          });
        }
      });
    },
  },
};
</script>

<style scoped>
.login-form {
  margin: auto;
}

.container {
  min-height: 57vh;
  margin: auto;
  display: flex;
}
</style>
