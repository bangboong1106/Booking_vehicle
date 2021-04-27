<template>
  <div>
    <a-table
        :scroll="{ x: 1500, y: this.maxHeight }"
        :columns="cols"
        :locale="{
        filterConfirm: 'Tìm kiếm',
        filterReset: 'Huỷ',
        emptyText: 'Không có dữ liệu',
      }"
        :row-key="(record) => record.id"
        :row-selection="{
        selectedRowKeys: selectedRowKeys,
        onChange: onSelectChange,
      }"
        :data-source="data"
        :loading="loading"
        :pagination="false"
        @change="handleTableChange"
    >


      <a
          slot="link"
          slot-scope="text, record"
          @click="() => openRecord(record)"
      >{{ text }}</a
      >
      <span slot="date" slot-scope="text">{{
          text == null ? "" : moment(String(text)).format("DD-MM-YYYY")
        }}</span>
      <span slot="datetime" slot-scope="text">{{
          text == null ? "" : moment(String(text)).format("DD-MM-YYYY HH:mm")
        }}</span>
      <span slot="time" slot-scope="text">{{ text }}</span>
      <span slot="number" slot-scope="text">{{ text }}</span>
      <a slot="email" :href="'mailto:' + text" slot-scope="text">
        <a-tooltip :title="text" placement="topLeft">
          {{
            text
          }}
        </a-tooltip>
      </a>
      <a slot="phone" :href="'tel:' + text" slot-scope="text">{{ text }}</a>
      <span slot="list" slot-scope="text, record, index, column">
        <a-tooltip :title="renderList(text,column)" placement="topLeft">
          {{
            renderList(text, column)
          }}
        </a-tooltip>
      </span>
      <span slot="text" slot-scope="text">
        <a-tooltip :title="text" placement="topLeft">
          {{
            text
          }}
        </a-tooltip>
      </span>
      <span slot="file" slot-scope="text">
        <img
            v-if="text"
            :src="text"
            alt="file"
            :style="{ width: '30px', height: '30px' }"
        /></span>
      <div
          slot="filterDate"
          slot-scope="{
          setSelectedKeys,
          selectedKeys,
          confirm,
          clearFilters,
          column,
        }"
          style="padding: 8px"
      >
        <a-date-picker
            v-ant-ref="(c) => (searchInput = c)"
            :locale="locale"
            :value="selectedKeys[0]"
            @change="
            (e) => {
              setSelectedKeys(e ? [e] : []);
            }
          "
            type="date"
            format="DD-MM-YYYY"
        />
        <a-button
            type="primary"
            icon="search"
            size="small"
            style="width: 120px; margin-right: 8px"
            @click="
            () => handleSearchDate(selectedKeys, confirm, column.dataIndex)
          "
        >
          Tìm kiếm
        </a-button>
        <a-button
            size="small"
            style="width: 120px"
            @click="
            () => handleReset(selectedKeys, clearFilters, column.dataIndex)
          "
        >
          Huỷ
        </a-button>
      </div>
      <div
          slot="filterDropdown"
          slot-scope="{
          setSelectedKeys,
          selectedKeys,
          confirm,
          clearFilters,
          column,
        }"
          style="padding: 8px"
      >
        <a-input
            v-ant-ref="(c) => (searchInput = c)"
            :placeholder="`Tìm kiếm`"
            :value="selectedKeys[0]"
            style="width: 250px; margin-bottom: 8px; display: block"
            @change="
            (e) => setSelectedKeys(e.target.value ? [e.target.value] : [])
          "
            @pressEnter="
            () => handleSearch(selectedKeys, confirm, column.dataIndex)
          "
        />
        <a-button
            type="primary"
            icon="search"
            size="small"
            style="width: 120px; margin-right: 8px"
            @click="() => handleSearch(selectedKeys, confirm, column.dataIndex)"
        >
          Tìm kiếm
        </a-button>
        <a-button
            size="small"
            style="width: 120px"
            @click="
            () => handleReset(selectedKeys, clearFilters, column.dataIndex)
          "
        >
          Huỷ
        </a-button>
      </div>
      <a-icon
          slot="filterIcon"
          slot-scope="filtered"
          type="search"
          :style="{ color: filtered ? '#108ee9' : undefined }"
      />
      <template slot="action" slot-scope="text, record">
        <a-icon
            type="edit"
            :style="{ color: 'green', marginRight: '8px' }"
            @click="() => showEdit(record)"
        />
        <a-icon
            type="delete"
            :style="{ color: 'red', marginRight: '8px' }"
            @click="() => showConfirm(record)"
        />
      </template>
      <template slot="renderAction" slot-scope="text, record, index, column">
        <a-icon
            type="edit"
            :style="{ color: 'green', marginRight: '8px' }"
            @click="() => showEdit(record)"
            v-if="
            column.isEditByUser &&
            record.ins_id == userId &&
            column.actions
              .find((p) => p.type === 'edit')
              .status.includes(record[column.statusField])
          "
        />
        <a-icon
            type="delete"
            :style="{ color: 'red', marginRight: '8px' }"
            @click="() => showConfirm(record)"
            v-if="
            column.isEditByUser &&
            record.ins_id == userId &&
            column.actions
              .find((p) => p.type === 'delete')
              .status.includes(record[column.statusField])
          "
        />
      </template>
      <template slot="customRender" slot-scope="text, record, index, column">
        <span v-if="searchText && searchedColumn === column.dataIndex">
          <template
              v-for="(fragment, i) in text
              .toString()
              .split(new RegExp(`(?<=${searchText})|(?=${searchText})`, 'i'))"
          >
            <mark
                v-if="fragment.toLowerCase() === searchText.toLowerCase()"
                :key="i"
                class="highlight"
            >{{ fragment }}</mark
            >
            <template v-else>{{ fragment }}</template>
          </template>
        </span>
        <template v-else>
          {{ text }}
        </template>
      </template>

    </a-table>
    <div class="pagination" v-if="this.data.length">
      <a-pagination
          show-size-changer
          v-model="pagination.current"
          :showLessItems="pagination.showLessItems"
          :showTitle="pagination.showTitle"
          :size="pagination.size"
          :hideOnSinglePage="pagination.hideOnSinglePage"
          :page-size-options="pagination.pageSizeOptions"
          :total="pagination.total"
          :showTotal="(total, range) => `${range[0]}-${range[1]} / ${total} bản ghi `"
          :page-size="pagination.pageSize"
          @showSizeChange="onShowSizeChange"
          @change="onShowSizeChange"

      >
        <template slot="buildOptionText" slot-scope="props">
          <span>{{ props.value }} / trang</span>
        </template>
      </a-pagination>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import mixins from "@/mixins";
