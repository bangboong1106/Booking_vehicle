
<template>
  <div>
    <a-page-header style="border: 1px solid rgb(235, 237, 240)" :title="title">
      <template slot="subTitle"
        ><a-icon type="undo" @click="reload" />
      </template>
      <template slot="extra" v-if="advanceMenu.length == 0">
        <a-button
          class="add-btn"
          type="primary"
          @click="handleAdd"
          v-if="isAddButton"
        >
          Thêm mới
        </a-button>
      </template>
      <template slot="extra" v-else>
        <span>{{ `Đã chọn: ${advanceMenu.length}` }}</span>
        <a-button
          class="mass-del-btn"
          type="danger"
          @click="handleMassDelete"
          v-if="isAddButton"
        >
          Xoá
        </a-button>
      </template>
    </a-page-header>
    <o-grid
      ref="grid"
      :entity="entity"
      :columns="columns"
      :title="title"
      :isPopup="false"
      @recordEdit="recordEdit($event)"
      @openRecord="openRecord($event)"
      @emitRecord="emitRecord($event)"
      @showAdvance="showAdvance"
      :isAction="isAction"
    ></o-grid>
    <a-modal
      :title="title"
      :visible="visible"
      :confirm-loading="confirmLoading"
      okText="Lưu"
      cancelText="Huỷ"
      @ok="handleOk"
      @cancel="handleCancel"
      :width="widthModal"
      :bodyStyle="bodyStyleModal"
      :destroyOnClose="true"
    >
      <template slot="footer" v-if="this.entity == 'order-customer'">
        <div>
            <a-button key="back" @click="handleCancel">
              Huỷ
            </a-button>
            <a-button key="calcAmount" @click="calcAmountEstimate">
              Tính giá
            </a-button>
            <a-button key="submit" type="primary" :confirm-loading="confirmLoading" @click="handleOk" :loading = "loading">
              Lưu
            </a-button>
        </div>
      </template>
      <component
        :is="detail"
        ref="detail"
        :formData="formData"
        :show="visible"
      ></component>
    </a-modal>
  </div>
</template>
<script>
import axios from "axios";
import OneLogGrid from "@/components/Grid/OneLogGrid";
import formUtility from "@/formUtility";
import moment from "moment/moment";
import ColumnConfig from "@/common/ColumnConfig";
import Lang from "@/common/Lang";
import Model from "@/common/Model";
import EventBus from '@/event-bus';

import OrderCustomer from "@/pages/OrderCustomer/Form";
import OrderClient from "@/pages/OrderClient/Form";
import Location from "@/pages/Location/Form";
import Staff from "@/pages/Staff/Form";
import Client from "@/pages/Client/Form";
import LocationType from "@/pages/LocationType/Form";
import LocationGroup from "@/pages/LocationGroup/Form";
import Goods from "@/pages/Goods/Form";
import GoodsUnit from "@/pages/GoodsUnit/Form";
import DefaultData from "@/pages/DefaultData/Form";
import constant from "@/constant";

