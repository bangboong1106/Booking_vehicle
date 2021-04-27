<template>
  <a-form-model
    ref="ruleForm"
    :model="form"
    :rules="rules"
    :label-col="labelCol"
    :wrapper-col="wrapperCol"
  >
    <a-form-model-item prop="order_date" label="Ngày đặt hàng">
      <a-date-picker
        v-model="form.order_date"
        :locale="locale"
        type="date"
        format="DD-MM-YYYY"
        :style="{ width: '50%' }"
      />
    </a-form-model-item>
    <a-form-model-item label="Điểm trả hàng" prop="location_arrival">
      <o-select
        :value="form.location_arrival"
        @input="form.location_arrival = $event"
        placeholder="Vui lòng chọn điểm nhận hàng"
        entity="location"
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
    <a-form-model-item label="Giá trị hàng hoá" prop="goods_amount">
      <a-input-number v-model="form.goods_amount" :style="{ width: '50%' }" />
    </a-form-model-item>
    <a-list item-layout="horizontal" :data-source="relationForm.list_goods">
      <div slot="header" class="header" style="background-color: #fafafa">
        <div class="header-title header-title-goods">Hàng hóa</div>
        <div class="header-title header-title-unit">Đơn vị</div>
        <div class="header-title header-title-quantity">Số lượng</div>
        <div class="header-title header-title-weight">
          Tổng trọng lượng (kg)
        </div>
        <div class="header-title header-title-volumn">Tổng thể lượng (m3)</div>
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
          :value="item.goods_type"
          :ref="`goodsType${index}`"
          @input="onChangeGoods($event, item, index)"
          placeholder="Vui lòng chọn đơn vị hàng hoá"
          entity="goods"
          :style="{ width: '250px', marginRight: '8px' }"
        />
        <o-select
          :value="item.goods_unit"
          @input="item.goods_unit_id = $event.key"
          placeholder="Vui lòng chọn đơn vị hàng hoá"
          entity="goods-unit"
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
          @click="removeGoods(item)"
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
  </a-form-model>
</template>
<script>
import locale from "ant-design-vue/es/date-picker/locale/vi_VN";
import formUtility from "@/formUtility";
import OneLogSelect from "@/components/Select/OneLogSelect";
import axios from "axios";
import constant from "@/constant";
const columns = [
  {
    title: "Hàng hóa",
    dataIndex: "goods",
    key: "goods",
  },
  {
    title: "Đơn vị",
    dataIndex: "unit",
    key: "unit",
  },
  {
    title: "Số lượng",
    dataIndex: "quantity",
    key: "quantity",
  },
  {
    title: "Tổng trọng lượng",
    dataIndex: "weight",
    key: "weight",
  },
  {
    title: "Tổng thể lượng",
    dataIndex: "volumn",
    key: "volumn",
  },
];

export default {
  mixins: [formUtility],
  components: {
    "o-select": OneLogSelect,
  },
  props: {
    formData: {},
    show: {
      type: Boolean,
      default: false,
    },
  },
  mounted() {
    this.onLoadDefault();
  },
  watch: {
    show: function (val) {
      if (val) {
        this.onLoadDefault();
      }
    },
  },
  data() {
    const fields = [
      { name: "order_no", required: true },
      { name: "location_arrival", required: true },
      { name: "order_date", required: true },
    ];
    let rules = this.generateRules("OrderClient", fields);
    return {
      display: true,
      constant: constant.orderCustomerStatusConstant,
      columns,
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
      relationForm: {
        list_goods: this.formData.list_goods || [],
      },
    };
  },
  methods: {
    onLoadDefault() {
      if (this.form.mode === "add") {
        let id = this.$auth.user().customer_id;
        axios
          .get(`c-default-data/default?client_id=${id}`)
          .then((response) => {
            if (response.data.errorCode != 0) {
              return;
            }
            let data = response.data.data;
            if (data && data.location_arrival_id) {
              this.form.location_arrival = {
                key: data.location_arrival_id,
                label: data.name_of_location_arrival_id,
              };
            } else {
              this.form.location_arrival = void 0;
            }
          })
          .catch((error) => {});
      }
    },
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

      let orderGoods = this.$refs[`item${index}`];
      let data = orderGoods.$children[0].items.find((p) => p.id == $event.key);
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
    },
    onChangeQuantity($event, item, index) {
      item.total_weight = item.quantity * item.weight;
      item.total_volume = item.quantity * item.volume;
    },
    addGoods() {
      this.display = false;
      this.relationForm.list_goods.push({
        index: this.relationForm.list_goods.length + 1,
        goods_type_id: "",
        goods_unit_id: "",
        quantity: 1,
        weight: 0,
        volume: 0,
        total_weight: 0,
        total_volume: 0,
      });
    },
    removeGoods(item) {
      let index = this.relationForm.list_goods.indexOf(item);
      if (index !== -1) {
        this.relationForm.list_goods.splice(index, 1);
      }
    },
  },
};
</script>
<style scoped>
.ant-form-item {
  margin-bottom: 12px;
}
.header {
  display: flex;
  border-bottom: 1px solid #3333332b;
  height: 50px;
}
.header-title {
  margin-right: 8px;
  display: flex;
  align-items: center;
  font-weight: 700;
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