import moment from "moment/moment";
import locale from "ant-design-vue/es/date-picker/locale/vi_VN";

export default {
  mixins: [mixins],
  props: {
    title: {
      type: String,
      default: "",
    },
    entity: {
      type: String,
      default: "",
    },
    columns: {
      type: Array,
      default: [],
    },
    isAction: {
      type: Boolean,
      default: true,
    },
    isPopup: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    let height = Math.max(
        document.documentElement.clientHeight || 0,
        window.innerHeight || 0
    );
    let maxHeight = this.isPopup
        ? (height * 55) / 100
        : window.innerHeight - 240;
    let cols = [];

    if (this.isAction) {
      cols.push({
        title: "Hành động",
        key: "operation",
        fixed: "left",
        width: 110,
        scopedSlots: {customRender: "action"},
      });
    }
    var tmp = this.columns.map((p) => {
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
          if (p.dataType == "date" || p.dataType == "datetime") {
            p.scopedSlots.filterDropdown = "filterDate";
          } else {
            p.scopedSlots.filterDropdown = "filterDropdown";
          }
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
        p.onFilter = (value, record) => {
          return true;
          // if (value instanceof moment) {
          //   value = value.format("DD-MM-YYYY");
          // }
          // return record[p.dataIndex]
          //   .toString()
          //   .toLowerCase()
          //   .includes(value.toLowerCase());
        };

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
    return {
      locale,
      maxHeight,
      moment,
      data: [],
      pagination: {
        showLessItems: true,
        size: "small",
        showTitle: false,
        hideOnSinglePage: false,
        showSizeChanger: true,
        pageSize: 50,
        pageSizeOptions: ["10", "20", "50", "100"],
        showTotal: (total, range) =>
            `${range[0]}-${range[1]} / ${total} bản ghi`,

      },
      loading: false,
      cols: cols,
      selectedRowKeys: [],
      filters: [],
      searchText: "",
      isFiltering: false,
      userId: this.$auth.user().id,
    };
  },
  computed: {
    hasSelected() {
      return this.selectedRowKeys.length > 0;
    },
  },
  mounted() {
    this.pagination.current = 1;
    this.fetch({
      subFilters: [],
      filters: [],
    });
  },
  methods: {
    onShowSizeChange(current, pageSize) {
      this.pagination.pageSize = pageSize;
      this.handleTableChange({current, pageSize}, [], [])
    },
    actionRecord(record, action) {
      if (action.type == "edit") {
        this.showEdit(record);
      } else if (action.type == "delete") {
        this.showConfirm(record);
      } else {
        this.$emit("emitRecord", {
          action: action.type,
          record: record,
        });
      }
    },
    renderList(text, column) {
      let item = column.filters.find((p) => p.value == text);
      return item ? item.text : "";
    },
    handleTableChange(pagination, filters, sorter) {
      if (this.isFiltering) return;

      for (let prop in filters) {
        this.filters = this.filters.filter((p) => p.field != prop);
        if (filters[prop]) {
          if (Array.isArray(filters[prop])) {
            if (filters[prop].length > 0) {
              this.filters.push({
                field: prop,
                operator: "in",
                value: filters[prop],
              });
            }
          } else {
            this.filters.push({
              field: prop,
              operator: "like",
              value: filters[prop],
            });
          }
        }
      }
      const pager = Object.assign({}, this.pagination);
      pager.current = typeof pagination.current !== 'undefined' ? pagination.current : this.pagination.current;
      pager.pageSize = typeof pagination.pageSize !== 'undefined' ? pagination.pageSize: this.pagination.pageSize;

      this.pagination = pager;

      this.fetch({
        sortField: sorter.field,
        sortOrder: sorter.order,
        subFilters: this.subFilters,
        filters: this.filters,
      });
    },
    fetch(params = {}) {
      this.loading = true;
      const pa = JSON.parse(JSON.stringify(params));
      pa.pageSize = this.pagination.pageSize;
      pa.pageIndex = this.pagination.current;

      pa.filters = pa.filters.map((p) => {
        let field = this.columns.find((x) => x.dataIndex === p.field);
        if (field) {
          p.field = field.originalIndex || field.dataIndex;
        }
        return p;
      });
      if (pa.subFilters) {
        pa.subFilters = pa.subFilters.map((p) => {
          let field = this.columns.find((x) => x.dataIndex === p.field);
          if (field) {
            p.field = field.originalIndex || field.dataIndex;
          }
          return p;
        });
      }
      axios
          .post(`c-${this.entity}/list`, pa)
          .then((response) => {
            const pagination = Object.assign({}, this.pagination);
            pagination.total = response.data.data.totalCount;

            this.loading = false;
            this.data = response.data.data.items;
            this.pagination = pagination;
            this.isFiltering = false;
          })
          .catch((error) => {
            this.loading = false;
            this.$message.error(error.message);
          });
    },
    reload() {
      this.pagination.current = 1;
      this.fetch({
        filters: this.filters,
        subFilters: this.subFilters,
      });
      this.selectedRowKeys = [];
      this.$emit("showAdvance", this.selectedRowKeys);
    },
    handleSearch(selectedKeys, confirm, dataIndex) {
      confirm();
      this.searchText = selectedKeys[0];
      this.searchedColumn = dataIndex;
      this.isFiltering = true;
      this.filters = this.filters.filter((p) => p.field != dataIndex);

      this.filters.push({
        field: dataIndex,
        value: selectedKeys[0],
      });
      this.reload();
    },
    handleSearchDate(selectedKeys, confirm, dataIndex) {
      confirm();
      this.searchText = selectedKeys[0].format("DD-MM-YYYY");
      this.searchedColumn = dataIndex;
      this.isFiltering = true;
      this.filters = this.filters.filter((p) => p.field != dataIndex);

      this.filters.push({
        field: dataIndex,
        operator: "equal",
        value: selectedKeys[0].format("YYYY-MM-DD"),
      });
      this.reload();
    },
    handleReset(selectedKeys, clearFilters, dataIndex) {
      clearFilters();
      this.searchText = "";
      this.filters = this.filters.filter((p) => p.field != dataIndex);
      this.reload();
    },
    onSelectChange(selectedRowKeys) {
      this.selectedRowKeys = selectedRowKeys;
      this.$emit("showAdvance", selectedRowKeys);
    },
    openRecord(record) {
      this.$emit("openRecord", record);
    },
    showEdit(record) {
      this.$emit("recordEdit", record);
    },
    showConfirm(record) {
      let name = record[this.cols.find((p) => p.isNameIndex).dataIndex];
      let entity = this.entity;
      let component = this;
      this.$confirm({
        title: "Bạn chắc chắn muốn xoá bản ghi " + name + "?",
        content: "",
        okText: "Xác nhận",
        cancelText: "Huỷ",
        onOk() {
          axios
              .delete(`c-${entity}?id=${record.id}`)
              .then((response) => {
                if (response.data.errorCode == 0) {
                  component.$message.success(`Xoá ${component.title} thành công`);
                  component.reload();
                } else {
                  component.$message.error(response.data.errorMessage);
                }
              })
              .catch((error) => {
                component.$message.error(error.message);
              });
        },
        onCancel() {
        },
      });
    },
  },
};
</script>
<style>
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

.pagination {
  display: flex;
  justify-content: flex-end;
  margin-top: 12px;
}
</style>