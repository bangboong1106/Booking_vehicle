<template>
  <div class="container">
    <div class="row justify-content-md-center " style="margin-top:100px">
      <div class="col-6">
        <div class="card card-default">
          <h4 class="card-header">Đăng nhập</h4>
          <div class="card-body">
            <div class="alert alert-danger" v-if="has_error && !success">
              <p v-if="error == 'login_error'">Lỗi xác thực.</p>
              <p v-else>Tài khoản hoặc mật khẩu của bạn không chính xác</p>
            </div>
            <form autocomplete="off" @submit.prevent="login" method="post">
              <div class="form-group">
                <label for="username">Tài khoản</label>
                <input type="text" id="email" class="form-control" placeholder="Tài khoản" v-model="username" required>
              </div>
              <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" class="form-control" v-model="password" required>
              </div>
              <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Đăng nhâp</button>
                <router-link :to="'register'">  <button class="btn btn-primary">Đăng ký</button> </router-link>
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
        username: null,
        password: null,
        success: false,
        has_error: false,
        error: ''
      }
    },
    mounted() {
      //
    },
    methods: {
      login() {
        // get the redirect object
        var redirect = this.$auth.redirect()
        var app = this
        this.$auth.login({
          data: {
            username: app.username,
            password: app.password
          },
          success: function() {
            // handle redirection
            app.success = true
            const redirectTo = 'home'
            this.$message.success('Bạn đã đang nhập thành công');
            this.$router.push({name: redirectTo})
          },
          error: function() {
            app.has_error = true
            app.error = res.response.data.error
          },
          rememberMe: true,
          fetchUser: true
        })
      }
    }
  }
</script>