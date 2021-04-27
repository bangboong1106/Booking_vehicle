<template>
  <a-layout>
    <a-layout-header class="header primary-background-color" collapsible>
      <div class="container wrap">
        <div class="logo" @click="goToHome">
          <img :src="require('../../assets/img/logo.png')" alt="" style="height: 64px;cursor: pointer">
        </div>
        <div class="menu-topbav">
          <a-menu
              theme="dark"
              mode="horizontal"
              :style="{ lineHeight: '64px', backgroundColor: 'transparent' }"

          >
            <a-menu-item :style="{ backgroundColor: 'transparent',textTransform:'uppercase',fontWeight:'bold' }"
                         :key="menu.key"
                         v-for="menu in homeMenu"

            >
              <router-link :to="menu.path">{{ menu.title }}</router-link>
            </a-menu-item
            >
          </a-menu>
        </div>
        <a-badge :key="key" :count="this.quantity">
          <a-icon class="cart-icon" type="shopping" @click="showDrawer"></a-icon>
        </a-badge>

        <div class="cart-mobile">
          <a-drawer
              placement="right"
              :closable="true"
              :visible="visible"
              :width="'100%'"
              :after-visible-change="afterVisibleChange"
              @close="onClose"
          >
            <div class="cart-header">
              <h3 style="margin: 0">Thông tin giỏ hàng (<span style="color: red">{{ quantity }}</span>)</h3>
              <span @click="onClose" style="cursor: pointer">X</span>
            </div>

            <a-divider style="margin : 0"/>
            <cart-list @getQuantityItemsInCart="this.getQuantity" @closeCart ="onClose"></cart-list>
          </a-drawer>

        </div>
        <div class="cart-tablet">
          <a-drawer
              placement="right"
              :closable="true"
              :visible="visible"
              :width="'100%'"
              :after-visible-change="afterVisibleChange"
              @close="onClose"
          >
            <div class="cart-header">
              <h3 style="margin: 0">Thông tin giỏ hàng (<span style="color: red">{{ quantity }}</span>)</h3>
              <span @click="onClose" style="cursor: pointer">X</span>
            </div>

            <a-divider style="margin : 0"/>
            <cart-list @getQuantityItemsInCart="this.getQuantity" @closeCart ="onClose"></cart-list>
          </a-drawer>

        </div>
      </div>

    </a-layout-header>

  </a-layout>
</template>
<script>
import CartList from "@/components/Carts/CartList";
import EventBus from "@/event-bus";

export default {
  components: {
    "cart-list": CartList
  },
  data() {
    var quantity = JSON.parse(localStorage.getItem("cartDetail")).length;
    return {
      homeMenu: [
        {
          key: "home",
          title: "Trang chủ",
          path: 'home'
        },
        {
          key: "services",

          title: "Dịch vụ",
          path: "serivce"

        },
        {
          key: "cars",

          title: "Xe",
          path: 'list-car'

        },
      ],
      visible: false,
      quantity,
      key: 0
    }
  },
  methods: {
    goToHome() {
      this.$router.push('/home')
    },
    afterVisibleChange(val) {

    },
    showDrawer() {
      this.visible = true;
    },
    onClose() {
      this.visible = false;
    },
    getQuantity() {
      this.quantity = JSON.parse(localStorage.getItem("cartDetail")).length;
      this.key++;
    }
  },

  created() {
    EventBus.$on('getQuantityItemsInCart', this.getQuantity)
  },
  destroyed() {
    EventBus.$off('getQuantityItemsInCart', this.getQuantity)

  }
};
</script>

<style scoped>
span.ant-badge {
  display: none;
}

.cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: absolute;
  top: 0px;
  width: 100%;
  border-top: 1px solid rgb(232, 232, 232);
  padding: 24px;
  text-align: right;
  left: 0px;
  background: rgb(255, 255, 255);
  z-index: 10;
}

.cart-header > h3 {
  margin: 0;
  text-transform: uppercase;
}

.cart-header > span {
  font-size: 18px;
}


.ant-row-rtl .logo {
  float: right;
  margin: 16px 0 16px 24px;
}

.header {
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
}

.wrap {
  display: flex;
  justify-content: space-between;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.home-slider-item {
  object-fit: cover;
  height: 700px;
  line-height: 700px;
  overflow: hidden;
  width: 100%;
}

.overlay {
  position: relative;
  opacity: 1;
}

.overlay::after {
  content: "";
  display: block;
  background: rgba(0, 0, 0, 0.7);
  top: 0;
  height: 100%;
  width: 100%;
}

.menu-sider {
  display: none;
}

.logo {
  display: flex;
  align-items: center;
  justify-content: center;
}

.cart {
  display: none;
}
.cart-tablet{
  display: none;
}

@media (max-width: 576px) {
  .home-slider-item {
    height: 280px;
  }

  .cart-mobile {
    display: block;
  }

  .logo {
    padding-top: 15px;
  }

  .header {
    padding: 0 15px !important;
  }

  .header > .container.wrap {
    display: flex;
    flex-direction: column;
  }

  .header.ant-layout-header {
    height: auto;
    text-align: center;
  }

  .container.wrap {
    position: relative;
  }

  span.ant-badge {
    display: block;
    position: absolute;
    right: 0;
    font-size: 23px;
    color: white;
    top: 18px;
    cursor: pointer;
  }

  .cart-icon {
    color: rgba(255, 255, 255, 0.65)
  }

  .cart-icon:hover {
    color: #fff;
  }
}

@media (min-width: 577px) and (max-width: 890px) {
  .home-slider-item {
    height: 480px;
  }
  .header {
    padding: 0 15px !important;
  }
  .cart-tablet {
    display: block;
  }
  .cart-icon {
    color: rgba(255, 255, 255, 0.65)
  }

  .cart-icon:hover {
    color: #fff;
  }
  span.ant-badge {
    display: block;
    position: absolute;
    right: 16px;
    font-size: 26px;
    color: white;
    top: 18px;
    cursor: pointer;
  }

}
</style>

