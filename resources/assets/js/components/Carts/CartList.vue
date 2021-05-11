<template>
  <div :key="key" class="list-car" v-if="this.cartList.length !=0 ">
    <a-list item-layout="vertical" size="large" :pagination="false" :data-source="cartList">
      <div slot="header" class="list-car--header">
        <span>Thông tin giỏ hàng</span>
      </div>
        <a-list-item slot="renderItem" slot-scope="item, index" :key="index">
        <img
            slot="extra"
            width="272"
            class="vehicle-image"
            alt="logo"
            :src="item.path"
        />
        <a-list-item-meta>
          <span class="title-vehicle" slot="title">{{ item.title != null ? item.title : null }}</span>
        </a-list-item-meta>
        <div class="choose-info">
          <a-form class="form-info">
            <a-form-item label="Tình trạng xe">
                <span class="primary-color status-text">{{ item.vehicleStatus != null ? item.vehicleStatus : item.vehicleStatus = "Xe mới" }}</span>
            </a-form-item>
            <a-form-item label="Số lượng">
<!--              <div class="cartItem-quantity">-->
<!--                <div class="cartInput-quatity">-->
<!--                  <a-icon type="minus" />-->
<!--                  <input class="input-quatity" type="number" :value="item.quantity"> </input>-->
<!--                  <a-icon type="plus" />-->
<!--                </div>-->
<!--              </div>-->
              <a-input-number :min="1" :step="1" :value="item.quantity" @change="onchangeQuantity($event,item,index)" ></a-input-number>

            </a-form-item>
          </a-form>
        </div>
        <div class="group-button">
          <button
              class="btn-base btn-delete"
              style="width: 80px"
              @click="deleteItem(index)"
          >
            <a-icon type="delete"/>
            Xoá
          </button>
        </div>
      </a-list-item>
      <div slot="footer" class="splice" style="text-align: center">
          <button @click="order" class="btn-base btn-order">Đặt Xe</button>
      </div>
    </a-list>
  </div>
</template>
<script>


import EventBus from "@/event-bus";

export default {
  data() {
    return {
      cartList: [],
      pagination: {
        onChange: page => {
          console.log(page);
        },
        pageSize: 5,
      },
      key:0

    };
  },

  methods: {
    getDatalist() {
      if (!localStorage.getItem("cartDetail")) {
        localStorage.setItem("cartDetail", JSON.stringify([]));
      }
      this.cartList = JSON.parse(localStorage.getItem("cartDetail"));
    },

    deleteItem(index) {

      if (!localStorage.getItem("cartDetail")) {
        localStorage.setItem("cartDetail", JSON.stringify([]));
      }

      this.cartList = JSON.parse(localStorage.getItem("cartDetail"));
      this.cartList.splice(index, 1)
      localStorage.setItem('cartDetail', JSON.stringify(this.cartList));
      this.$emit('getQuantityItemsInCart')
    },

    reload() {
      this.cartList = JSON.parse(localStorage.getItem("cartDetail"));
    },
    order(){
      this.$router.push('order-setup');
      EventBus.$emit('orderNow')
    },
    onchangeQuantity(value,item,index){
      item.quantity=value
      this.cartList[index].quantity=value
      localStorage.setItem('cartDetail', JSON.stringify(this.cartList));

    }
  },
  // watch:{
  //     cartList:{
  //       handler(){
  //         localStorage.setItem('cartDetail', JSON.stringify(this.cartList));
  //       }
  //     }
  // },
  beforeMount() {
    this.getDatalist();
  },
  created() {
    EventBus.$on('reload', this.reload)
  }, destroyed() {
    EventBus.$off('reload', this.reload)
  }

};
</script>
<style scoped>
.ant-list-item-meta-title{
  margin-top: 16px;
}
.ant-list-item-meta-title > .title-vehicle {
  font-size: 16px;
  font-weight: 600;
  text-transform: uppercase;
}
.ant-list-item {
  flex-direction: column-reverse;
}

.ant-list-vertical .ant-list-item-meta {
  margin: 0;
}
.splice{
  padding-top:20px;
  border-top: 1px solid;
}
.ant-row.ant-form-item {
  display: inline-flex;
}

.ant-row.ant-form-item {
  margin: 0;
}
.vehicle-image{
  width: 100%;
  cursor: pointer;
  object-fit: cover
}
.group-button {
  margin-top: 12px;
  display: flex;
  justify-content: center;
}
.form-info {
  display: flex;
  flex-direction: column;

}
.status-text{
  text-transform: uppercase;
}
.ant-row.ant-form-item {
  display: flex;
  align-items: center;
}
.ant-row.ant-form-item > label{
  margin-bottom: -8px;
}
.cartItem-quantity{
  display: flex;
  justify-content: center;
  align-items: center;

}
.input-quatity{
  width: 50px;
  height: 32px;
  border-left: none;
  border-right: none;
  font-size: 16px;
  font-weight: 400;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  text-align: center;
  cursor: text;
  border-radius: 0;
  -webkit-appearance: none;
}
@media (max-width: 576px) {
  .list-car--header{
    display: none;
  }
  .ant-list-item-meta-title > .title-vehicle {
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
  }
  .vehicle-image{
    width: 100%;
    margin: 0;

  }
  .group-button {
    margin-top: 24px;
    display: flex;
    justify-content: flex-end;
  }
  .list-car{
    margin-top : 35px;
  }
  .ant-list-header, .ant-list-footer{
    border-top:2px solid!important;
  }

}
@media (min-width: 577px) and (max-width: 890px) {
  .group-button{
    justify-content:center;
  }
  .btn-base{
    width: 50%;
  }
}

</style>