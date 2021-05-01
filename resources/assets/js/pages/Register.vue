<template>
 <div class="container">
    <a-card title="Đăng ký" :bordered="true" style="width: 600px ; margin:auto">
      <a-form
    id="components-form-demo-normal-login"
    class="login-form "
    :form="form"
    @submit.prevent="register"
    :label-col="{ span: 7 }" :wrapper-col="{ span: 16 }"
  >
    <a-form-item label="Họ tên">
      <a-input
        placeholder="Họ tên" v-model="full_name"
      >
        <a-icon slot="prefix" type="user" style="color: rgba(0,0,0,.25)" />
      </a-input>
    </a-form-item>
    <a-form-item label="Email">
      <a-input
        placeholder="Email" v-model="email"
      >
        <a-icon slot="prefix" type="email" style="color: rgba(0,0,0,.25)" />
      </a-input>
    </a-form-item>
     <a-form-item label="Tài khoản">
      <a-input
        placeholder="Tài khoản" v-model="username"
      >
        <a-icon slot="prefix" type="user" style="color: rgba(0,0,0,.25)" />
      </a-input>
    </a-form-item>
    <a-form-item label="Mật khẩu">
      <a-input-password 
        type="password" v-model="password"
        placeholder="Mật khẩu"
      >
        <a-icon slot="prefix" type="lock" style="color: rgba(0,0,0,.25)" />
      </a-input-password >
    </a-form-item>
    <a-form-item label="Xác nhận lại mật khẩu">
      <a-input-password 
        type="password" v-model="password_confirmation"
        placeholder="Xác nhận lại mật khẩu"
      >
        <a-icon slot="prefix" type="lock" style="color: rgba(0,0,0,.25)" />
      </a-input-password >
    </a-form-item>
    <a-form-item>
      
      <a-button type="primary" html-type="submit" class="login-form-button">
        Đăng Ký
      </a-button>
      Hoặc
      <router-link :to="'/login'"> Đăng Nhập Ngay!</router-link>
   
    </a-form-item>
  </a-form>
    </a-card>
  </div>
</template>
<script>
const key = 'updatable';
  export default {
    data() {
      return {
        loader: false,
        error: false,
        login_img: require("@/assets/img/login.png"),
        logo_img: require("@/assets/img/logo.png"),
        full_name:'',
        username: '',
        email: '',
        password: '',
        password_confirmation: '',
        has_error: false,
        error: '',
        errors: {},
        success: false
      }
    },
    methods: {
      register() {
        
        var app = this
        this.$auth.register({
          data: {
            username: app.username,
            email: app.email,
            full_name:app.full_name,
            password: app.password,
            password_confirmation: app.password_confirmation
          },
          success: function () {
            app.success = true
            this.$message.success('Bạn đã đang ký thành công');
            this.$router.push({name: 'login'})
          },
          error: function (res) {
            // console.log(res.response.data.errors)
            app.has_error = true
            app.error = res.response.data.error
            app.errors = res.response.data.errors || {}
          }
        })
      }
    }
  }
</script>
<style scoped>
.container{
   min-height: 69vh;
    margin: auto;
    display: flex;
}
</style>