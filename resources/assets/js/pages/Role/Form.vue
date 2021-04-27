<template>
  <a-form-model :model="form" :label-col="labelCol" :wrapper-col="wrapperCol">
    <a-form-model-item label="Mã vai trò">
      <a-input v-model="form.code" />
    </a-form-model-item>
    <a-form-model-item label="Tên vai trò">
      <a-input v-model="form.name" />
    </a-form-model-item>
  </a-form-model>
</template>
<script>
export default {
  props: {
    formData: {
      code: "",
      name: "",
      description: "",
    },
  },
  data() {
    return {
      labelCol: { span: 6 },
      wrapperCol: { span: 16 },
      form: this.formData,
    };
  },
};
</script>
