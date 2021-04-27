<template>
  <div class="select">
    <a-select
        show-search
        label-in-value
        :mode="mode"
        :allowClear="true"

        :autoClearSearchValue="true"
        :value="val"
        :disabled="disabled"
        @input="changeModel()"
        :placeholder="placeholder"
        style="width: 100% ; border-left: none"
        :filter-option="false"
        :not-found-content="fetching ? undefined : null"
        @search="fetchData"
        @change="handleChange"
        @popupScroll="onScroll"
        @focus="onFocus"

    >
      <a-spin v-if="fetching" slot="notFoundContent" size="small"/>
      <a-icon slot="clearIcon" type="close" @click="clearSearchValue"/>
      <a-select-option v-for="d in data" :key="d.value">
        {{ d.text }}
      </a-select-option>
    </a-select>


    <a-button
        :disabled="disabled"
        @click="show()"
        :style="`display:${this.isDisplay};
                border-left:none;
                margin-left:-5px;
                border-top-left-radius:0;
                border-bottom-left-radius:0`">
      <a-icon :type="icon"/>
    </a-button>

    <a-modal
        width="80%"
        v-model="visible"
        :title="title"
        cancelText="Đóng"
        okText="Chọn"
        :ok-button-props="{ props: { disabled: isDisabled } }"
        centered
        @ok="handleOk"
        :bodyStyle="{
          padding: '16px',
          height: '80vh',
          overflow:'hidden'
        }"
    >
      <o-list
          :key="keyLoad"
          :loadingProp="loading"
          :placeholder="placeholder"
          :textField="textField"
          @onSelectChange="onSelectChange"
          :entity="entity"
          :isAddSelect = "isAddSelect"
      ></o-list>
    </a-modal>
  </div>
</template>
<script>
import axios from "axios";
import List from "@/components/Select/List";
import debounce from 'lodash/debounce';
import EventBus from "@/event-bus";

export default {
  props: {
    isAddSelect :{
      type:Boolean,
      default:false
    },
    mode: {
      type: String,
      default: "default",
    },
    placeholder: {
      type: String,
      default: "",
    },
    value: void 0,
    valueField: {
      type: String,
      default: "id",
    },
    textField: {
      type: String,
      default: "title",
    },
    entity: {
      type: String,
      default: "",
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: 'Danh sách'
    },
    icon: {
      type: String,
      default: "ellipsis"
    },
    isDisplay: {
      type: String,
      default: ''
    },
    isDisplayAdd: {
      type: String,
      default: ''
    }
  },
  components: {
    "o-list": List
  },
  created() {
    EventBus.$on('reload',this.reload)
  },
  destroyed() {
    EventBus.$off('reload',this.reload)

  },
  data() {
    this.fetchData = debounce(this.fetchData, 800);
    return {
      data: [],
      loading: false,
      visible: false,
      isDisabled: true,
      fetching: false,
      isKeepFilter: false,
      isFirstFocus: true,
      params: {
        pageSize: 20,
        pageIndex: 1,
        filters: [],
      },
      val: this.value,
      isEnd: false,
      items: [],
      valueProp: {},
      isHaveDatas: [],
      keyLoad : 0

    };

  },
  watch: {
    value: function (val) {
      this.val = val;
    },
  },
  methods: {
    reload(){
      this.keyLoad ++;
    },
    clearSearchValue(){
      this.params.filters = [];
      this.data = [];
    },

    onSelectChange(data) {
      this.valueProp = {label: data.title, key: data.index, data: data.data};
      this.isHaveDatas = data.data;
      this.isDisabled = data.disabled;
    },
    handleOk(e) {

      if (this.isHaveDatas.length > 0) {
        this.val = this.valueProp;
        this.$emit("input", this.val);
        this.visible = false;
      } else {
        this.$message.error('Chưa chọn dữ liệu');
      }

    },
    show() {
      this.visible = true;
      this.loading = true;
    }
    ,
    onFocus(e) {
      if(this.params.filters.length == 0){
        if (this.isFirstFocus) {
            this.isFirstFocus = false;
            this.fetchData();
        }
        else{
          this.isFirstFocus = true;
          if(this.data.length == 0){
            this.fetchData();
          }

        }
      }



    },
    changeModel() {
      this.$emit("input", this.val);
    },
    onLoad({field, value}) {
      this.data = [];
      this.isKeepFilter = true;
      this.isFirstFocus = false;
      this.isEnd = false;
      this.params.pageIndex = 1;

      if (typeof value == "undefined") {
        this.params.filters = this.params.filters.filter(
            (p) => p.field != field
        );
      } else {
        this.params.filters = this.params.filters.filter(
            (p) => p.field != field
        );
        this.params.filters.push({
          field: field,
          operator: "=",
          value: value,
        });
        this.fetchData();
      }
    },
    onScroll(e) {
      var target = e.target;
      if (this.isEnd) return;
      if (target.scrollTop + target.offsetHeight === target.scrollHeight) {
        this.params.pageIndex++;
        this.isKeepFilter = true;
        this.fetchData();
      }
    },
    fetchData(value) {
      if (!this.isKeepFilter) {
        this.data = [];
      }
      this.fetching = true;
      if (!this.isKeepFilter) {
        this.params.filters = [];
        if (value) {
          this.params.filters.push({
            field: this.textField,
            value: value,
          });
        }
      }

      axios
          .post(`c-${this.entity}/list`, this.params)
          .then((response) => {
            if (this.params.pageIndex == response.data.data.totalPage) {
              this.isEnd = true;
            }
            this.items = response.data.data.items;
            const results = this.items.map((item) => ({
              text: item[this.textField],
              value: item[this.valueField],
            }));
            this.data = this.data.concat(results);
            this.fetching = false;
            this.isKeepFilter = false;
          })
          .catch((error) => {
            this.isKeepFilter = false;
            this.fetching = false;
            this.$message.error(error.message);
          });
    },
    handleChange(value) {
      Object.assign(this, {
        val: value,
        fetching: false,
      });
      this.$emit("input", this.val);
    },

    handleAfterDelete(value) {
      let data = this.data;
      let item = data.find( record => record.value === value.goods_type_id);

      this.val = {
        label: item.text,
        key: item.value
      };
    }
  },
};
</script>
<style lang="scss">
.select {
  display: flex;
  position: relative;
}

</style>