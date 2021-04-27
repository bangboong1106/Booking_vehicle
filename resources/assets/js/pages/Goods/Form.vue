<template>
  <a-form-model
    ref="ruleForm"
    :model="form"
    :rules="rules"
    :label-col="labelCol"
    :wrapper-col="wrapperCol"
  >
    <a-form-model-item :style="{ display: 'none' }">
      <a-input v-model="form.file_id" />
    </a-form-model-item>
    <a-form-model-item label="Hình ảnh">
      <a-upload
        name="avatar"
        list-type="picture-card"
        class="avatar-uploader"
        :show-upload-list="false"
        :customRequest="customRequest"
        :before-upload="beforeUpload"
        @change="handleChange"
      >
        <img
          v-if="imageUrl"
          :src="imageUrl"
          alt="file"
          :style="{ width: '120px', height: '120px' }"
        />
        <div v-else>
          <a-icon :type="loading ? 'loading' : 'plus'" />
          <div class="ant-upload-text">Tải ảnh hàng hoá</div>
        </div>
      </a-upload>
    </a-form-model-item>
    <a-form-model-item label="Mã hàng hoá" prop="code">
      <a-input v-model="form.code" />
    </a-form-model-item>
    <a-form-model-item label="Tên hàng hoá" prop="title">
      <a-input v-model="form.title" />
    </a-form-model-item>
    <a-form-model-item label="Đơn vị hàng hoá">
      <o-select
        :value="form.goods_unit"
        @input="form.goods_unit = $event"
        placeholder="Vui lòng chọn đơn vị hàng hoá"
        entity="goods-unit"
        :icon="'gift'"
        :isAddSelect = "true"
      />
    </a-form-model-item>
    <a-form-model-item label="Giá mua (VND)">
      <a-input-number
        v-model="form.in_amount"
        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"
        :min="0"
        :max="999999999999"
        :style="{ width: '50%' }"
      />
    </a-form-model-item>
    <a-form-model-item label="Giá bán (VND)">
      <a-input-number
        v-model="form.out_amount"
        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"
        :min="0"
        :max="999999999999"
        :style="{ width: '50%' }"
      />
    </a-form-model-item>
    <a-form-model-item label="Dung tích (m3)">
      <a-input-number
        v-model="form.volume"
        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"
        :min="0"
        :max="100000"
        :style="{ width: '50%' }"
      />
    </a-form-model-item>
    <a-form-model-item label="Tải trọng (kg)">
      <a-input-number
        v-model="form.weight"
        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"
        :min="0"
        :max="100000"
        :style="{ width: '50%' }"
      />
    </a-form-model-item>
    <a-form-model-item label="Ghi chú">
      <a-input v-model="form.note" type="textarea" />
    </a-form-model-item>
  </a-form-model>
</template>
<script>
import OneLogSelect from "@/components/Select/OneLogSelect";
import formUtility from "@/formUtility";
import axios from "axios";

function getBase64(img, callback) {
  const reader = new FileReader();
  reader.addEventListener("load", () => callback(reader.result));
  reader.readAsDataURL(img);
}
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
  data() {
    const fields = [
      { name: "code", required: true },
      { name: "title", required: true },
    ];
    let rules = this.generateRules("Goods", fields);
    return {
      labelCol: { span: 6 },
      wrapperCol: { span: 16 },
      form: this.formData,
      rules,
      loading: false,
      imageUrl: "",
    };
  },
  mounted() {
    this.imageUrl = this.form.path_of_file_id || "";
  },
  watch: {
    show: function (val) {
      if (!val) {
        this.imageUrl = "";
      } else {
        this.imageUrl = this.formData
          ? this.formData.path_of_file_id
          : this.form.path_of_file_id || "";
      }
    },
  },
  methods: {
    handleChange(info) {
      if (info.file.status === "uploading") {
        this.loading = true;
        return;
      }
      if (info.file.status === "done") {
        getBase64(info.file.originFileObj, (imageUrl) => {
          this.imageUrl = imageUrl;
          this.loading = false;
        });
      }
    },
    beforeUpload(file) {
      const isJpgOrPng =
        file.type === "image/jpeg" || file.type === "image/png";
      if (!isJpgOrPng) {
        this.$message.error("Bạn chỉ được upload ảnh định dạng jpeg hoặc png");
      }
      const isLt5M = file.size / 1024 / 1024 < 5;
      if (!isLt5M) {
        this.$message.error("Dung lượng ảnh không được quá 5MB");
      }
      return isJpgOrPng && isLt5M;
    },
    customRequest(options) {
      const fmData = new FormData();
      const { onSuccess, onError, file, onProgress } = options;
      fmData.append("file", file);
      axios
        .post("c-uploadFiles", fmData)
        .then((respones) => {
          this.form.file_id = respones.data.data.fileId;
          onSuccess(respones.body);
        })
        .catch((err) => {
          onError(err);
        });
    },
  },
};
</script>
<style scoped>
.ant-form-item {
  margin-bottom: 12px;
}
</style>