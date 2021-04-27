<template>
  <div class="wrapper-content">

    <div class="heading">
      <div class="new-add" v-if="isAddSelect" >
        <a-button type="primary" @click="newAdd">Thêm mới</a-button>
        <a-icon type="undo" @click="reload" style="margin-left: 8px" />
      </div>
      <div class="search-heading">
        <b>Tìm kiếm : </b>
        <a-input-search
            :placeholder="placeholder"
            v-model="value"
            :style="{width: '50%',marginLeft: '8px'}"
            @pressEnter="fetchData"
            @search="fetchData"
            @change="fetchData"
        />
      </div>
    </div>
    <a-spin :spinning="loading" size="default" tip="Đang tải...">
      <a-table
          :customRow="customRow"
          :rowClassName="setRowClassName"
          :scroll="{x:300,y:this.maxHeight}"
          :pagination="pagination"
          :row-selection="rowSelection"
          type="radio"
          :columns="columns"
          :data-source="data"
          :locale="{
          emptyText: 'Không có dữ liệu'

        }"
      >
        <span slot="date" slot-scope="text">{{
            text == null ? "" : moment(String(text)).format("DD-MM-YYYY")
          }}</span>
        <span slot="datetime" slot-scope="text">{{
            text == null ? "" : moment(String(text)).format("DD-MM-YYYY HH:mm")
          }}</span>
        <span slot="time" slot-scope="text">{{ text }}</span>
        <span slot="number" slot-scope="text">{{ text }}</span>
        <a slot="email" :href="'mailto:' + text" slot-scope="text">{{ text }}</a>
        <a slot="phone" :href="'tel:' + text" slot-scope="text">{{ text }}</a>
        <span slot="list" slot-scope="text, record, index, column">{{
            renderList(text, column)
          }}</span>
        <span slot="file" slot-scope="text">
        <img
            v-if="text"
            :src="text"
            alt="file"
            :style="{ width: '48px', height: '48px' }"
        /></span>

      </a-table>

    </a-spin>
    <a-modal
        :title="title"
        :visible="visible"
        okText="Lưu"
        cancelText="Huỷ"
        @ok="handleOk"
        @cancel="handleCancel"

    >
      <component
          :is="detail"
          ref="detail"
          :formData="formData"
          :show="visible"
          :width = "inputWidth"
      ></component>

    </a-modal>
  </div>

</template>

<script>

import axios from "axios";
import ColumnConfig from "@/common/ColumnConfig";
import debounce from 'lodash/debounce';
import Lang from "@/common/Lang";
import formUtility from "@/formUtility";
import Model from "@/common/Model";
import moment from "moment/moment";
import EventBus from "@/event-bus";

const Location = () => import("@/pages/Location/Form");
const LocationType = () => import("@/pages/LocationType/Form");
const LocationGroup = () => import("@/pages/LocationGroup/Form");
const Client = () => import("@/pages/Client/Form");
const Staff = () => import("@/pages/Staff/Form");
const GoodsUnit = () => import("@/pages/GoodsUnit/Form");


