<template>
  <a-form-model
    ref="ruleForm"
    :model="form"
    :rules="rules"
    :label-col="labelCol"
    :wrapper-col="wrapperCol"
    :style="{ top: 0 }"
  >
    <a-form-model-item label="Mã dữ liệu mặc định" prop="code">
      <a-input
        v-model="form.code"
        title="Mã dữ liệu mặc định"
        placeholder="Vui lòng nhập mã dữ liệu mặc định"
      />
    </a-form-model-item>
    <a-form-model-item label="Khách hàng" prop="client">
      <o-select
        :value="form.client"
        :textField="'full_name'"
        @input="form.client = $event"
        placeholder="Vui lòng chọn khách hàng"
        :entity="'client'"
        :title= "'Khách hàng'"
        :icon="'user'"
        :isAddSelect = "true"
      />
    </a-form-model-item>
    <a-form-model-item label="Điểm nhận hàng" prop="location_destination">
      <o-select
        :value="form.location_destination"
        @input="form.location_destination = $event"
        placeholder="Vui lòng chọn điểm nhận hàng"
        entity="location"
        :title= "'Điểm nhận hàng'"
        :icon="'environment'"
      />
    </a-form-model-item>
    <a-form-model-item label="Điểm trả hàng" prop="location_arrival">
      <o-select
        :value="form.location_arrival"
        @input="form.location_arrival = $event"
        placeholder="Vui lòng chọn điểm trả hàng"
        entity="location"
        :title= "'Điểm trả hàng'"
        :icon="'environment'"
      />
    </a-form-model-item>
  </a-form-model>
</template>
<script>
import locale from "ant-design-vue/es/date-picker/locale/vi_VN";
import formUtility from "@/formUtility";
import OneLogSelect from "@/components/Select/OneLogSelect";

export default {
  mixins: [formUtility],
  components: {
    "o-select": OneLogSelect,
  },
  props: {
    formData: {},
  },
  data() {
    const fields = [
      { name: "client", required: true },
      { name: "location_destination", required: true },
      { name: "location_arrival", required: true },
      { name: "code", required: true },
    ];
    let rules = this.generateRules("DefaultData", fields);
    return {
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
    };
  },
  methods: {

  },
};
</script>