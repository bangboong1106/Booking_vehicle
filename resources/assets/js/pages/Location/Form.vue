<template>
  <a-form-model
    ref="ruleForm"
    :rules="rules"
    :model="form"
    :label-col="labelCol"
    :wrapper-col = "wrapperCol"
    :layout="layout"
  >
    <a-form-model-item label="Mã địa điểm" prop="code">
      <a-input v-model="form.code" :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item label="Tên địa điểm" prop="title">
      <a-input v-model="form.title" :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item label="Loại địa điểm">
      <o-select
        :value="form.location_type"
        @input="form.location_type = $event"
        placeholder="Vui lòng chọn loại địa điểm"
        entity="location-type"
        :title= "'Loại địa điểm'"
        :icon="'environment'"
        :isAddSelect = "true"
      />
    </a-form-model-item>
    <a-form-model-item label="Nhóm địa điểm">
      <o-select
        :value="form.location_group"
        @input="form.location_group = $event"
        placeholder="Vui lòng chọn nhóm địa điểm"
        entity="location-group"
        :title= "'Nhóm địa điểm'"
        :icon="'environment'"
        :isAddSelect = "true"
      />
    </a-form-model-item>
    <a-form-model-item label="Tỉnh/Thành phố" prop="province">
      <o-select
        ref="province"
        :value="form.province"
        @input="onChangeProvince"
        placeholder="Vui lòng chọn tỉnh/thành phố"
        entity="province"
        :title= "'Tỉnh/Thành phố'"
        :icon="'environment'"
        :isDisplay = "'none'"
        :isDisplayAdd = "'none'"

      />
    </a-form-model-item>
    <a-form-model-item label="Quận/Huyện" prop="district">
      <o-select
        ref="district"
        :value="form.district"
        @input="onChangeDistrict"
        placeholder="Vui lòng chọn quận/huyện"
        entity="district"
        :disabled="districtDisabled"
        :title= "'Quận/Huyện'"
        :icon="'environment'"
        :isDisplay = "'none'"

      />
    </a-form-model-item>
    <a-form-model-item label="Phường/Xã" prop="ward">
      <o-select
        ref="ward"
        :value="form.ward"
        @input="form.ward = $event"
        placeholder="Vui lòng chọn phường/xã"
        entity="ward"
        :disabled="wardDisabled"
        :title= "'Phường/Xã'"
        :icon="'environment'"
        :isDisplay = "'none'"
      />
    </a-form-model-item>
  </a-form-model>
</template>
<script>
import OneLogSelect from "@/components/Select/OneLogSelect";
import formUtility from "@/formUtility";

export default {
  mixins: [formUtility],
  components: {
    "o-select": OneLogSelect,
  },
  props: {
    formData: {},
    width : String
  },
  data() {
    let inputWidth = this.width ? this.width : 'width  100%';
    const fields = [
      { name: "code", required: true, min: 0, max: 50 },
      { name: "title", required: true, min: 0, max: 250 },
      { name: "province", required: true },
      { name: "district", required: true },
      { name: "ward", required: true },
    ];
    let rules = this.generateRules("Location", fields);
    const icons = {

    };
    return {
      labelCol: { span: 6 },
      wrapperCol: { span : 16 },
      layout:"horizontal",
      form: this.formData,
      rules,
      districtDisabled: true,
      wardDisabled: true,
      inputWidth

    };
  },
  methods: {
    onChangeProvince($event) {
      this.form.province = $event;
      let item = this.$refs.district;

      this.districtDisabled = typeof $event == "undefined";
      this.form.district = void 0;

      this.wardDisabled = true;
      this.form.ward = void 0;

      this.onChangeLocation($event, item, "province_id");
    },
    onChangeDistrict($event) {
      this.form.district = $event;
      let item = this.$refs.ward;
      this.wardDisabled = typeof $event == "undefined";
      this.form.ward = void 0;
      this.onChangeLocation($event, item, "district_id");
    },
    onChangeLocation($event, item, field) {
      let value = $event ? $event.key : void 0;
      item.onLoad({ field, value });
    },
  },

};
</script>
<style scoped>
.ant-form-item {
  margin-bottom: 12px;

}


</style>