<template>
  <div class="select">
    <a-select
        style="width: 100%"
        :placeholder="textHolder"
        :value="val"
        show-search
        label-in-value
        :allowClear="true"
        :autoClearSearchValue="true"
        :filter-option="false"
        :not-found-content="fetching ? undefined : null"
        @search="fetchData"
        @focus="onFocus"
        @change="handleChange"
    >

      <a-spin v-if="fetching" slot="notFoundContent" size="small"/>
      <a-icon slot="clearIcon" type="close" @click="clearSearchValue()"/>
      <a-select-option v-for="(d,index) in listLocations" :key="index" >
        {{ d.title }}
      </a-select-option>
    </a-select>

  </div>
</template>

<script>
import axios from "axios";
import debounce from "lodash/debounce";

export default {
    props: {
    textHolder: String,
    icon: String,
    isDisplay: Boolean,
    selectValue: {
      type:Object,
      default :() => {}
    }
  },
  data() {
    this.fetchData = debounce(this.fetchData, 800);
    return {
      listLocations: [],
      fetching: false,
      params: {
        pageSize: 20,
        pageIndex: 1,
        filters: [],
      },
      val: {}
    }
  },
  mounted() {
    this.onSelected()
  },
  methods: {
    fetchData() {
      this.fetching = true;
      axios
          .post('na/c-location/list', this.params)
          .then((response) => {
            if (response.data.errorCode != 0) {
              this.$message.error(response.data.errorMessage.map((p) => p.errorMessage).join("\n"))
            } else {
              this.listLocations = response.data.data.items;
              this.fetching = false;
            }

          })
          .catch((error) => {
            this.fetching = false;
            this.$message.error(error.message);
          });
    },
    onFocus() {
      if (this.params.filters.length == 0) {
        this.fetchData()
      } else {
        this.fetchData()
      }

    },
    clearSearchValue() {
      this.params.filters = [];
      this.listLocations = [];
    },
    handleChange(value) {
      this.value = value;
      this.val = value;
      this.$emit('setLocationValue', this.value);
    },
    onSelected() {
      if (JSON.stringify(this.selectValue) != '{}') {
        this.val = this.selectValue;
      } else {
        this.val = {};
      }
    }
  },


}
</script>

<style scoped>
.select {
  display: flex;
  position: relative;
}
</style>