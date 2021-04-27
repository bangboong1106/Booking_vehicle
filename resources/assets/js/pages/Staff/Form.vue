<template>
  <a-form-model
    ref="ruleForm"
    :model="form"
    :rules="rules"
    :label-col="labelCol"
    :wrapper-col="wrapperCol"
  >
    <a-form-model-item prop="type" label="Loại KH" :style="{ display: 'none' }">
      <a-input v-model="form.type" />
    </a-form-model-item>
    <a-form-model-item :style="{ display: 'none' }">
      <a-input v-model="form.user_id" />
    </a-form-model-item>
    <a-form-model-item :style="{ display: 'none' }">
      <a-input v-model="form.avatar_id" />
    </a-form-model-item>
    <a-form-model-item label="Ảnh đại diện">
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
          alt="avatar"
          :style="{ width: '120px', height: '120px' }"
        />
        <div v-else>
          <a-icon :type="loading ? 'loading' : 'plus'" />
          <div class="ant-upload-text">Tải ảnh dại diện</div>
        </div>
      </a-upload>
    </a-form-model-item>
    <a-form-model-item prop="customer_code" label="Mã nhân viên">
      <a-input v-model="form.customer_code" allow-clear />
    </a-form-model-item>
    <a-form-model-item prop="full_name" label="Họ tên">
      <a-input v-model="form.full_name" allow-clear />
    </a-form-model-item>
    <a-form-model-item prop="identity_no" label="CMND/CCCD">
      <a-input v-model="form.identity_no" allow-clear />
    </a-form-model-item>
    <a-form-model-item prop="birth_date" label="Ngày sinh">
      <a-date-picker
        v-model="form.birth_date"
        :locale="locale"
        type="date"
        format="DD-MM-YYYY"
        :style="{ width: '50%' }"
      />
    </a-form-model-item>
    <a-form-model-item label="Số điện thoại" prop="mobile_no">
      <a-input v-model="form.mobile_no" allow-clear />
    </a-form-model-item>
    <a-form-model-item label="Email">
      <a-input v-model="form.email" allow-clear />
    </a-form-model-item>
    <a-form-model-item prop="username" label="Tài khoản">
      <a-input v-model="form.username" allow-clear />
    </a-form-model-item>
    <a-form-model-item prop="password" label="Mật khẩu">
      <a-input-password v-model="form.password" allow-clear />
    </a-form-model-item>
    <a-form-model-item prop="confirm_password" label="Xác nhận mật khẩu">
      <a-input-password v-model="form.confirm_password" allow-clear />
    </a-form-model-item>
  </a-form-model>
</template>
<script>
import locale from "ant-design-vue/es/date-picker/locale/vi_VN";
import formUtility from "@/formUtility";
import axios from "axios";

function getBase64(img, callback) {
  const reader = new FileReader();
  reader.addEventListener("load", () => callback(reader.result));
  reader.readAsDataURL(img);
}

export default {
  mixins: [formUtility],
  props: {
    formData: {},
    show: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    let validatePassword = (rule, value, callback) => {
      if (this.form.mode == "add") {
        if (value === "") {
          callback(new Error("Vui lòng nhập giá trị nhập mật khẩu"));
        } else {
          if (this.form.confirm_password !== "") {
            this.$refs.ruleForm.validateField("confirm_password");
          }
          callback();
        }
      } else {
        if (
          typeof value != "undefined" &&
          value !== "" &&
          this.form.confirm_password !== ""
        ) {
          this.$refs.ruleForm.validateField("confirm_password");
        }
        callback();
      }
    };

    let validateConfirmPassword = (rule, value, callback) => {
      if (this.form.mode == "add") {
        if (value === "") {
          callback(new Error("Vui lòng nhập giá trị xác nhận mật khẩu"));
        } else if (value !== this.form.password) {
          callback(new Error("Xác nhận mật khẩu và mật khẩu không giống nhau"));
        } else {
          callback();
        }
      } else {
        if (
          typeof value != "undefined" &&
          value !== "" &&
          value !== this.form.password
        ) {
          callback(new Error("Xác nhận mật khẩu và mật khẩu không giống nhau"));
        } else {
          callback();
        }
      }
    };
    const fields = [
      { name: "customer_code", required: true, min: 0, max: 50 },
      { name: "full_name", required: true, min: 0, max: 250 },
      { name: "identity_no", required: true, min: 0, max: 20 },
      { name: "birth_date", required: true, trigger: "change" },
      { name: "mobile_no", required: true, min: 0, max: 20 },
      { name: "username", required: true },
      { name: "password", validator: validatePassword, trigger: "change" },
      {
        name: "confirm_password",
        validator: validateConfirmPassword,
        trigger: "change",
      },
    ];
    let rules = this.generateRules("Staff", fields);
    return {
      labelCol: { span: 8 },
      wrapperCol: { span: 16 },
      form: this.formData,
      locale,
      rules,
      loading: false,
      imageUrl: "",
    };
  },
  mounted() {
    this.imageUrl = this.form.path_of_avatar_id || "";
  },
  watch: {
    show: function (val) {
      if (!val) {
        this.imageUrl = "";
      } else {
        this.imageUrl = this.formData
          ? this.formData.path_of_avatar_id
          : this.form.path_of_avatar_id || "";
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
          this.form.avatar_id = respones.data.data.fileId;
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
.avatar-uploader > .ant-upload {
  width: 128px;
  height: 128px;
}
.ant-upload-select-picture-card i {
  font-size: 32px;
  color: #999;
}

.ant-upload-select-picture-card .ant-upload-text {
  margin-top: 8px;
  color: #666;
}
</style>
