<template>
  <div class="form-order">
    <div class="order-info">
      <div class="order-info-wrapper">
        <a-row :gutter="16">
          <a-col :xs="{ span: 24 }" :sm="{ span: 12 }">
            <a-descriptions title="Thông tin chung" :column="2">
              <a-descriptions-item label="Địa điểm nhận">
                {{ formData.locationDestination.label }}
              </a-descriptions-item>
              <!-- <span> <a-icon type="edit" @click="editLocation" /></span> -->
              <a-descriptions-item label="Địa điểm trả">
                {{ formData.locationArrival.label }}</a-input>
              </a-descriptions-item>
              <!-- <span> <a-icon type="edit" @click="editLocation2" /></span> -->
               <a-descriptions-item label="Tổng quãng đường">
                {{ distance.distance }} km
              </a-descriptions-item>
              <a-descriptions-item label="Ngày nhận xe">
                {{ formData.startTime.value.split("-").reverse().join("-") }}
              </a-descriptions-item>
              <a-descriptions-item label="Loại hình vận chuyển">
                {{ typeShip }}
              </a-descriptions-item>
              <a-descriptions-item label="Tổng tiền">
                69.960.000 ₫
              </a-descriptions-item>
            </a-descriptions>
          </a-col>
          <!-- form điền thông tin -->
          <a-col :xs="{ span: 24 }" :sm="{ span: 12 }">
                          <a-form :form="form" @submit="handleSubmit">
            <a-descriptions title="Thông tin người gửi"/>
                <a-form-item v-bind="formItemLayout" label="E-mail">
                  <a-input
                    v-decorator="[
                      'emailsender',
                      {
                        rules: [
                          {
                            type: 'email',
                            message: 'The input is not valid E-mail!',
                          },
                          {
                            required: true,
                            message: 'Please input your E-mail!',
                          },
                        ],
                      },
                    ]"
                  />
                </a-form-item>
                <a-form-item v-bind="formItemLayout">
                  <span slot="label"> Họ Tên&nbsp; </span>
                  <a-input
                    v-decorator="[
                      'fullnamesender',
                      {
                        rules: [
                          {
                            required: true,
                            message: 'Please input your nickname!',
                            whitespace: true,
                          },
                        ],
                      },
                    ]"
                  />
                </a-form-item>
                <a-form-item v-bind="formItemLayout" label="Số điện thoại">
                  <a-input
                    v-decorator="[
                      'phonesender',
                      {
                        rules: [
                          {
                            required: true,
                            message: 'Please input your phone number!',
                          },
                        ],
                      },
                    ]"
                    style="width: 100%"
                  >
                    <a-select
                      slot="addonBefore"
                      v-decorator="['prefix', { initialValue: '86' }]"
                      style="width: 70px"
                      :value="+84"
                    >
                    </a-select>
                  </a-input>
                </a-form-item>
                  <a-descriptions title="Thông tin người nhận"/> 
                      <a-form-item v-bind="formItemLayout" label="E-mail">
                        <a-input
                          v-decorator="[
                            'emailreceiver',
                            {
                              rules: [
                                {
                                  type: 'email',
                                  message: 'The input is not valid E-mail!',
                                },
                                {
                                  required: true,
                                  message: 'Please input your E-mail!',
                                },
                              ],
                            },
                          ]"
                        />
                      </a-form-item>
                      <a-form-item v-bind="formItemLayout">
                        <span slot="label"> Họ Tên&nbsp; </span>
                        <a-input
                          v-decorator="[
                            'fullnamereceiver',
                            {
                              rules: [
                                {
                                  required: true,
                                  message: 'Please input your nickname!',
                                  whitespace: true,
                                },
                              ],
                            },
                          ]"
                        />
                      </a-form-item>
                      <a-form-item
                        v-bind="formItemLayout"
                        label="Số điện thoại"
                      >
                        <a-input
                          v-decorator="[
                            'phonereceiver',
                            {
                              rules: [
                                {
                                  required: true,
                                  message: 'Please input your phone number!',
                                },
                              ],
                            },
                          ]"
                          style="width: 100%"
                        >
                          <a-select
                            slot="addonBefore"
                            v-decorator="['prefix', { initialValue: '84' }]"
                            style="width: 70px"
                            :value="+84"
                          >
                          </a-select>
                        </a-input>
                      </a-form-item>
              </a-form>                
          </a-col>
        </a-row>
      </div>
    </div>
    <div class="good-info">
      <div class="ant-descriptions-title">Thông tin hàng hóa</div>
      <!--      <a-empty v-if="orderDetail && orderDetail.length == 0">-->
      <!--        <span slot="description">Không có dữ liệu</span>-->
      <!--      </a-empty>-->
      <div class="grid-item-pc">
        <a-table
          :columns="columns"
          :data-source="orderDetail"
          :pagination="false"
          :row-selection="{
            selectedRowKeys: selectedRowKeys,
            onChange: onSelectChange,
          }"
        >
          <span slot="avatar" slot-scope="text">
            <img
              v-if="text"
              :src="text"
              alt="file"
              :style="{ width: '30px', height: '30px' }"
              :row-selection="{
                selectedRowKeys: selectedRowKeys,
                onChange: onSelectChange,
              }"
          /></span>
          <span slot="action" slot-scope="text" @click="deleteOrder">
            <a-icon
              type="delete"
              :style="{ color: 'rgb(0, 177, 79)', cursor: 'pointer' }"
            />
          </span>
        </a-table>
      </div>
    </div>
    <!-- <div class="grid-item-mobile">
      <div class="list-item">
        <div class="item" v-for="(item, index) in orderDetail" :key="index">
          <div class="item-avatar">
            <a-checkbox
              :style="{ marginRight: '8px' }"
              @change="onChangeCheckedMobile(index)"
            >
            </a-checkbox>
            <img
              class="avatar"
              :src="item.path"
              alt="avatar"
              :style="{ width: '100px', height: '71px' }"
            />
          </div>
          <div class="item-info">
            <div class="item-info-name">
              <span>{{ item.title }}</span>
            </div>
            <div class="item-info-status primary-color">
              <span>{{ item.vehicleStatus }}</span>
            </div>
            <div class="item-info-price">
              <span>{{ item.amount }} đ</span>
              <span>x {{ item.quantity }}</span>
            </div>
          </div>
        </div>
      </div>
    </div> -->
  </div>
