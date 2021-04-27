<template>
  <div>
    <div class="footer-hrv-address">
      <div class="container">
        <a-row :gutter="[16, 16]">
          <a-col
            :span="12"
            :xs="{ span: 24 }"
            :sm="{ span: 12 }"
            class="widget-address widget-address-hcm"
          >
            <p class="title-address">CÔNG TY TNHH VẬN CHUYỂN Ô TÔ</p>
            <ul class="list-unstyled list-address">
              <li class="street-address">
                Địa chỉ: Ô 36, Lô 2, Khu đô thị Đền Lừ 1, Phường Hoàng Văn Thụ,
                Quận Hoàng Mai, TP Hà Nội.
              </li>
              <li class="tel">
                Giám đốc:
                <span
                  ><strong style="color: #00b14f">
                    NGUYỄN VĂN BẰNG</strong
                  ></span
                >
              </li>
              <li class="street-address">MST/CMND: 0106760674</li>
              <li class="street-address">Ngày cấp: 26/01/2015</li>
            </ul>
          </a-col>
          <a-col
            :span="12"
            :xs="{ span: 24 }"
            :sm="{ span: 12 }"
            class="widget-address widget-address-hcm"
          >
            <p class="title-address">Văn phòng</p>
            <ul class="list-unstyled list-address">
              <li class="street-address">
                Văn Phòng: Ô 43, Lô 13, KĐT Đền Lừ 1, Phường Hoàng Văn Thụ, Quận
                Hoàng Mai, TP Hà Nội
              </li>
              <li class="street-address">
                Kho Bãi tại Hà Nội: Quốc lộ 21, Xã Phù Cát, Huyện Quốc Oai, TP
                Hà Nội
              </li>
              <li class="street-address">
                Kho Bãi tại Đà Nẵng: Hòa An, Quận Cẩm Lệ, TP Đà Nẵng
              </li>
              <li class="street-address">
                Kho bãi và VPĐD tại TP Hồ Chí Minh :Đường Võ Chí Công, Quận Thủ
                Đức, TP Hồ Chí Minh
              </li>
              <li class="street-address">
                Kho bãi tại Bình Dương: Bình Thắng, TX Dĩ An, tỉnh Bình Dương
              </li>
            </ul>
          </a-col>
        </a-row>
      </div>
    </div>

    <div class="footer-hrv-bottom">
      <div class="container">
        <ul class="list-unstyled">
          <li>
               <a-icon type="facebook" style="color:#52c41a" />
            <a
              class="fter-txtbold"
              href="https://www.facebook.com/"
              target="_blank"
              rel="noopener"
              >VEHICLESIP</a
            >
          </li>
          <li>
            <a-icon type="mail"  theme="twoTone" two-tone-color="#52c41a" />
            <a
              class="fter-txtbold"
              target="_blank"
              href="https://join.skype.com/invite/w9a6dTloQ9os"
              rel="noopener"
              >VEHICLESIP@gmail.com</a
            >
          </li>
        
          <li>
           <a-icon type="phone"  theme="twoTone" two-tone-color="#52c41a"  />
            <a class="fter-txtbold" href="tel:0912731762">0912.731.762</a>
          </li>
        </ul>
        <p id="back-to-top">
          <a href="#"><i class="fa fa-angle-up"></i></a>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Footer",
};
</script>

<style scoped>
    .footer-hrv-address {
    padding: 15px 0 50px;
    background: #fafafa;
    border-top: 1px solid #e6e6e6;
}

.widget-address .title-address {
    font-size: 16px;
    color: #00b14f;
    margin: 20px 0 15px;
    text-transform: uppercase;
    font-weight: 600;
}

ul.list-address li {
    position: relative;
    padding-left: 20px;
    margin: 4px 0;
    list-style: none;
}

ul.list-address li:before {
    content: "";
    position: absolute;
    left: 6px;
    top: 8px;
    width: 4px;
    height: 4px;
    background: #546e7a;
    border-radius: 50%;
}

.footer-hrv-bottom {
    padding: 24px 0 35px;
    background: #f6f6f6;
    border-top: 1px solid #e6e6e6;
}

.footer-hrv-bottom ul {
    list-style: none;
    text-align: center;
    margin: 0;
}

.footer-hrv-bottom ul li {
    display: inline-block;
    position: relative;
}

@media (min-width: 1200px) {
    .widget-hrv-social .title-footer {
        white-space: nowrap;
    }

    .widget-hrv-social {
        margin-bottom: 60px;
    }

    .widget-hrv-appstore .title-footer span {
        display: block
    }
}

@media (min-width: 768px) {
    .footer-hrv-bottom ul li + li:before {
        content: "";
        display: inline-block;
        vertical-align: middle;
        width: 1px;
        height: 14px;
        background: #3d5067;
        margin: 0px 30px;
    }
}

@media (max-width: 991px) and (min-width: 768px) {
    ul.navbar-social li {
        width: 30px;
        height: 30px;
    }
}

@media (max-width: 767px) {
    .footer-hrv-linklists {
        padding: 0;
    }

    ul.navbar-footerlink {
        width: 100%;
        float: left;
    }

    ul.navbar-footerlink li {
        width: 50%;
        float: left
    }

    ul.navbar-footerlink li, ul.list-footerlink li {
        margin: 10px 0;
    }

    ul.navbar-appstore li {
        display: inline-block;
    }

    .widget-linklists .title-footer {
        position: relative;
        display: block;
        font-size: 14px;
        margin: 0;
        padding: 15px 15px;
    }

    .widget-linklists .logo-hrv-footer {
        margin: 0;
        padding: 15px 15px 10px;
    }

    .widget-linklists .togged-footer:before {
        content: "\f107";
        font-family: "FontAwesome";
        position: absolute;
        top: 12px;
        right: 20px;
        text-align: center;
        font-size: 22px;
        transition: all 0.4s ease-in-out 0s;
        -moz-transition: all 0.4s ease-in-out 0s;
        -o-transition: all 0.4s ease-in-out 0s;
        -webkit-transition: all 0.4s ease-in-out 0s;
        -ms-transition: all 0.4s ease-in-out 0s;
    }

    .widget-linklists .logo-hrv-footer.togged-footer:before {
        top: 17px;
    }

    .widget-linklists .togged-footer.opened:before {
        -moz-transform: rotate(-180deg);
        -webkit-transform: rotate(-180deg);
        -o-transform: rotate(-180deg);
        -ms-transform: rotate(-180deg);
        transform: rotate(-180deg);
    }

    .widget-footer-mb {
        padding: 0;
        border-bottom: 1px solid #e6e6e6;
    }

    .widget-footer-mb.widget-border-b0 {
        border-bottom: 0;
    }

    .widget-footer-mb .footer-collapse {
        display: none;
        padding: 5px 15px;
        background: #fff;
    }

    .widget-footer-mb .footer-collapse.navbar-social,
    .widget-footer-mb .footer-collapse.navbar-appstore {
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .widget-footer-mb .widget-hrv-social {
        border-bottom: 1px solid #e6e6e6;
    }

    .footer-hrv-bottom ul li {
        display: block;
    }

    .footer-hrv-bottom ul li + li span.fter-txtbold {
        display: block
    }
}
</style>