<template>
  <a-form-model
    ref="ruleForm"
    :model="form"
    :rules="rules"
    :label-col="labelCol"
    :wrapper-col="wrapperCol"
    :style="{ top: 0 }"
  >
    <a-tabs default-active-key="1">
      <a-tab-pane key="1" tab="THÔNG TIN">
        <a-form-model-item label="Khách hàng" prop="client">
          <o-select
            :value="form.client"
            :title="'Khách hàng'"
            :textField="'full_name'"
            @input="onChangeClient($event)"
            placeholder="Vui lòng chọn khách hàng"
            :entity="'client'"
            :icon = "'user'"
            :isAddSelect ="true"
          />
        </a-form-model-item>
        <a-form-model-item prop="order_date" label="Ngày đặt hàng">
          <a-date-picker
            v-model="form.order_date"
            :locale="locale"
            type="date"
            format="DD-MM-YYYY"
            :style="{ width: '50%' }"
          />
        </a-form-model-item>
        <a-form-model-item label="Điểm nhận hàng" prop="location_destination">
          <o-select
            :value="form.location_destination"
            @input="onChangeLocationDestination($event)"
            placeholder="Vui lòng chọn điểm nhận hàng"
            entity="location"
            :title="'Điểm nhận hàng'"
            :icon="'environment'"
            :isAddSelect = "true"
          />
        </a-form-model-item>
        <a-form-model-item label="Ngày nhận hàng" prop="ETD_date">
          <a-date-picker
            v-model="form.ETD_date"
            :locale="locale"
            type="date"
            format="DD-MM-YYYY"
            :style="{ width: '50%' }"
            @change="calcETA"
          />
          <a-time-picker
            v-model="form.ETD_time"
            :locale="locale"
            format="HH:mm"
            placeholder="Chọn giờ"
            :style="{ width: '45%', marginLeft: '8px' }"
            @change="calcETA"
          />
        </a-form-model-item>
        <a-form-model-item label="Điểm trả hàng" prop="location_arrival">
          <o-select
            :value="form.location_arrival"
            @input="onChangeLocationArrival($event)"
            placeholder="Vui lòng chọn điểm trả hàng"
            entity="location"
            :title="'Điểm trả hàng'"
            :icon="'environment'"
            :isAddSelect = "true"

          />
        </a-form-model-item>
        <a-form-model-item label="Ngày trả hàng" prop="ETA_date">
          <a-date-picker
            v-model="form.ETA_date"
            :locale="locale"
            type="date"
            format="DD-MM-YYYY"
            :style="{ width: '50%' }"
          />
          <a-time-picker
            v-model="form.ETA_time"
            :locale="locale"
            format="HH:mm"
            placeholder="Chọn giờ"
            :style="{ width: '45%', marginLeft: '8px' }"
          />
        </a-form-model-item>
        <a-form-model-item label="Ngày trả hàng dự kiến" prop="ETA_date_desired">
          <a-date-picker
            v-model="form.ETA_date_desired"
            :locale="locale"
            type="date"
            format="DD-MM-YYYY"
            :style="{ width: '50%' }"
            disabled
          />
          <a-time-picker
            v-model="form.ETA_time_desired"
            :locale="locale"
            format="HH:mm"
            placeholder="Chọn giờ"
            :style="{ width: '45%', marginLeft: '8px' }"
            disabled
          />
        </a-form-model-item>

        <a-form-model-item label="Giá trị hàng hoá" prop="goods_amount">
          <a-input-number
            v-model="form.goods_amount"
            :formatter="
              (value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
            "
            :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"
            :min="0"
            :max="999999999999"
            :style="{ width: '50%' }"
          />
        </a-form-model-item>
        <a-form-model-item label="Ghi chú" prop="note">
          <a-textarea v-model="form.note" placeholder="Ghi chú" :rows="4" />
        </a-form-model-item>
        <a-form-model-item
          label="Lý do"
          prop="reason"
          v-if="
            form.status == constant.CHU_HANG_HUY ||
            form.status == constant.CHU_HANG_YEU_CAU_SUA_DOI
          "
        >
          <a-textarea
            v-model="form.reason"
            placeholder="Lý do"
            :rows="4"
            :disabled="true"
          />
        </a-form-model-item>
        <a-form-model-item style="display:none">
          <a-input v-model="form.distance" />
        </a-form-model-item>
        <a-form-model-item style="display:none">
          <a-input v-model="form.amount_estimate" />
        </a-form-model-item>
      </a-tab-pane>
      <a-tab-pane key="2" tab="HÀNG HOÁ" force-render>
        <a-list
          item-layout="horizontal"
          :data-source="relationForm.list_goods"
          :style="{ border: 'none' }"
        >
          <div slot="header" class="header" style="background-color: #fafafa">
            <div class="header-title header-title-goods">Hàng hóa</div>
            <div class="header-title header-title-unit">Đơn vị</div>
            <div class="header-title header-title-quantity">Số lượng</div>
            <div class="header-title header-title-weight">
              Tổng trọng lượng (kg)
            </div>
            <div class="header-title header-title-volumn">
              Tổng thể lượng (m3)
            </div>
            <div class="header-title header-title-space"></div>
          </div>

          <a-empty v-if="relationForm.list_goods.length === 0">
            <span slot="description">Không có dữ liệu</span>
          </a-empty>
          <a-list-item
            slot="renderItem"
            slot-scope="item, index"
            :ref="`item${index}`"
          >
            <o-select
              :ref="`goodsType${index}`"
              :value="item.goods_type"
              @input="onChangeGoods($event, item, index)"
              placeholder="Vui lòng chọn hàng hoá"
              entity="goods"
              :icon="'gift'"
              :style="{ width: '250px', marginRight: '8px' }"
            />
            <o-select
              :value="item.goods_unit"
              @input="item.goods_unit_id = $event.key"
              placeholder="Vui lòng chọn đơn vị hàng hoá"
              entity="goods-unit"
              :icon = "'deployment-unit'"
              :disabled="true"
              :style="{ width: '150px', marginRight: '8px' }"
            />
            <a-input-number
              v-model="item.quantity"
              @change="onChangeQuantity($event, item, index)"
              :style="{ width: '100px', marginRight: '8px' }"
            />
            <a-input-number
              v-model="item.total_weight"
              :disabled="true"
              :style="{ width: '100px', marginRight: '8px' }"
            />
            <a-input-number
              v-model="item.total_volume"
              :disabled="true"
              :style="{ width: '100px', marginRight: '8px' }"
            />
            <a-icon
              class="dynamic-delete-button"
              type="minus-circle-o"
              @click="removeGoods(item, index)"
              :style="{ width: '60px', marginRight: '8px' }"
            />
          </a-list-item>
        </a-list>
        <a-form-model-item v-bind="formItemLayoutWithOutLabel">
          <a-button type="dashed" style="width: 100%" @click="addGoods">
            <a-icon type="plus" />
            Thêm hàng hoá
          </a-button>
        </a-form-model-item>
      </a-tab-pane>
    </a-tabs>
  </a-form-model>
</template>
<script>
import locale from "ant-design-vue/es/date-picker/locale/vi_VN";
import formUtility from "@/formUtility";
import OneLogSelect from "@/components/Select/OneLogSelect";
import axios from "axios";
import constant from "@/constant";
import moment from "moment/moment";
import EventBus from '@/event-bus';

export default {
  mixins: [formUtility],
  components: {
    "o-select": OneLogSelect,
  },
  props: {
    formData: {},
  },
  created() {
    EventBus.$on('calcAmountEstimate', this.calcAmountEstimate);
  },
  data() {
    const fields = [
      { name: "order_no", required: true },
      { name: "client", required: true },
      { name: "location_destination", required: true },
      { name: "location_arrival", required: true },
      { name: "order_date", required: true },
    ];
    let rules = this.generateRules("OrderCustomer", fields);
    const icons = {
      location :'environment',
      client:'user',
      'location-group':'environment',
      ward : 'environment',
      district : 'environment',
      province : 'environment',
      'location-type':'environment',
      'goods-unit' :'deployment-unit',
      'goods':'gift'
    };
    return {
      constant: constant.orderCustomerStatusConstant,
      display: true,
      formItemLayout: {
        labelCol: {
          xs: { span: 24 },
          sm: { span: 4 },
        },
        wrapperCol: {
          xs: { span: 24 },
          sm: { span: 20 },
        },
      },
      formItemLayoutWithOutLabel: {
        wrapperCol: {
          xs: { span: 24, offset: 0 },
          sm: { span: 24, offset: 0 },
        },
      },
      labelCol: { span: 6 },
      wrapperCol: { span: 16 },
      form: this.formData,
      rules,
      locale,
      icons,
      relationForm: {
        list_goods: this.formData.list_goods || [],
      },
    };
  },
  methods: {
    onChangeGoods($event, item, index) {
      if (
        this.relationForm.list_goods.find((p) => p.goods_type_id == $event.key)
      ) {
        item.goods_type = void 0;
        let goodsType = this.$refs[`goodsType${index}`];
        goodsType.val = void 0;
        this.$message.error(
          "Bạn không được phép chọn hàng hoá đã có trong danh sách"
        );
        return;
      }

      item.goods_type_id = $event.key;

      let data = {};
      
      if (typeof $event.data !== 'undefined') {
        data = $event.data.find((p) => p.id == $event.key)
      } else {
        let orderGoods = this.$refs[`item${index}`];
        data = orderGoods.$children[0].items.find((p) => p.id == $event.key);
      }

      if (!data) return;
      item.quantity = 1;
      item.weight = data.weight;
      item.volume = data.volume;
      item.total_weight = data.weight;
      item.total_volume = data.volume;
      item.goods_unit_id = data.goods_unit_id;
      item.goods_unit = {
        key: data.goods_unit_id,
        label: data.name_of_goods_unit_id,
      };
      this.calcTotalWeight();
    },
    onChangeQuantity($event, item, index) {
      item.total_weight = item.quantity * item.weight;
      item.total_volume = item.quantity * item.volume;
      this.calcTotalWeight();
    },
    addGoods() {
      this.display = false;
      this.relationForm.list_goods.push({
        index: this.relationForm.list_goods.length + 1,
        goods_type: void 0,
        goods_unit: void 0,
        goods_type_id: "",
        goods_unit_id: "",
        quantity: 1,
        weight: 0,
        volume: 0,
        total_weight: 0,
        total_volume: 0,
      });
      this.calcTotalWeight();
    },
    removeGoods(item, idx) {
      let index = this.relationForm.list_goods.indexOf(item);
      if (index !== -1) {
        this.relationForm.list_goods.splice(index, 1);
        this.$refs[`goodsType${idx}`].handleAfterDelete(this.relationForm.list_goods[index]);
      }
      this.calcTotalWeight();
    },
    onChangeClient($event) {
      this.form.client = $event;
      axios
        .get(`c-default-data/default?client_id=${$event.key}`)
        .then((response) => {
          if (response.data.errorCode != 0) {
            this.$message.error(response.data.errorMessage);
            return;
          }
          let data = response.data.data;
          if (data && data.location_destination_id) {
            this.form.location_destination = {
              key: data.location_destination_id,
              label: data.name_of_location_destination_id,
            };
          } else {
            this.form.location_destination = void 0;
          }
          if (data && data.location_arrival_id) {
            this.form.location_arrival = {
              key: data.location_arrival_id,
              label: data.name_of_location_arrival_id,
            };
          } else {
            this.form.location_arrival = void 0;
          }
          this.calcETA();
        })
        .catch((error) => {
          this.$message.error(error.message);
        });
    },
    onChangeLocationDestination($event) {
      this.form.location_destination = $event;
      this.calcETA();
    },
    onChangeLocationArrival($event) {
      this.form.location_arrival = $event;
      this.calcETA();
    },
    calcTotalWeight() {
      this.form.total_weight = this.form.list_goods.reduce((currentTotal, item) => {
        return parseFloat(item.total_weight) + currentTotal;
      }, 0);
    },

    calcETA() {
      if (typeof this.form.location_arrival !== 'undefined'
          && typeof this.form.location_destination !== 'undefined'
          && this.form.ETD_date != ''
          && this.form.ETD_time != ''
      ){

        axios
          .post('c-order-customer/calc-eta', {
            "etd" : moment(String(this.form.ETD_date)).format("DD-MM-YYYY") + " " + moment(String(this.form.ETD_time)).format("HH:mm:ss"),
            "location_destination_id" : this.form.location_destination.key,
            "location_arrival_id" : this.form.location_arrival.key,
          })
          .then((response) => {
            this.form.distance = response.data.data.distance;
            this.form.ETA_date_desired = moment.utc(response.data.data.eta_date, "DD-MM-YYYY");
            this.form.ETA_time_desired = moment.utc(response.data.data.eta_time, "HH:mm");
          })
          .catch((error) => {
            this.$message.error(error.message);
          });
      }
    },

    calcAmountEstimate() {
      let form = this.form;
      
      if (typeof form.location_arrival !== 'undefined'
          && typeof form.location_destination !== 'undefined'
      ){
        axios
          .post('c-order-customer/calc-amount-estimate', {
            "weight": form.total_weight == "" ? 0 : form.total_weight,
            "location_destination_id" : form.location_destination.key ,
            "location_arrival_id" : form.location_arrival.key,
          })
          .then((response) => {
            this.form.amount_estimate = response.data.data.amount_estimate;
            this.$message.success("Giá tạm tính: " + response.data.data.amount_estimate);
          })
          .catch((error) => {
            this.$message.error(error.message);
          });
      } else {
        this.$message.error("Vui lòng chọn điểm nhận hàng và điểm trả hàng");
      }
    },
  },
  beforeDestroy() {
    EventBus.$off('calcAmountEstimate');
  },
};
</script>
<style scoped>
.ant-form-item {
  margin-bottom: 12px;
}
.header {
  display: flex;
  height: 50px;
  border-bottom: 1px solid #e8e8e8;
}
.header-title {
  margin-right: 8px;
  display: flex;
  align-items: center;
  font-weight: 600;
  padding: 0 4px;
}
.header-title-goods {
  width: 250px;
}
.header-title-unit {
  width: 150px;
}
.header-title-quantity {
  width: 100px;
}
.header-title-weight {
  width: 100px;
}
.header-title-volumn {
  width: 100px;
}
.header-title.header-title-space {
  width: 60px;
  margin-right: 0;
}
</style>