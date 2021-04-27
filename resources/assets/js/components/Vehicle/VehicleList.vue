<template>
  <a-list item-layout="vertical"
          size="large" :pagination="pagination"
          :data-source="listVehicles"
  >
    <div slot="header">
      <div class="menu">
        <div class="menu-item" v-for="(i,index) in listBrand" :key="index">
          <div class="brand-item" @click="fetchData(i.id)">
            <img :src="require('../../../../../public/media/goods/brands/'+ i.name +'.png')" alt="" :width="48">
            <p class="item-label">
              {{ i.name }}
            </p>
          </div>
        </div>
      </div>
    </div>
    <a-spin v-if="loading"></a-spin>
    <a-list-item slot="renderItem" :key="index"  slot-scope="item, index" style="display:flex;align-items: center">
      <img
          slot="extra"
          class="img-vehiclelist"
          alt="logo"
          :src="item.path"
          :not-found-content="fetching ? undefined : null"

      />
      <a-spin v-if="fetching" slot="notFoundContent" size="small"/>
      <a-list-item-meta style="margin: 0">
        <span class="title-vehicle" slot="title">{{ item.title }}</span>
      </a-list-item-meta>
      <div class="choose-info">
        <a-form class="form-info">
          <a-form-item label="Tình trạng xe">
            <div class="group-button">
              <a-button  :class="{active :newIndex === index}"  @click="NewVehicle(item,index)">Xe mới</a-button>
              <a-button  :class="{active :oldIndex === index}"  @click="OldVehicle(item,index)" style="margin-left: 10px">Xe cũ</a-button>
            </div>

          </a-form-item>
          <a-form-item label="Số lượng">

            <a-input-number :min="1" :step="1" @change="onChangeQuantity($event,item)"></a-input-number>
          </a-form-item>

        </a-form>
      </div>
      <div class="group-button">
        <button class="btn-base btn-addcart" @click="addToCart(item,index)">
          <a-icon type="shopping-cart"/>
          Thêm vào giỏ
        </button>
        <button class="btn-base btn-order" style="margin-left: 10px;">Đặt ngay</button>


      </div>
    </a-list-item>

  </a-list>
