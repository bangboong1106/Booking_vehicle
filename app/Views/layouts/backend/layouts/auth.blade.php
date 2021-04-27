<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{!! isset($title) ? sprintf("%s | ", $title) : ''!!}{{config('constant.APP_NAME')}}</title>
    <meta name="description" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{public_url('favicon.png')}}">
    @include('layouts.backend.elements.structures.font_kanit')
    @stack('before-css')
    <?php

    $cssFiles = [
        'autoload/login',
    ];
    ?>
    {!! loadFiles($cssFiles, isset($area) ? $area : 'backend') !!}
    @stack('after-css')

</head>
<body class="auth widescreen">
<div class="logo-image"></div>
<div id="header">
    <a id="logo-link">
        <div class="page-content-logo">

        </div>
    </a>
</div>
<div class="login-form">
    <div class="wrapper-page-left">
        <div class="text-center">
            <a href="{{route('backend.login', ['return_url' => route('dashboard.index')])}}" class="logo-lg"><i
                        class="mdi mdi-radar"></i>
                <p style="font-size: 18px;line-height: 1.6;">NỀN TẢNG QUẢN LÝ <br> DOANH NGHIỆP VẬN TẢI</p></a>
        </div>
        <img class="logo-delivery-truck" src="{{public_url('css/backend/images/delivery-truck.svg')}}"
             alt="delivery-truck">
        <ul class="support-info">
            <li>
                <div class="icon-support"></div>
                <a class="" href="tel:{{config('constant.APP_HOTLINE')}}">Hotline: {{config('constant.APP_HOTLINE')}}</a>
            </li>
            <li>
                <div class="icon-mail"></div>
                <a class="" href="mailto:{{config('constant.APP_EMAIL_SUPPORT')}}">{{config('constant.APP_EMAIL_SUPPORT')}}</a>
            </li>
            <li>
                <div class="icon-use-tutorial"></div>
                <a class="" href="" target="_blank">Hướng dẫn
                    sử dụng</a>
            </li>
        </ul>
    </div>
    <div class="wrapper-page wrapper-page-right">
        @yield('content')
    </div>
</div>
<div class="footer-contact-content">
    <div class="container" style="text-align: center">
        <div id="copy-right">
            <span> Copyright © 2018 - <script>document.write(new Date().getFullYear())</script> {{config('constant.APP_NAME')}} | <a
                href="{{config('constant.APP_WEB')}}" style="color:white;" target="_blank">{{config('constant.APP_WEB')}}</a>

            </span>
        </div>
    </div>
</div>
{{--@stack('scripts')--}}
</body>
<script>

    //Kiem tra input co bi null hay khong
    displayErrorBorder();

    function displayErrorBorder() {
        let username = document.getElementById('username');
        let password = document.getElementById('password');
        let password_confirm = document.getElementById('password_confirmation');
        let email = document.getElementById('email');
        //focus input
        if (username != null) {
            username.addEventListener('keyup', function () {
                let border = this.parentNode;
                border.classList.remove('border-error-input');
                border.classList.add('border-primary-input');
                document.getElementById('username_error').style.display = 'none';
            })
        }
        if (password != null) {
            password.addEventListener('keyup', function () {
                let border = this.parentNode;
                border.classList.remove('border-error-input');
                border.classList.add('border-primary-input');
                document.getElementById('password_error').style.display = 'none';
            })
        }
        if (password_confirm != null) {
            password_confirm.addEventListener('keyup', function () {
                let border = this.parentNode;
                border.classList.remove('border-error-input');
                border.classList.add('border-primary-input');
                document.getElementById('password_confirmation_error').style.display = 'none';
            })
        }
        if (email != null) {
            email.addEventListener('keyup', function () {
                let border = this.parentNode;
                border.classList.remove('border-error-input');
                border.classList.add('border-primary-input');
                document.getElementById('email_error').style.display = 'none';
            })
        }
    }


    //show/hide password
    showAndHidePassword();

    function showAndHidePassword() {
        let eyes = document.querySelectorAll('.toggle-password');
        if (eyes != null) {
            for (let i = 0; i < eyes.length; i++) {
                eyes[i].addEventListener('click', function () {
                    let input = eyes[i].parentNode.childNodes[3];
                    // let input = document. getElementById('password');
                    if (input.type === 'password') {
                        this.classList.remove('hide-password');
                        this.classList.add('show-password');
                        input.type = 'text';
                    } else {
                        this.classList.remove('show-password');
                        this.classList.add('hide-password');
                        input.type = 'password';
                    }
                })
            }
        }
    }


    function LoadingButton() {
        this.innerHTML = 'Loading...';
        this.classList.add('stripes-button');
    }

    function errorMessage(param) {
        param.style.display = 'block';
        let border = param.parentNode.childNodes[1];
        border.classList.remove('border-primary-input');
        border.classList.add('border-error-input');     // class error message
        param.focus();
    }

    function validateNull(id, text, count) {
        let param = document.getElementById(id);
        if(param != null)
        {
            let valid = document.getElementById(id + '_error');
            if(param.value === '' || param.value === null)
            {
                valid.innerHTML = text;
                errorMessage(valid);
                hideLabel(id);
                param.focus();
                count++;
            }
        }
        return count;
    }

    function hideLabel(id)
    {
        let lab = document.getElementById('span-' + id);
        if(lab != null)
        {
            lab.style.display = 'none';
        }
    }

    function validate() {
        let count = 0;
        count = validateNull('username', '* Tên đăng nhập không được để trống', count);
        count = validateNull('password', '* Mật khẩu mới không được để trống', count);
        count = validateNull('password_confirmation', '* Mật khẩu mới không được để trống', count);
        count = validateNull('email', '* Email không được để trống', count);

        if(count === 0) {
            return true;
        } else {
            return false;
        }
    }

    //submit login form
    submitForm();

    function submitForm() {
        let submit = document.getElementById('login-submit');
        if (submit != null) {
            submit.addEventListener('click', function (e) {
                e.preventDefault();
                if (validate()) {
                    this.innerHTML = 'Loading...';
                    this.classList.add('stripes-button');
                    document.getElementById('login-form').submit();
                }
            })
        }
    }

    //get email form
    getEmail()

    function getEmail() {
        let email = document.getElementById('btn-get-email');
        if (email != null) {
            email.addEventListener('click', function (e) {
                e.preventDefault();
                if (validate()) {
                    this.innerHTML = 'Loading...';
                    this.classList.add('stripes-button');
                    document.getElementById('get-email-form').submit();
                }
            })
        }
    }

    //reset password option
    resetPassword()

    function resetPassword()
    {
        let reset = document.getElementById('btn-reset-password');
        if (reset != null) {
            reset.addEventListener('click', function (e) {
                e.preventDefault();
                if (validate()) {
                    this.innerHTML = 'Loading...';
                    this.classList.add('stripes-button');
                    document.getElementById('reset-form').submit();
                }
            })
        }
    }

    //
    setTimeout(displayHideNotification, 3000);

    function slideMessageToTop(param) {
        if (param != null) {
            let pos = 0;
            let id = setInterval(frame, 20);

            function frame() {
                if (pos === 120) {
                    clearInterval(id);
                } else {
                    pos++;
                    param.style.top = '-' + pos + 'px';
                }
            }
        }
    }

    function displayHideNotification() {
        let error = document.querySelector('#error_msg');
        let success = document.getElementById('success_msg');
        if (error != null) {
            slideMessageToTop(error);
        }

        if (success != null) {
            slideMessageToTop(success);
        }
    }
</script>
</html>
