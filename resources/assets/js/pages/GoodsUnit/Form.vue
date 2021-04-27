<template>
  <a-form-model
    ref="ruleForm"
    :rules="rules"
    :model="form"
    :label-col="labelCol"
    :wrapper-col="wrapperCol"
  >
    <a-form-model-item label="Mã đơn vị tính" prop="code">
      <a-input v-model="form.code" :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item label="Tên đơn vị tính" prop="title">
      <a-input v-model="form.title" :style="inputWidth"/>
    </a-form-model-item>
  </a-form-model>
</template>
<script>
import formUtility from "@/formUtility";

export default {
  mixins: [formUtility],
  props: {
    formData: {},
    width : String
  },
  data() {
    let inputWidth = this.width ? this.width : 'width : 100%'
    const fields = [
      { name: "code", required: true },
      { name: "title", required: true },
    ];
    let rules = this.generateRules("GoodsUnit", fields);
    return {
      labelCol: { span: 6 },
      wrapperCol: { span: 16 },
      form: this.formData,
      rules,
      inputWidth
    };
  },
};
</script>
