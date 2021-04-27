<template>
  <a-form-model
    ref="ruleForm"
    :model="form"
    :rules="rules"
    :label-col="labelCol"
    :wrapper-col="wrapperCol"
  >
    <a-form-model-item prop="type" label="Loại KH" :style="{ display: 'none' }">
      <a-input v-model="form.type" :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item :style="{ display: 'none' }">
      <a-input v-model="form.user_id" :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item :style="{ display: 'none' }">
      <a-input v-model="form.avatar_id" :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item label="Ảnh đại diện" style="display: flex">
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
    <a-form-model-item label="Mã KH" prop="customer_code">
      <a-input v-model="form.customer_code" allow-clear :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item prop="full_name" label="Tên KH">
      <a-input v-model="form.full_name" allow-clear :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item label="Số điện thoại" prop="mobile_no">
      <a-input v-model="form.mobile_no" allow-clear :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item label="Loại khách hàng" prop="type">
      <a-radio-group v-model="form.type" @change="changeClientType" :disabled= "this.formData.mode == 'add' ? false : true">
        <a-radio value="1">
          Doanh nghiệp
        </a-radio>
        <a-radio value="2">
          Cá nhân
        </a-radio>
      </a-radio-group>
    </a-form-model-item>
    <a-form-model-item label="Email" prop="email">
      <a-input v-model="form.email" allow-clear :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item label="Tài khoản" prop="username">
      <a-input v-model="form.username" allow-clear :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item label="Mật khẩu" prop="password">
      <a-input-password v-model="form.password" :style="inputWidth"/>
    </a-form-model-item>
    <a-form-model-item prop="confirm_password" label="Xác nhận mật khẩu">
      <a-input-password v-model="form.confirm_password" :style="inputWidth"/>
    </a-form-model-item>
    <div v-if="this.clientType == 1">
      <a-form-model-item label="Mã số thuế" prop="tax_code">
        <a-input v-model="form.tax_code" allow-clear :style="inputWidth" />
      </a-form-model-item>
      <a-form-model-item label="Người đại diện" prop="delegate">
        <a-input v-model="form.delegate" allow-clear :style="inputWidth" />
      </a-form-model-item>
    </div>
    <div v-else>
      <a-form-model-item prop="birth_date" label="Ngày sinh">
          <a-date-picker
            v-model="form.birth_date"
            type="date"
            format="DD-MM-YYYY"
            :style="{ width: '100%' }"
            placeholder="Vui lòng chọn ngày sinh"
          />
        </a-form-model-item>
      <a-form-model-item label="Giới tính" prop="sex">
        <a-select v-model="form.sex" :style="inputWidth" >
          <a-select-option value="male">
            Nam
          </a-select-option>
          <a-select-option value="female">
            Nữ
          </a-select-option>
          <a-select-option value="other">
            Khác
          </a-select-option>
        </a-select>
      </a-form-model-item>
    </div>
  </a-form-model>
</template>
<script>
import formUtility from "@/formUtility";
import axios from "axios";
import Lang from "@/common/Lang";

function getBase64(img, callback) {
  const reader = new FileReader();
  reader.addEventListener("load", () => callback(reader.result));
  reader.readAsDataURL(img);
}

export default {
  mixins: [formUtility],
  props: {
    formData: {},
    width :String,
    show: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    let inputWidth = this.width ? this.width : 'width  100%';
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

    let validateTypeClient = (rule, value, callback) => {
      if (this.clientType == 1 && ['delegate', 'tax_code'].includes(rule.field)) {
        if (value === "") {
          callback(new Error("Vui lòng nhập " + Lang["Client"].attribute[rule.field]));
        } else if (value.length > 250) {
          callback(new Error("Độ dài " + Lang[field.name] + " tối đa là 250"));
        } else {
          callback();
        }
      }
    };

    const fields = [
      { name: "customer_code", required: true, min: 0, max: 50 },
      { name: "tax_code", validator: validateTypeClient},
      { name: "full_name", required: true, min: 0, max: 250 },
      { name: "delegate", validator: validateTypeClient},
      { name: "mobile_no", required: true, min: 0, max: 20 },
      { name: "username", required: true },
      { name: "password", validator: validatePassword, trigger: "change" },
      {
        name: "confirm_password",
        validator: validateConfirmPassword,
        trigger: "change",
      },
    ];
    let rules = this.generateRules("Client", fields);
    return {
      labelCol: { span: 6 },
      wrapperCol: { span: 16 },
      form: this.formData,
      rules,
      loading: false,
      imageUrl: "",
      inputWidth,
      clientType: 1,
    };
  },
  mounted() {
    this.imageUrl = this.form.path_of_avatar_id || "";
    this.clientType = this.form.type !== "" ? this.form.type : 1;
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
    changeClientType(e) {
      this.clientType = e.target.value;
      this.form.tax_code = '';
      this.form.delegate = '';
      this.form.birth_date = '';
      this.form.sex = '';
      this.form.type = e.target.value
    }
  },
};
</script>
<style scoped>
.ant-form-item {
  margin-bottom: 12px;
}
</style>