</template>

<script>
import EventBus from "@/event-bus";
import moment from "moment";
import ColumnConfig from "@/common/ColumnConfig";
import axios from 'axios';

export default {
  beforeCreate() {
    this.form = this.$form.createForm(this, { name: "register" });
  },
  component: {},
  props: {
    typeShip: String,
  },
  data() {
    const columns = ColumnConfig["OrderDetail"];
    return {
      dateFormat: "YYYY/MM/DD",
      orderDetail: [],
      formData: {},
      distance:[],
      columns,
      selectedRowKeys: [],
      formLayout: "horizontal",
      isCheck: false,
      editing: false,
      editing2: false,
      location: "",
      location2: "",
      beforeEditCahe: "",
      beforeEditCahe2: "",
      formItemLayout: {
        labelCol: {
          xs: {
            span: 24,
          },
          sm: {
            span: 8,
          },
        },
        wrapperCol: {
          xs: {
            span: 24,
          },
          sm: {
            span: 16,
          },
        },
      },
    };
  },
  components: {},
  methods: {
    moment,
    getFormData() {
      this.formData = JSON.parse(localStorage.getItem("cart"));
      let locationDestinationID=this.formData.locationDestination.key
      let locationArrivalID=this.formData.locationArrival.key
      axios.post('c-order/cal-distance',
              {
                location_destination_id: locationDestinationID,
                location_arrival_id: locationArrivalID,
              }).then((response) => {
            if (response.data.errorCode != 0) {
              this.$message.error(response.data.errorMessage.map((p) => p.errorMessage).join("\n"))
            }
            else{
              this.distance=response.data.data
            }
            }) .catch((error) => {
            
            this.$message.error(error.message);
          });
    },
    getDatalist() {
      if (!localStorage.getItem("cartDetail")) {
        localStorage.setItem("cartDetail", JSON.stringify([]));
      }
      this.orderDetail = JSON.parse(localStorage.getItem("cartDetail"));
      this.orderDetail.map((p) => {
        p.totalPrice = p.amount * p.quantity;
      });
      this.orderDetail.forEach((d) => {
        return (d.amount = d.amount * 0.02);
      });
    },
    onSelectChange(selectedRowKeys) {
      this.selectedRowKeys = selectedRowKeys;
      this.$emit("onCountSelectedItem", this.selectedRowKeys);
    },
    onCheckAll(data) {
      this.selectedRowKeys = data;
      this.onSelectChange(data);
    },
    deleteOrder(index) {
      if (!localStorage.getItem("cartDetail")) {
        localStorage.setItem("cartDetail", JSON.stringify([]));
      }

      this.orderDetail = JSON.parse(localStorage.getItem("cartDetail"));
      this.orderDetail.splice(index, 1);
      localStorage.setItem("cartDetail", JSON.stringify(this.orderDetail));
      this.$emit("getQuantityItemsInCart");
    },
    onSelectChangeMobile(value) {
      console.log(value);
    },

    reload() {
      this.cartList = JSON.parse(localStorage.getItem("cartDetail"));
    },
    handleSubmit(e) {
      e.preventDefault();
      this.form.validateFieldsAndScroll((err, values) => {
        if (!err) {
          console.log("Received values of form: ", values);
          EventBus.$emit("submitForm", this.values);
        }
      });
    },
    onChange() {
      this.isCheck = !this.isCheck;
    },
  },
  beforeCreate() {
    this.form = this.$form.createForm(this, {
      name: "register",
    });
  },
  beforeMount() {
    this.getDatalist();
    this.getFormData();
    EventBus.$on("getTitleTypeShip", (data) => {
      this.typeShip = data;
    });
  },
  created() {
    EventBus.$on("reload", this.reload);
    EventBus.$on("onCheckedItems", this.onCheckAll);
  },
  destroyed() {
    EventBus.$off("reload", this.reload);
    EventBus.$off("onCheckedItems", this.onCheckAll);
  },
};
</script>

