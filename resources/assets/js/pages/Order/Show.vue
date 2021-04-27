<template>
  <a-modal
      v-model="show"
      title="Chi tiết đơn hàng"
      :width="width"
      centered
      cancelText="Thoát"
      :bodyStyle="{
      padding: '16px',
      height: '80vh',
      overflowY: 'scroll',
      overflowX: 'hidden',
    }"
  >
    <template slot="footer">
      <a-button key="back" @click="hideModal">Thoát</a-button>
    </template>
    <a-row>
      <a-col :span="15">
        <div class="order-info">
          <div class="order-info-wrapper">
            <a-descriptions
                title="Thông tin chung"
                layout="horizontal"
                :column="2"
            >
              <a-descriptions-item label="Mã đơn hàng">
                {{ form.code }}
              </a-descriptions-item>
              <a-descriptions-item label="Số đơn hàng">
                {{ form.order_no }}
              </a-descriptions-item>
              <a-descriptions-item label="Ngày đặt hàng">
                {{
                  form.order_date ? moment(String(form.ETD_date)).format("DD-MM-YYYY")
                      : ""
                }}
              </a-descriptions-item>
              <a-descriptions-item label="Khách hàng">
                {{ form.name_of_client_id }}
              </a-descriptions-item>
              <a-descriptions-item label="Xe">
                {{ form.name_of_vehicle_id }}
              </a-descriptions-item>
              <a-descriptions-item label="Tài xế">
                {{ form.name_of_primary_driver_id }}
              </a-descriptions-item>
              <a-descriptions-item label="Điểm nhận hàng">
                {{ form.name_of_location_destination_id }}
              </a-descriptions-item>
              <a-descriptions-item label="Ngày nhận hàng">
                {{
                  form.ETD_date
                      ? moment(String(form.ETD_date)).format("DD-MM-YYYY")
                      : ""
                }} {{ form.ETD_time }}
              </a-descriptions-item>
              <a-descriptions-item label="Điểm trả hàng">
                {{ form.name_of_location_arrival_id }}
              </a-descriptions-item>
              <a-descriptions-item label="Ngày trả hàng">
                {{
                  form.ETA_date
                      ? moment(String(form.ETD_date)).format("DD-MM-YYYY")
                      : ""
                }} {{ form.ETA_time }}
              </a-descriptions-item>
            </a-descriptions>
          </div>
        </div>
        <div class="good-info">
          <div class="ant-descriptions-title">Thông tin hàng hóa</div>
          <a-empty v-if="form.list_goods && form.list_goods.length == 0">
            <span slot="description">Không có dữ liệu</span>
          </a-empty>
          <div v-else class="good-info-wrapper">
            <a-list
                bordered
                item-layout="horizontal"
                :data-source="form.list_goods"
                size="small"
            >

              <div slot="header" class="goods-header">
                <span class="goods avatar"></span>
                <span class="goods title">Hàng hóa</span>
                <span class="goods quantity">Số lượng</span>
                <span class="goods number">Tổng khối lượng</span>
                <span class="goods number">Tổng thể tích</span>
              </div>
              <a-list-item
                  slot="renderItem"
                  slot-scope="item"
                  class="goods-body"
              >
                <a-list-item-meta>
                  <a-avatar
                      slot="avatar"
                      class="goods-avatar"
                      :src="item.file_path"
                  />
                  <div class="goods row" slot="title">
                    <div class="goods title">
                      <a class="goods-name">{{ item.name_of_goods_type_id }}</a>
                      <div class="">{{ item.name_of_goods_unit_id }}</div>
                    </div>
                    <div class="goods quantity">{{ item.quantity }}</div>
                    <div class="goods number">{{
                        item.total_weight ? Intl.NumberFormat().format(Number.parseFloat(item.total_weight).toFixed(0))
                            : ""
                      }}
                    </div>
                    <div class="goods number">{{
                        item.total_volume ? Intl.NumberFormat().format(Number.parseFloat(item.total_volume).toFixed(0))
                            : ""
                      }}
                    </div>
                  </div>
                </a-list-item-meta>
              </a-list-item>
            </a-list>
          </div>
        </div>
      </a-col>
      <a-col :span="1"></a-col>
      <a-col :span="8">
        <a-row>
          <div class="transport-info">
            <div class="ant-descriptions-title">Trạng thái vận chuyển</div>
          </div>
          <div class="transport-info-wrapper">
            <a-steps size="small" style="font-size: 12px" direction="vertical">
              <a-popover
                  slot="progressDot"
                  slot-scope="{ index, status, title, prefixCls }"
              >
                <template slot="content">
                  <span>Trạng thái: {{ title }}</span>
                </template>
                <span :class="`${prefixCls}-icon-dot`"/>
              </a-popover>
              <a-step
                  :title="renderStep(history)"
                  v-for="(history, index) in histories"
                  :key="`history-${index}`"
              />
            </a-steps>
          </div>
        </a-row>
      </a-col>
    </a-row>
  </a-modal>
</template>
<script>
import axios from "axios";
import constant from "@/constant";
import moment from "moment/moment";

export default {
  // mixins: [constant],
  props: {
    show: false,
    form: {},
  },
  data() {
    return {
      moment,
      current: null,
      btnZoomName: "Phóng to",
      status: "",
      width: "80%",
      height: "80%",
      histories: [],
      orderStatus: constant.orderStatus,
    };
  },
  mounted() {
  },
  watch: {
    show: function (val) {
      if (val) {
        axios
            .get(`c-order/history?id=${this.form.id}`)
            .then((response) => {
              this.histories = response.data.data;
            })
            .catch((error) => {
              this.$message.error(error.message);
            });
      }
    },
  },
  methods: {
    hideModal() {
      this.$emit('hideModal')
    },
    renderStep(history) {
      return this.orderStatus.find((p) => p.value == history.order_status).text;
    },
    handleZoom(e) {
      if (this.width == "80%") {
        this.width = "100%";
        this.height = "100vh";
        this.btnZoomName = "Thu nhỏ";
      } else {
        this.btnZoomName = "Phóng to";
        this.width = "80%";
      }
    },
    onChangeSteps() {
      this.current = current;
      this.status = "process";
    },
  },
};
</script>
<style scoped lang="scss">
.good-info-wrapper {
  .ant-list .ant-list-header {
    padding-bottom: 0px;
  }
}

.goods-header {
  display: flex;
  justify-content: space-between;
  background: #fafafa;
  padding: 8px;

  .goods {
    text-align: center;
    font-weight: bold;
  }
}

.goods-body {
  .goods.quantity {
    background: #fafafa;
    border: 1px solid #fafafa;
  }
}

.goods {
  &.row {
    display: flex;
    flex: 1;
  }

  &.title {
    width: 40%;
  }

  &.quantity {
    width: 20%;

    border-radius: 4px;
    text-align: center;
  }

  &.number {
    width: 20%;
    text-align: right;
  }
}
</style>
