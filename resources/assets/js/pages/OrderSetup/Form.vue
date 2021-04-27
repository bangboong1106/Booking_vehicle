<template>
  <div class="form-order">
    <div class="order-info">
      <div class="order-info-wrapper">
        <a-row :gutter="16" >
          <a-col  :xs="{ span: 24 }" :sm="{ span: 12 }">
            <a-descriptions
                title="Thông tin chung"
                :column="2"
            >
              <a-descriptions-item label="Địa điểm nhận">
                <div v-if="!editing" >
                  {{ formData.locationDestination.label }}
                </div>
                <a-input v-else="" v-model="location" @blur="doneEdit"  @keyup.enter="doneEdit" @keyup.esc="cancelEdit"></a-input>
              </a-descriptions-item>
              <span><a-icon type="edit"@click="editLocation" /></span>
              <a-descriptions-item label="Địa điểm trả">
                <div v-if="!editing2" >
                  {{ formData.locationArrival.label }}
                </div>
                <a-input v-else="" v-model="location2" @blur="doneEdit"  @keyup.enter="doneEdit" @keyup.esc="cancelEdit"></a-input>
              </a-descriptions-item>
              <span><a-icon type="edit"@click="editLocation2" /></span>
              <a-descriptions-item label="Ngày trả xe">
                {{
                  formData.startTime.value.split("-").reverse().join("-")
                }}
              </a-descriptions-item>

              <a-descriptions-item label="Tổng tiền">
                69.960.000 ₫
              </a-descriptions-item>
              <a-descriptions-item label="Loại hình vận chuyển">
                {{typeShip}}
              </a-descriptions-item>

            </a-descriptions>
          </a-col>

          <a-col  :xs="{ span: 24 }" :sm="{ span: 12 }">
            <a-descriptions title="Thông tin người gửi">
              <a-form :form="form" @submit="handleSubmit">
                <a-form-item v-bind="formItemLayout" label="E-mail">
                  <a-input
                      v-decorator="[
          'email',
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
      <span slot="label">
        Họ Tên&nbsp;
      </span>
                  <a-input
                      v-decorator="[
          'nickname',
          {
            rules: [{ required: true, message: 'Please input your nickname!', whitespace: true }],
          },
        ]"
                  />
                </a-form-item>
                <a-form-item v-bind="formItemLayout" label="Số điện thoại">
                  <a-input
                      v-decorator="[
          'phone',
          {
            rules: [{ required: true, message: 'Please input your phone number!' }],
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
              </a-form>
            </a-descriptions>
            <a-checkbox-group @change="onChange" :default-value="['A']">
              <a-row>
                <a-col :span="24">
                  <a-checkbox value="A">
                    Sử dụng thông tin này làm thông tin nhận hàng
                  </a-checkbox>
                </a-col>
                <a-col :span="24">
                  <a-checkbox value="true"  >
                    Sử dụng thông tin này làm thông tin nhận hàng
                  </a-checkbox>
                  <a-descriptions v-if="isCheck" title="Thông tin người nhận">
                    <a-form :form="form" @submit="handleSubmit">
                      <a-form-item v-bind="formItemLayout" label="E-mail">
                        <a-input
                            v-decorator="[
          'email',
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
      <span slot="label">
        Họ Tên&nbsp;
      </span>
                        <a-input
                            v-decorator="[
          'nickname',
          {
            rules: [{ required: true, message: 'Please input your nickname!', whitespace: true }],
          },
        ]"
                        />
                      </a-form-item>
                      <a-form-item v-bind="formItemLayout" label="Số điện thoại">
                        <a-input
                            v-decorator="[
          'phone',
          {
            rules: [{ required: true, message: 'Please input your phone number!' }],
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
                    </a-form>
                  </a-descriptions>
                </a-col>

              </a-row>
            </a-checkbox-group>
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
        <a-table :columns="columns" :data-source="orderDetail" :pagination='false'
                 :row-selection="{ selectedRowKeys: selectedRowKeys, onChange: onSelectChange }">
                <span slot="avatar" slot-scope="text">
                    <img
                        v-if="text"
                        :src="text"
                        alt="file"
                        :style="{ width: '30px', height: '30px' }"
                        :row-selection="{ selectedRowKeys: selectedRowKeys, onChange: onSelectChange }"
                    /></span>
          <span slot="action" slot-scope="text" @click="deleteOrder">
                  <a-icon type="delete" :style="{color:'rgb(0, 177, 79)',cursor:'pointer'}"/>
                </span>
        </a-table>
      </div>

    </div>
    <div class="grid-item-mobile">
      <div class="list-item">
        <div class="item" v-for="(item,index) in orderDetail" :key="index">
          <div class="item-avatar">
            <a-checkbox :style="{marginRight : '8px'}" @change="onChangeCheckedMobile(index)  ">
            </a-checkbox>
            <img
                class="avatar"
                :src="item.path"
                alt="avatar"
                :style="{ width: '100px',height:'71px'}"
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
    </div>
  </div>
</template>
<script>
import EventBus from "@/event-bus";
import moment from 'moment';
import ColumnConfig from "@/common/ColumnConfig";

export default ({
  component: {},
  props: {
    typeShip:String
  },
  data() {
    const columns = ColumnConfig['OrderDetail'];
    return {
      typeShip:"...",
      dateFormat: 'YYYY/MM/DD',
      orderDetail: [],
      formData: {},
      columns,
      selectedRowKeys: [],
      formLayout: 'horizontal',
      isCheck:false,
      editing:false,
      editing2:false,
      location:"",
      location2:"",
      beforeEditCahe:"",
      beforeEditCahe2:"",
      formItemLayout: {
        labelCol: {
          xs: { span: 24 },
          sm: { span: 8 },
        },
        wrapperCol: {
          xs: { span: 24 },
          sm: { span: 16 },
        },
      },
    }
  },
  components: {},
  methods: {
    moment,
    getFormData() {
      this.formData = JSON.parse(localStorage.getItem('cart'));
    },
    getDatalist() {
      if (!localStorage.getItem('cartDetail')) {
        localStorage.setItem('cartDetail', JSON.stringify([]))
      }
      this.orderDetail = JSON.parse(localStorage.getItem('cartDetail'));
    },
    onSelectChange(selectedRowKeys) {
      this.selectedRowKeys = selectedRowKeys;
      this.$emit('onCountSelectedItem', this.selectedRowKeys);
    },
    onCheckAll(data) {
      this.selectedRowKeys = data;
      this.onSelectChange(data)
    },
    deleteOrder(index) {
      if (!localStorage.getItem("cartDetail")) {
        localStorage.setItem("cartDetail", JSON.stringify([]));
      }

      this.orderDetail = JSON.parse(localStorage.getItem("cartDetail"));
      this.orderDetail.splice(index, 1)
      localStorage.setItem('cartDetail', JSON.stringify(this.orderDetail));
      this.$emit('getQuantityItemsInCart')
    },
    onSelectChangeMobile(value) {
      console.log(value)
    },

    reload() {
      this.cartList = JSON.parse(localStorage.getItem("cartDetail"));
    },
    editLocation(){
      this.location=this.formData.locationDestination.label
      this.editing = true
      this.editing2=false
    },
    editLocation2(){
      this.location2=this.formData.locationArrival.label
      this.editing2 = true
      this.editing=false
    },
    doneEdit(){
      this.beforeEditCahe=this.formData.locationDestination.label
      this.beforeEditCahe2=this.formData.locationArrival.label
      if(this.location.trim()== "" ){
            this.location=this.beforeEditCahe
      }
      if(this.location2.trim()== "" ){
        this.location2=this.beforeEditCahe2
      }
      this.editing=false
      this.editing2=false
      this.formData.locationDestination.label=this.location
      this.formData.locationArrival.label=this.location2
      localStorage.setItem('cart',JSON.stringify(this.formData))
    },

    cancelEdit() {
      this.formData.locationDestination.label= this.beforEditCache
      this.editing = false

    },
    handleSubmit(e) {
      e.preventDefault();
      this.form.validateFieldsAndScroll((err, values) => {
        if (!err) {
          console.log('Received values of form: ', values);
        }
      });
    },
    onChange() {
        this.isCheck = !this.isCheck;
    },
  },
  beforeCreate() {
    this.form = this.$form.createForm(this, { name: 'register' });
  },
  beforeMount() {
    this.getDatalist();
    this.getFormData()
    EventBus.$on('getTitleTypeShip', (data)=>{
      this.typeShip=data
    });
  },
  created() {
    EventBus.$on('reload', this.reload);
    EventBus.$on('onCheckedItems', this.onCheckAll);

  }, destroyed() {
    EventBus.$off('reload', this.reload);
    EventBus.$off('onCheckedItems', this.onCheckAll)

  }
})
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