</template>
<script>
import axios from "axios";
import EventBus from "@/event-bus";
import constant from "@/constant";
export default {
  components: {},
  props: {
    vehiclesType: Number
  },
  data() {

    return {
      newIndex:undefined,
      oldIndex:undefined,
      quantityCart:1,
      key: 0,
      fetching: true,
      cartList: [],
      listVehicles: [],
      loading: true,
      listBrand: [],
      pagination: {
        onChange: page => {
          console.log(page);
        },
        pageSize: 5,
      },
      textFilter:""

    };
  },
  methods: {
    fetchData(data) {
      this.loading = true ;
      if (!data) {
        axios
            .post('c-goods/list', {
              "pageSize": 50,
              "pageIndex": 1,
              "sort": [
                {"sortType": "asc", "sortField": "id"}
              ],
              "filters": [
                {"field": "type", "value": `${this.vehiclesType}`},
                {"field": "title", "value": `${this.textFilter}`},
                
              ]
            })
            .then((response) => {
              if (response.data.errorCode != 0) {
                this.$message.error(response.data.errorMessage.map((p) => p.errorMessage).join("\n"))
              } else {
                this.listVehicles = response.data.data.items;
                this.listVehicles.map((p) => {
                  p.quantity = 1;
                  p.vehicleStatus = "" || "Xe mới";
                })
                this.loading = false;

              }

            })
      }
      else {
        axios
            .post('c-goods/list', {
              "pageSize": 50,
              "pageIndex": 1,
              "sort": [
                {"sortType": "asc", "sortField": "id"}
              ],
              "filters": [
                {"field": "type", "value": `${this.vehiclesType}`},
                {"field": "goods_group_id", "value": `${data}`},
              ]
            })
            .then((response) => {
              if (response.data.errorCode != 0) {
                this.$message.error(response.data.errorMessage.map((p) => p.errorMessage).join("\n"))
              } else {
                this.listVehicles = response.data.data.items;
                this.listVehicles.map((p) => {
                  p.quantity = 1;
                  p.vehicleStatus = "" || "Xe mới";
                })
                this.loading = false;

              }

            })
      }

    },
    fetchDataBrands() {
      axios
          .post('c-goods-group/list', {
            "pageSize": 50,
            "pageIndex": 1,
            "sort": [
              {"sortType": "asc", "sortField": "id"}
            ],
            "filters": [
              {"field": "type", "value": `${this.vehiclesType}`}
            ]
          })
          .then((response) => {
            if (response.data.errorCode != 0) {
              this.$message.error(response.data.errorMessage.map((p) => p.errorMessage).join("\n"))
            } else {
              this.listBrand = response.data.data.items;
            }

          })
    },
    getTextSearch(value){
this.textFilter=value;
this.fetchData();
    },
    onChangeQuantity(value, item) {
      item.quantity = value;
      this.quantityCart=value
    },
    filterBrand(id) {
      this.fetchData();
      var listFilter = [];
      listFilter = this.listVehicles.filter((item) => {
        return item.goods_group_id == id;
      });
      this.listVehicles = listFilter;
    },
    NewVehicle(item,index) {
      item.vehicleStatus = ""
      item.vehicleStatus = "Xe mới"
      item.key=item.id
      this.newIndex=index
      this.oldIndex=undefined
    },
    OldVehicle(item,index) {
      item.vehicleStatus = ""
      item.vehicleStatus = "Xe cũ"
      item.key=item.id + '-old'
      this.newIndex=undefined
      this.oldIndex=index
    },
    addToCart(i,index) {
      const item = this.listVehicles.find(p=>p.id === i.id);
      if (!localStorage.getItem("cartDetail")) {
        localStorage.setItem("cartDetail", JSON.stringify([]));
      }
      this.cartList = JSON.parse(localStorage.getItem('cartDetail'))
      const indexItem=this.cartList.findIndex(p=>p.id=== i.id && p.vehicleStatus===i.vehicleStatus )
      if(indexItem >= 0 ) {
        this.cartList[indexItem].quantity +=item.quantity
        this.cartList[indexItem].vehicleStatus=item.vehicleStatus
       }else if
       (indexItem === -1) {
        this.cartList.push(item)
       }
      else {
        item.quantity +=1;
         this.cartList.push(item)

       }
      localStorage.setItem('cartDetail', JSON.stringify(this.cartList));
      EventBus.$emit('reload', index);
      EventBus.$emit('getQuantityItemsInCart');
    },

  },
  created() {
    this.fetchData();
    this.fetchDataBrands();
    EventBus.$on('getTextFilter',this.getTextSearch);
  }
};
</script>
<style scoped>
.ant-list-vertical .ant-list-item-meta {
  margin: 0;
}

.menu {
  overflow-x: auto;
  background-color: #fff;
  display: grid;
  grid-template-columns: repeat(100, 16.66%);
  list-style: none;
  display: flex;
}
.brand-item{
  cursor: pointer;
}
.menu-item {
  text-align: center;
  padding: 0 23px;
}

.menu-item a {
  display: block;
  width: 100px;
}

.item-label {
  margin-top: 5px;
  color: black;
}

.ant-list-item {
  display: flex;
  flex-direction: row-reverse;
}

.ant-list-item-extra {
  margin-left: 0 !important;
}

.ant-form-item {
  margin-bottom: 8px !important;
}

.ant-row.ant-form-item {
  display: inline-flex;
}

.img-vehiclelist {
  width: 272px;
  cursor: pointer;
  margin-right: 20px;
  object-fit: cover
}

.title-vehicle {
  text-transform: uppercase;
  color: #444;
  font-size: 16px;
  font-weight: 600;
}

.title-vehicle:hover {
  color: #1890FF;
  cursor: pointer;
}

.form-info {
  display: flex;
  flex-direction: column;
}

.group-button {
  display: flex;
  justify-content: flex-end;
}

.group-button {
  display: flex;
  flex-direction: row;
}

.ant-row.ant-form-item {
  display: flex;
  align-items: center;
}

::-webkit-scrollbar {
  height: 10px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #8888889e;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555;
}

@media (max-width: 576px) {
  .img-vehiclelist {
    width: 100%;
    margin: 0;
  }

  .title-vehicle h4 {
    margin: 8px 0;
  }

  .group-button {
    display: flex;
    justify-content: flex-end;
  }
}
</style>