export default {

  mixins: [formUtility],

  name: "base-list",
  props: {
    loadingProp: Boolean,
    placeholder: String,
    textField: String,
    entity: String,
    isAddSelect: Boolean
  },

  data() {
    let entity = this.convertPascalCase(this.entity);
    let columns = ColumnConfig[entity];
    let cols = [];
    let form = this.generateFormData(Model[entity]) || {};


    let title = Lang[entity].name || "";
    var tmp = columns.map((p) => {
      switch (p.dataType) {
        case "number":
          p.className = "column-number";
          break;
        case "date":
        case "time":
        case "datetime":
          p.className = "column-date";
          break;
        default:
          p.className = "column-string";
          break;
      }
      if (!p.scopedSlots) {
        p.scopedSlots = {
          filterIcon: "filterIcon",
          customRender: "customRender",
        };
        if (p.dataSource) {
          p.filters = p.dataSource;
        } else {
          p.scopedSlots.filterDropdown = "filterDropdown";
        }
        if (p.isCodeIndex) {
          p.scopedSlots.customRender = "link";
        } else {
          if (p.dataType) {
            p.scopedSlots.customRender = p.dataType;
          }
        }
      }

      if (p.dataSource) {
      } else {
        p.onFilter = (value, record) =>
            record[p.dataIndex]
                .toString()
                .toLowerCase()
                .includes(value.toLowerCase());
        p.onFilterDropdownVisibleChange = (visible) => {
          if (visible) {
            setTimeout(() => {
              if (this.searchInput) {
                this.searchInput.focus();
              }
            }, 0);
          }
        };
      }
      return p;
    });
    cols = [].concat.apply(cols, tmp);
    this.fetchData = debounce(this.fetchData, 800);
    const maxHeight = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0) * 55 / 100;

    return {
      confirmLoading: false,
      visible: false,
      maxHeight,
      data: [],
      selectedRowKeys: [],
      cols: cols,
      isClick: false,
      fetching: false,
      columns,
      isDisabled: false,
      typeChoose: 'radio',
      typeData: ['Chọn một địa điểm', 'Chọn nhiều địa điểm'],
      isKeepFilter: false,
      value: null,
      loading: this.loadingProp,
      params: {pageSize: 100, pageIndex: 1, filters: []},
      pagination: {
        pageSize: 25,
      },
      title,
      formData: form,
      config: Model[entity],
      inputWidth : 'width : 75%'


    }
  },
  created() {
    axios
        .post(`c-${this.entity}/list`, this.params)
        .then((response) => {
          this.data = response.data.data.items;
          this.data.forEach((item, index) => {
            item.key = index;
          })
          if (response.data.errorCode == 0) {
            this.loading = false;
          } else {
            this.loading = true;
          }

        })
        .catch((error) => {
          this.loading = false;
          this.$message.error(error.message);
        });
  },
  methods: {
    customRow(record, index) {
      return {
        on: {
          click: (event) => {
            this.selectedRowKeys = [index];
            this.$emit('onSelectChange', {
              index: record.id,
              title: record.title,
              data: this.data,
              disabled: this.isDisabled
            })
          }
        }
      }
    },
    setRowClassName(record, index) {
      return index === 0 ? 'click-row-style' : '';
    },
    onSelectChange(selectedRowKeys) {

      this.selectedRowKeys = selectedRowKeys;
      if (this.selectedRowKeys.length > 1) {
        this.$message.error('Chọn nhiều chưa xử lý')
      } else {
        this.$emit('onSelectChange', {
          index: this.data[this.selectedRowKeys].id,
          title: this.data[this.selectedRowKeys].title,
          data: this.data,
          disabled: this.isDisabled

        });

      }


    },
    handleTypeChange(value) {
      if (value === this.typeData[0]) {
        this.typeChoose = 'radio';
      } else {
        this.typeChoose = 'checkbox';

      }
    },
    convertPascalCase(title) {
      return title.replace(/(^\w|-\w)/g, function (text) {
        return text.replace(/-/, "").toUpperCase();
      });
    },
    fetchData(value) {
      if (!this.isKeepFilter) {
        this.data = [];
      }
      this.fetching = true;
      this.loading = true;
      this.filter = this.value;
      if (!this.isKeepFilter) {
        this.params.filters = [];
        if (value) {
          this.params.filters.push({
            field: this.textField,
            value: this.value,
          });
        }
      }

      axios
          .post(`c-${this.entity}/list`, this.params)
          .then((response) => {
            this.data = response.data.data.items;
            this.data.forEach((item, index) => {
              item.key = index;
            })
            // this.data = result;
            this.loading = false;
          })
          .catch((error) => {
            this.loading = false;
            this.$message.error(error.message);
          });
    },
    newAdd() {
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
    },
    handleOk() {
      this.$refs.detail.$refs.ruleForm.validate((valid) => {
        if (valid) {
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
                    return false;
                  }
                  this.visible = false;
                  this.$message.success(`Lưu ${this.title} thành công`);
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
    reload(){
      EventBus.$emit('reload')
    },
    handleCancel() {
      this.visible = false;
    },



  },
  computed: {

    rowSelection() {

      const {selectedRowKeys} = this;
      return {
        selectedRowKeys,
        onChange: this.onSelectChange,
        type: this.typeChoose,
        hideDefaultSelections: true,
      };


    },
    detail() {
      switch (this.entity) {

        case "client":
          return Client;
        case "staff":
          return Staff;
        case "location-type":
          return LocationType;
        case "location-group":
          return LocationGroup;
        case "location":
          return Location;
        case "goods-unit":
          return GoodsUnit;
      }
    },
  }
}
</script>

<style scoped>
th.column-string,
td.column-string {
  text-align: left !important;
}

th.column-date,
td.column-date {
  text-align: center !important;
}

th.column-number,
td.column-number {
  text-align: right !important;
}

.ant-table-body {
  max-height: 50vh;
}

.wrapper-content {
  text-align: center;
}

.search-heading {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  flex: 1;
}

.heading {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;

}

.choose-type {
  flex: 1;
  display: flex;
}

.ant-table td {
  white-space: nowrap
}


</style>