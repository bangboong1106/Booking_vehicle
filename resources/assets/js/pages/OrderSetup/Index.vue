<template>
  <div class="order-setup container">
    <Steps :current="2" />
    <a-row>
      <a-col :xs="{ span: 24 }" :lg="{ span: 24 }" :md="{ span: 24 }">
        <div>
          <form-detail
            @onCountSelectedItem="totalSelectedItem"
            :key="key"
            @calcdistance="calcdistance"
          ></form-detail>
        </div>
      </a-col>
      <a-col :xs="{ span: 24 }" :lg="{ span: 1 }" :md="{ span: 1 }"></a-col>
      <!-- <a-col :xs="{ span: 24 }" :lg="{ span: 8 }" :md="{ span: 8 }">
        <div>
          <ship-list></ship-list>
        </div>
      </a-col> -->
      <a-col
        :xs="{ span: 24 }"
        :lg="{ span: 24 }"
        :md="{ span: 24 }"
        class="order-setup-footer"
      >
        <div class="order-setup-footer">
          <div class="order-setup-footer__row1"></div>
          <div class="order-setup-footer__row2">
            <div class="order-setup-footer__checkbox-wrapper">
              <a-checkbox @change="selectAllItems"
                >Chọn tất cả ({{ totalGoods }})</a-checkbox
              >
              <div class="clear-btn">
                <span @click="showConfirm">Xóa</span>
                <a-modal></a-modal>
              </div>
            </div>

            <div class="order-setup-footer-summary">
              <div class="order-setup-footer-summary__subtotal-text">
                <span
                  >Tổng tiền hàng ({{ selectedItems.length }} sản phẩm) :
                </span>
              </div>
              <div class="order-setup-footer-summary__amount">
                <span> 69.969.696 ₫</span>
              </div>
            </div>
            <div class="order-setup-footer__checkout">
              <a-button
                class="primary-background-color"
                type="primary"
                @click="checkOut"
                style="width: 210px"
                >Đặt hàng
              </a-button>
            </div>
          </div>
        </div>
      </a-col>
    </a-row>
  </div>
</template>
<script>
import Form from "@/pages/OrderSetup/Form";
// import List from "@/components/Shipping/List";
import Steps from "@/components/Steps/Steps";
import EventBus from "@/event-bus";
import moment from "moment";
import axios from "axios";

export default {
  data() {
    return {
      totalGoods: 0,
      selectedItems: [],
      key: 0,
      checked: false,
      formData: [],
      detailData: [],
      distance: null,
    };
  },

  components: {
    "form-detail": Form,
    Steps,
  },
  methods: {
    moment,
    selectAllItems(e) {
      let listItemsChecked = [];
      let listItemsInCart = JSON.parse(localStorage.getItem("cartDetail"));
      if (listItemsInCart) {
        listItemsInCart.forEach((item, index) => {
          listItemsChecked.push(item.id);
        });
        if (!this.checked) {
          this.checked = e.target.checked;
          EventBus.$emit("onCheckedItems", listItemsChecked);
        } else {
          this.checked = e.target.checked;
          EventBus.$emit("onCheckedItems", []);
        }
      }
    },
    totalSelectedItem(value) {
      this.selectedItems = value;
    },
    removeItems() {
      if (!localStorage.getItem("cartDetail")) {
        localStorage.setItem("cartDetail", JSON.stringify([]));
      } else {
        let listItemsInCart = [];
        let selectedItems = this.selectedItems;
        let listItemOrder = JSON.parse(localStorage.getItem("cartDetail"));
        listItemOrder.forEach((item, index) => {
          if (
            selectedItems.filter((selectedItem, ind) => {
              return selectedItem == item.id;
            }).length == 0
          ) {
            listItemsInCart.push(item);
          }
        });
        this.selectedItems = [];
        localStorage.setItem("cartDetail", JSON.stringify(listItemsInCart));
        this.totalGoods = JSON.parse(localStorage.getItem("cartDetail")).length;
      }
    },
    showConfirm() {
      if (!this.selectedItems) {
        this.$message.warning("Vui lòng chọn sản phẩm");
      } else if (this.selectedItems.length === 0) {
        this.$message.warning("Vui lòng chọn sản phẩm");
      } else {
        let component = this;
        this.$confirm({
          title: `Bạn có muốn bỏ ${component.selectedItems.length} sản phẩm không ?`,
          okText: "Xác nhận",
          cancelText: "Huỷ",
          onOk() {
            component.removeItems();
          },
          onCancel() {},
        });
      }
    },
    checkOut() {
      this.formData = JSON.parse(localStorage.getItem("cart"));
      let locationDestination = JSON.parse(
        JSON.stringify(this.formData.locationDestination)
      );
      let locationArrival = JSON.parse(
        JSON.stringify(this.formData.locationArrival)
      );
      this.detailData = JSON.parse(localStorage.getItem("cartDetail"));
      
      let data = {
        ETD_date: moment(this.formData.startTime).format("DD-MM-YYYY"),
        location_destination_id: locationDestination.key,
        location_arrival_id: locationArrival.key,
      };
      axios
        .post("c-order-customer/save", data)
        .then((response) => {
          if (response.data.errorCode == 0) {
            this.$message.success(`Lưu đơn hàng thành công`);
            console.log("thành công");
          } else {
            console.log("thất bại");
          }
        })
        .catch((error) => {
          this.$message.error(error.message);
          this.$emit("onError");
        });
    },
    calcdistance(value){
      this.distance=value
    console.log(this.distance)

    }
  },
  computed:{
    
  },
  mounted() {
    this.totalSelectedItem();
    this.calcdistance();
  },
  created() {
    this.totalGoods = JSON.parse(localStorage.getItem("cartDetail")).length;
    //  EventBus.$on("calcdistance", this.calcdistance())
            
  },
  destroyed() {
   
  },
};
</script>
<style scoped>
.contaier {
  max-width: 1400px;
  margin: 20px auto;
}

.order-setup {
  margin-top: 100px;
  background: #fff;
  position: relative;
  padding: 10px 20px;
}

.order-setup-footer {
  position: sticky;
  bottom: 0;
  left: 0;
  right: 0;
  background: #fff;
  height: 100px;
  padding: 12px 0;
}

.order-setup-footer:before {
  content: "";
  position: absolute;
  top: -1.25rem;
  left: 0;
  height: 1.25rem;
  width: 100%;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.06));
}

.order-setup-footer__row2 {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 100%;
}

.order-setup-footer__checkbox-wrapper {
  padding: 0 20px;
  display: flex;
}

.clear-btn {
  cursor: pointer;
  margin: 0 6px;
}

.order-setup-footer-summary__amount > span {
  color: rgb(0, 177, 79);
  font-size: 24px;
}
.clear-btn > span:hover {
  color: rgb(0, 177, 79);
}
.order-setup-footer__checkout {
  padding: 0 22px;
}

@media (max-width: 576px) {
  .order-setup.container {
    margin: 130px 15px 0 15px;
  }
}
</style>