<style lang="scss" scoped>
.ant-descriptions-title {
  color: rgb(0, 177, 79) !important;
}

.form-order {
  padding: 16px 0 0 16px;
}

.header {
  display: flex;
  font-weight: bold;
  margin: -8px -12px;
}

.header > .goods.avatar {
  width: 60px;
}

.goods {
  &.title {
    width: 250px;
  }

  &.quantity {
    width: 100px;
  }

  &.price {
    width: 100px;
  }

  &.price-total {
    width: 100px;
  }
}

.ant-list-item {
  padding: 8px 12px !important;
  display: flex;
  flex-direction: revert;
}

.header-title > b {
  font-size: 16px;
}

.ant-row.ant-form-item {
  display: flex;
  margin: 0;
}

.grid-item-pc {
  display: block;
}

.grid-item-mobile {
  display: none;
}

.item {
  display: flex;
  justify-content: space-between;
  padding: 20px 0;
}

.avatar {
  border: 1px solid #ddd;
}

.item-info {
  display: inline-block;
  width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.item-info-name > span {
  text-transform: uppercase;
  font-weight: 600;
}

.item-info-status {
  padding: 4px 0;
}

.item-info-price > span:nth-child(1) {
  color: red;
}

.item-info-price {
  display: flex;
  justify-content: space-between;
}

.form {
  padding: 16px 0;
}

@media (max-width: 576px) {
  .grid-item-pc {
    display: none;
  }

  .grid-item-mobile {
    display: block;
  }

  .ant-list-vertical .ant-list-item {
    flex-direction: row-reverse;
  }

  .ant-row.ant-form-item {
    display: block;
  }
}
</style>
