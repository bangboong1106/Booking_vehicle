<template>
  <div class="book-car container">
    <a-row :gutter="[16,16]" class="row">
      <a-col :xs="{ span: 24 }" :lg="{ span: 6 }" :md="{ span: 24 }">
        <order-info></order-info>
      </a-col>
      <a-col :xs="{ span: 24 }" :lg="{ span: 12 }" :md="{ span: 24 }">
        <div class="list-car">
          <a-input-search placeholder="Tìm kiếm" enter-button @search="onSearch" />
          <a-tabs default-active-key="1">
            <a-tab-pane key="1" tab="Ô tô">
              <OrderItem :vehiclesType="typeOfCar"/>
            </a-tab-pane>
            <a-tab-pane key="2" tab="Xe máy" force-render>
              <OrderItem :vehiclesType="typeOfMoto"/>
            </a-tab-pane>
          </a-tabs>
        </div>
      </a-col>
      <a-col :xs="{ span: 24 }" :lg="{ span: 6 }" :md="{ span: 24 }" class="cart-pc">
        <cart-list></cart-list>
      </a-col>
    </a-row>
  </div>

</template>
<script>
import OrderItem from "@/components/Vehicle/VehicleList";
import OrderInfo from "@/components/Form/OrderInfor";
import CartList from "@/components/Carts/CartList";
import constant from "@/constant";
import EventBus from "@/event-bus";

export default {
  data() {
    const typeOfCar = constant.vehicleType.CAR;
    const typeOfMoto = constant.vehicleType.MOTORBIKE;
    return {
      typeOfCar,
      typeOfMoto,
      current: 1,
      textFilter:"",
      steps: [
        {
          title: 'Bước 1',
          content: 'Điền thông tin',
        },
        {
          title: 'Bước 2',
          content: 'Chọn các loại xe',
        },
        {
          title: 'Bước 3',
          content: 'Đặt xe',
        },
      ],
    };

  },

  components: {
    OrderInfo,
    OrderItem,
    'cart-list': CartList,
  },
  methods: {
    next() {
      this.current++;
    },
    prev() {
      this.current--;
      if (this.current === 0) {
        this.$router.push('home')
      }
      ;
    },
    onSearch(value){
      EventBus.$emit('getTextFilter',value)

    }
  },
  computed: {},
};
</script>
<style scoped>
.container {
  max-width: 1200px;
  margin: 20px auto;
}

.cart-pc {
  display: block;
}

.book-car {
  margin-top: 150px;
}

.list-car {
  padding: 30px 15px;
  box-shadow: 0 1px 5px rgb(0 0 0 / 30%);
  margin-bottom: 30px;
  border: 1px solid #c0b3b3;
  background: #fff;
}

@media (max-width: 576px) {
  .row {
    padding: 0 16px !important;
  }

  .cart-pc {
    display: none;
  }

}
@media (min-width: 577px) and (max-width: 890px) {
  .cart-pc{
    display: none;
  }
  .row {
    padding: 0 16px !important;
  }
}
</style>
