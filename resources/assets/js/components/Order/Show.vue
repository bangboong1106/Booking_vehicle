<template>
  <div>
    <a-row>
      <a-col :span="form.status == 1 ? 24 : 15">
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
                  form.order_date
                      ? moment(String(form.order_date)).format("DD-MM-YYYY")
                      : ""
                }}
              </a-descriptions-item>
              <a-descriptions-item label="Khách hàng">
                {{ form.name_of_client_id }}
              </a-descriptions-item>
              <a-descriptions-item
                  label="Điểm nhận hàng"
                  v-if="form.name_of_location_destination_id"
              >
                {{ form.name_of_location_destination_id }}
              </a-descriptions-item>
              <a-descriptions-item
                  label="Ngày giờ nhận hàng"
                  v-if="form.name_of_location_destination_id"
              >
                {{
                  form.ETD_date
                      ? moment(String(form.ETD_date)).format("DD-MM-YYYY")
                      : ""
                }}
                {{ form.ETD_time ? form.ETD_time : "" }}
              </a-descriptions-item>
              <a-descriptions-item label="Điểm trả hàng">
                {{ form.name_of_location_arrival_id }}
              </a-descriptions-item>
              <a-descriptions-item label="Ngày giờ trả hàng">
                {{
                  form.ETA_date
                      ? moment(String(form.ETA_date)).format("DD-MM-YYYY")
                      : ""
                }}
                {{ form.ETA_time ? form.ETA_time : "" }}
              </a-descriptions-item>
              <a-descriptions-item label="Cước phí vận chuyển">
                {{
                  form.amount  ? Intl.NumberFormat().format(Number.parseFloat(form.amount).toFixed(0))
                      : ""
                }}
              </a-descriptions-item>
              <a-descriptions-item label="Giá trị hàng hoá">
                {{
                  form.goods_amount ? Intl.NumberFormat().format(Number.parseFloat(form.goods_amount).toFixed(0))
                      : ""
                }}
              </a-descriptions-item>
              <a-descriptions-item label="Tổng khối lượng (kg)">
                {{
                  form.weight ? Intl.NumberFormat().format(Number.parseFloat(form.weight).toFixed(0))
                      : ""
                }}
              </a-descriptions-item>
              <a-descriptions-item label="Tổng thể tích (m3)">
                {{
                  form.volume ? Intl.NumberFormat().format(Number.parseFloat(form.volume).toFixed(0))
                      : ""
                }}
              </a-descriptions-item>
            </a-descriptions>
          </div>
        </div>
        <div class="good-info">
          <div class="ant-descriptions-title">Thông tin hàng hóa</div>
          <div class="good-info-wrapper">
            <a-list
                item-layout="horizontal"
                :data-source="form.list_goods"
                size="small"
            >
              <a-empty v-if="form.list_goods && totalGoodsQuantity == 0">
                <span slot="description">Không có dữ liệu</span>
              </a-empty>
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
                  v-if="totalGoodsQuantity > 0"
              >
                <a-list-item-meta v-if="item.quantity">
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
                    <div class="goods quantity">
                      {{ formatNumber(item.quantity) }}
                    </div>
                    <div class="goods number">
                      {{ formatNumber(item.total_weight) }}
                    </div>
                    <div class="goods number">
                      {{ formatNumber(item.total_volume) }}
                    </div>
                  </div>
                </a-list-item-meta>
              </a-list-item>
            </a-list>
          </div>
        </div>
      </a-col>
      <a-col :span="1"></a-col>
      <a-col :span="form.status == 1 ? 0 : 8" v-if="form.status != 1">
        <a-row>
          <div class="transport-info">
            <div class="ant-descriptions-title">Tình trạng đơn hàng</div>
          </div>
          <a-list>
            <a-list-item>
              <div class="transport-info-wrapper">
                <a-steps
                    size="small"
                    style="font-size: 12px"
                    :current="form.histories.length"
                >
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
                      :title="renderOrderCustomerStatus(history)"
                      v-for="(history, index) in form.histories"
                      :key="`history-${index}`"
                  />
                </a-steps>
              </div>
              <div class="quanity-transport">
                <span></span>
              </div>
            </a-list-item>
          </a-list>
          <div class="transport-info">
            <div class="ant-descriptions-title">Trạng thái vận chuyển</div>
          </div>
          <a-list
              item-layout="vertical"
              :data-source="transportations"
              size="small"
          >
            <a-empty v-if="transportations.length == 0">
              <span slot="description">Không có dữ liệu</span>
            </a-empty>
            <a-list-item slot="renderItem" slot-scope="item">
              <div class="transport-info-wrapper">
                <a-steps
                    size="small"
                    style="font-size: 12px"
                    :current="form.histories.length"
                    direction="vertical"
                >
                  <a-popover title="Thông tin xe">
                    <template slot="content">
                      <a-descriptions title="Thông tin chung">
                        <a-descriptions-item label="Xe">
                          {{ item.name_of_vehicle_id }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Tài xế">
                          {{ item.name_of_primay_driver_id }}
                        </a-descriptions-item>
                      </a-descriptions>
                    </template>
                    <a-icon type="car"/>
                    <br/>
                    <span
                    ><strong> {{ item.name_of_vehicle_id }}</strong></span
                    >
                    <span> {{ item.name_of_primay_driver_id }}</span>
                  </a-popover>
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
                      v-for="(history, index) in item.histories"
                      :key="`history-${index}`"
                  />
                </a-steps>
              </div>
              <div class="quanity-transport">
                <span></span>
              </div>
            </a-list-item>
          </a-list>
        </a-row>
      </a-col>
    </a-row>
  </div>
</template>
<script>
import axios from "axios";
import constant from "@/constant";
import utility from "@/utility";
import moment from "moment/moment";

export default {
  mixins: [utility],
  props: {
    show: false,
    form: {},
  },
  data() {
    return {
      moment,
      current: null,
      status: "",
      transportations: [],
      orderStatus: constant.orderStatus,
      orderCustomerStatus: constant.orderCustomerStatus,
      title: "",
      data: {
        status: null,
        note: null,
      },
    };
  },
  mounted() {
    if (this.show) {
      this.transportations = [];
      axios
          .get(`c-order-customer/order?id=${this.form.id}`)
          .then((response) => {
            this.transportations = response.data.data;
          })
          .catch((error) => {
            this.$message.error(error.message);
          });
    }
  },
  computed: {
    totalGoodsQuantity: function() {
      return this.form.list_goods.reduce((currentTotal, item) => {
        return parseFloat(item.quantity) + currentTotal;
      }, 0);
    }
  },
  watch: {
    show: function (val) {
      if (val) {
        this.transportations = [];
        axios
            .get(`c-order-customer/order?id=${this.form.id}`)
            .then((response) => {
              this.transportations = response.data.data;
            })
            .catch((error) => {
              this.$message.error(error.message);
            });
      }
    },
  },
  methods: {
    renderOrderCustomerStatus(history) {
      return this.orderCustomerStatus.find((p) => p.value == history.status)
          .text;
    },
    renderStep(history) {
      return this.orderStatus.find((p) => p.value == history.order_status).text;
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

.ant-steps-item-content {
  min-height: 70px;
}

.ant-steps-item-description {
  font-size: 12px;
}

.transport-info-wrapper {
  margin-top: 0;
}

.transport-info {
  margin-bottom: 30px;
}

.ant-steps-item-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  font-size: 10px;
}

.ant-steps-small .ant-steps-item-title {
  font-size: 12px;
}

i.anticon.anticon-car {
  font-size: 20px;
}
</style>