export default {
  mixins: [formUtility],
  components: {
    "o-grid": OneLogGrid,
  },
  props: {
    entity: {
      type: String,
      default: "",
    },
    isAdd: {
      type: Boolean,
      default: true,
    },
    isAction: {
      type: Boolean,
      default: true,
    },
    widthModal: "",
    bodyStyleModal: {},
    supportRole: {
      type: Array,
      default: [],
    },
  },
  computed: {
    detail() {
      switch (this.entity) {
        case "order-customer":
          return OrderCustomer;
        case "order-client":
          return OrderClient;
        case "client":
          return Client;
        case "staff":
          return Staff;
        case "default-data":
          return DefaultData;
        case "location":
          return Location;
        case "location-type":
          return LocationType;
        case "location-group":
          return LocationGroup;
        case "goods":
          return Goods;
        case "goods-unit":
          return GoodsUnit;
      }
    },
  },
  data() {
    let entity = this.convertPascalCase(this.entity);
    let columns = ColumnConfig[entity] || [];
    let title = Lang[entity].name || "";

    let form = this.generateFormData(Model[entity]) || {};
    return {
      visible: false,
      confirmLoading: false,
      formData: form,
      id: null,
      isAddButton: this.isAdd,
      columns,
      title,
      config: Model[entity],
      advanceMenu: [],
      loading : false,
      isSubmitting : false,
    };
  },
  mounted() {
    if (!this.supportRole.includes(this.$auth.user().customer_type)) {
      this.$router.push({ path: "access-denied" });
    }
  },
  methods: {
    reload() {
      if (this.$refs.grid) {
        this.$refs.grid.reload();
      }
    },
    convertPascalCase(title) {
      return title.replace(/(^\w|-\w)/g, function (text) {
        return text.replace(/-/, "").toUpperCase();
      });
    },
    showModal() {
      this.visible = true;
    },
    handleOk(e) {
      if ((this.entity == 'order-customer' || this.entity == 'order-client' ) && this.isSubmitting) {
        return;
      } 

      this.$refs.detail.$refs.ruleForm.validate((valid) => {
        if (valid) {
          this.loading = true;
          this.confirmLoading = true;
          try {
            let form = JSON.parse(JSON.stringify(this.$refs.detail.form));

            if (this.id) {
              form.id = this.id;
            }
            for (let prop in form) {
              for (let propConfig in this.config) {
                if (this.config[propConfig].relation == prop) {
                  form[prop + "_id"] = form[prop]["key"];
                  delete form[prop];
                }
              }
              if (this.config[prop] && this.config[prop].dataType) {
                if (this.config[prop].dataType == "time") {
                  form[prop] = moment(form[prop]).format("H:mm:ss");
                }
              }
            }
            if (this.$refs.detail.relationForm) {
              for (let prop in this.$refs.detail.relationForm) {
                form[prop] = JSON.parse(
                  JSON.stringify(this.$refs.detail.relationForm[prop])
                );
              }
            }
            axios
              .post(`c-${this.entity}/save`, form)
              .then((response) => {
                this.confirmLoading = false;
                if (response.data.errorCode != 0) {
                  if (Array.isArray(response.data.errorMessage)) {
                    this.$message.error(
                      response.data.errorMessage
                        .map((p) => p.errorMessage)
                        .join("\n")
                    );
                  } else {
                    this.$message.error(response.data.errorMessage);
                  }
                  this.loading = false;
                  return false;
                }
                this.visible = false;
                this.loading =false;
                this.$message.success(`Lưu ${this.title} thành công`);
                this.isSubmitting = true;
                if (this.$refs.grid) {
                  this.$refs.grid.reload();
                }
              })
              .catch((error) => {
                this.confirmLoading = false;
                this.$message.error(error.message);
              });
          } catch (err) {
            this.$message.error(err);
          }
        } else {
          this.confirmLoading = false;
          return false;
        }
      });
    },
    handleCancel(e) {
      this.visible = false;
    },
    handleAdd() {
      this.id = null;
      if (this.$refs.detail) {
        this.$refs.detail.$refs.ruleForm.resetFields();
      }
      this.formData = this.generateFormData(this.config);
      this.formData["mode"] = "add";
      if (this.$refs.detail) {
        this.$refs.detail.form = this.formData;
      }
      if (this.$refs.detail && this.$refs.detail.relationForm) {
        for (let prop in this.$refs.detail.relationForm) {
          this.$refs.detail.relationForm[prop] = [];
        }
      }
      this.visible = true;

      if (this.entity == 'order-customer' || this.entity == 'order-client') {
        this.isSubmitting = false;
      } 
    },
    openRecord(record) {
      this.$emit("openRecord", record);
    },
    emitRecord(record) {
      this.$emit("emitRecord", record);
    },
    showAdvance: function(record) {
      this.advanceMenu = record;
    },
    recordEdit(record) {
      this.id = record.id;
      this.loading = false;
      if (this.$refs.detail) {
        this.$refs.detail.$refs.ruleForm.resetFields();
      }
      axios
        .get(`c-${this.entity}/detail?id=${record.id}`)
        .then((response) => {
          if (response.data.errorCode != 0) {
            if (Array.isArray(response.data.errorMessage)) {
              this.$message.error(
                response.data.errorMessage.map((p) => p.errorMessage).join("\n")
              );
            } else {
              this.$message.error(response.data.errorMessage);
            }
            return;
          }

          let data = response.data.data;
          this.formData = this.generateFormData(this.config);
          for (let prop in this.config) {
            this.formData[prop] = data[prop];

            let config = this.config[prop];
            if (config.relation) {
              this.formData[config.relation] = {
                key: data[config.relation + "_id"] || "",
                label: data["name_of_" + config.relation + "_id"] || "",
              };
            }

            if (config.dataType) {
              if (config.dataType == "date") {
                this.formData[prop] = moment.utc(data[prop], "YYYY-MM-DD");
              }
              if (config.dataType == "time") {
                this.formData[prop] = moment.utc(data[prop], "H:mm:ss");
              }

              if (config.dataType == "array") {
                if (config.dataRelation) {
                  for (let i = 0; i < config.dataRelation.length; i++) {
                    for (let j = 0; j < data[prop].length; j++) {
                      let item = data[prop][j];
                      item[config.dataRelation[i]] = {
                        key: item[config.dataRelation[i] + "_id"],
                        label:
                          item["name_of_" + config.dataRelation[i] + "_id"],
                      };
                    }
                  }
                }
              }
            }
          }

          this.formData["mode"] = "edit";

          if (this.$refs.detail) {
            this.$refs.detail.form = this.formData;
          }

          if (this.$refs.detail && this.$refs.detail.relationForm) {
            for (let prop in this.$refs.detail.relationForm) {
              this.$refs.detail.relationForm[prop] = JSON.parse(
                JSON.stringify(this.formData[prop])
              );
            }
          }
          this.visible = true;
        })
        .catch((error) => {
          this.$message.error(error.message);
        });
    },
    calcAmountEstimate() {
      EventBus.$emit('calcAmountEstimate');
    },
    handleMassDelete() {
      let idRecord = this.advanceMenu;
      let data = this.$refs.grid.data;
      let grid = this.$refs.grid;
      let codeRecord = [];
      let code = 'code';
      let entity = this.entity;
      let component = this;
      let count = 0;

      if (Object.keys(constant.entityFieldCode).includes(entity)) {
        code = constant.entityFieldCode[entity];
      }

      data.forEach((record) => {
        if (idRecord.includes(record.id)) {
          codeRecord.length < 3 ? codeRecord.push(record[code]) : count ++;
        }
      });

      if (codeRecord.length >= 2 && count > 0 ) {
        codeRecord.push(`và ${count} ${component.title} còn lại`);
      }

      this.$confirm({
        title: `Bạn chắc chắn muốn xoá ${component.title} ${codeRecord.join(', ')} ?`,
        content: "",
        okText: "Xác nhận",
        cancelText: "Huỷ",
        onOk() {
          axios
            .delete(`c-${entity}?id=${idRecord.join(',')}`)
            .then((response) => {
              if (response.data.errorCode == 0) {
                component.$message.success(`Xoá ${component.title} thành công`);
                component.reload();
                component.advanceMenu = [];
                grid.reload();
              } else {
                component.$message.error(response.data.errorMessage);
              }
            })
            .catch((error) => {
              component.$message.error(error.message);
            });
        },
        onCancel() {},
      });
    }
  },
};
</script>