<template>
  <div>
    <a-modal
      v-model="show"
      title="Chi tiết đơn hàng"
      width="80%"
      centered
      cancelText="Thoát"
      :bodyStyle="{
        padding: '16px',
        height: '80vh',
        overflowY: 'scroll',
        overflowX: 'scroll',
      }"
    >
      <template slot="footer">
        <a-button key="back" @click="hideModal"> Thoát </a-button>
      </template>
      <a-row>
        <a-col :span="24">
          <a-button-group v-if="form.status == 1">
            <a-button icon="check" @click="approve" :style="{ width: '180px' }"
              >Xác nhận</a-button
            >
            <a-button icon="stop" @click="cancel" :style="{ width: '180px' }">
              Huỷ
            </a-button>
            <a-button
              icon="exclamation"
              @click="requireEdit"
              :style="{ width: '180px' }"
            >
              Yêu cầu sửa đổi
            </a-button>
          </a-button-group>
          <a-button-group
            v-if="
              form.status == 2 || (form.status_goods == 1 && [5, 6, 7].includes(form.status))
            "
          >
            <a-button icon="check" @click="openExportModal" :disabled="disabled"
              >Xuất kho</a-button
            >
          </a-button-group>
          <a-divider dashed />
        </a-col>
      </a-row>
      <o-order :show="show" :form="form" />
    </a-modal>
    <a-modal
      :title="title"
      okText="Lưu"
      cancelText="Thoát"
      v-model="isShowModal"
      @ok="saveStatus"
    >
      <a-textarea v-model="data.note" placeholder="Nội dung" :rows="4" />
    </a-modal>
    <a-modal :title="title" v-model="isShowExportModal" width="750px">
      <template slot="footer">
        <a-button @click="() => (isShowExportModal = false)">Thoát</a-button>
        <a-button type="primary" @click="exportStore" :loading="loading">
          Lưu
        </a-button>
      </template>
      <div class="good-info-wrapper">
        <div class="ant-row ant-form-item">
          <div class="ant-col ant-col-6 ant-form-item-label">
            <label class="ant-form-item-required">Điểm nhận hàng</label>
          </div>
          <div class="ant-col ant-col-16 ant-form-item-control-wrapper">
            <o-select
              :value="exportForm.location_destination"
              @input="
                exportForm.location_destination_id = $event.key;
                exportForm.name_of_location_destination_id = $event.label;
              "
              placeholder="Vui lòng chọn điểm nhận hàng"
              entity="location"
            />
          </div>
        </div>

        <div class="ant-row ant-form-item">
          <div class="ant-col ant-col-6 ant-form-item-label">
            <label class="ant-form-item-required">Thời gian nhận hàng</label>
          </div>
          <div class="ant-col ant-col-16 ant-form-item-control-wrapper">
            <a-date-picker
              v-model="exportForm.ETD_date"
              :locale="locale"
              type="date"
              format="DD-MM-YYYY"
              :style="{ width: '50%' }"
            />
            <a-time-picker
              v-model="exportForm.ETD_time"
              :locale="locale"
              format="HH:mm"
              placeholder="Chọn giờ"
              :style="{ width: '45%', marginLeft: '8px' }"
            />
          </div>
        </div>
        <a-list
          bordered
          item-layout="horizontal"
          :data-source="form.list_goods"
          size="large"
          :style="{ width: '100%' }"
        >
          <a-empty v-if="form.list_goods && form.list_goods.length == 0">
            <span slot="description">Không có dữ liệu</span>
          </a-empty>
          <div slot="header" class="header">
            <div class="goods-avatar"></div>
            <div class="goods title">Hàng hóa</div>
            <div class="goods quantity">Số lượng</div>
            <div class="goods quantity_out">SL hàng đã xuất</div>
            <div class="goods quantity_remain">SL hàng xuất</div>
          </div>
          <a-list-item slot="renderItem" slot-scope="item" v-if="item.quantity > 0">
            <a-list-item-meta>
              <a-avatar
                slot="avatar"
                class="goods-avatar"
                :src="item.file_path"
              />
            </a-list-item-meta>
            <div class="goods title">
              <a class="goods-name">{{ item.name_of_goods_type_id }}</a>
              <div class="">{{ item.name_of_goods_unit_id }}</div>
            </div>
            <div class="goods quantity">{{ item.quantity }}</div>
            <div class="goods quantity_out">{{ item.quantity_out || 0 }}</div>
            <div class="goods quantity_remain">
              <a-input-number
                v-model="item.quantity_out_export"
                :style="{ width: '150px', marginRight: '8px' }"
                :max="item.quantity_out_export"
                :disabled="item.quantity === item.quantity_out"
              />
            </div>
          </a-list-item>
        </a-list>
      </div>
    </a-modal>
  </div>
</template>
<script>
import axios from "axios";
import moment from "moment/moment";

import Order from "@/components/Order/Show";
import constant from "@/constant";
import locale from "ant-design-vue/es/date-picker/locale/vi_VN";
import OneLogSelect from "@/components/Select/OneLogSelect";

export default {
  // mixins: [constant],
  components: {
    "o-order": Order,
    "o-select": OneLogSelect,
  },
  props: {
    show: Boolean,
    form: {},
    disabled: Boolean,
  },
  data() {
    return {
      locale,
      loading: false,
      isShowModal: false,
      isShowExportModal: false,
      isDisabled: this.disabled,
      title: "",
      data: {
        status: null,
        note: null,
      },
      exportForm: {
        location_destination: void 0,
        location_destination_id: null,
        name_of_location_destination_id: null,
        ETD_date: moment(),
        ETD_time: moment(),
      },
    };
  },
  mounted() {},
  methods: {
    hideModal() {
      this.$emit("hideModal");
    },
    onChangeSteps() {
      this.current = current;
      this.status = "process";
    },
    approve() {
      this.title = "Xác nhận đơn hàng";
      this.data.status = 2;
      this.data.note = "";
      this.saveStatus();
    },
    cancel() {
      this.title = "Huỷ đơn hàng";
      this.data.status = 3;
      this.data.note = "";
      this.isShowModal = true;
    },
    requireEdit() {
      this.title = "Yêu cầu sửa đổi đơn hàng";
      this.data.status = 4;
      this.data.note = "";
      this.isShowModal = true;
    },
    openExportModal() {
      this.loading = false;
      this.title = "Xuất kho";
      this.data.status = 5;
      this.data.note = "";

      this.exportForm.location_destination = void 0;
      this.exportForm.location_destination_id = null;
      this.exportForm.name_of_location_destination_id = null;
      
      if (this.form.location_destination_id) {
        this.exportForm.location_destination = {
          key: this.form.location_destination_id,
          label: this.form.name_of_location_destination_id,
        };

        this.exportForm.location_destination_id = this.form.location_destination_id;
        this.exportForm.name_of_location_destination_id = this.form.name_of_location_destination_id;
      }
      this.exportForm.ETD_date = moment();
      this.exportForm.ETD_time = moment();
      this.isShowExportModal = true;
    },
    saveStatus() {
      this.data.id = this.form.id;
      axios
        .post(`c-order-customer/save-status`, this.data)
        .then((response) => {
          this.form.status = this.data.status;
          this.isShowModal = false;
          this.$message.success(this.title + " thành công");
          this.$emit("saveStatus", this.data.status);
        })
        .catch((error) => {
          this.$message.error(error.message);
        });
    },
    exportStore() {
      if (
        this.form.list_goods.some(
          (p) => p.quantity_out + p.quantity_out_export > p.quantity
        )
      ) {
        this.disabled = false;
        this.$message.error(
          "Bạn không được phép xuất kho quá số lượng hàng hoá. Vui lòng kiểm tra lại"
        );
        return;
      }
      this.loading = true;

      let data = this.form.list_goods.map((p) => {
        return {
          id: p.id,
          goods_type_id: p.goods_type_id,
          quantity: p.quantity,
          quantity_out_export: p.quantity_out_export,
          quantity_out: p.quantity_out + p.quantity_out_export,
        };
      });
      let status =
        data.filter((p) => p.quantity != p.quantity_out).length > 0
          ? constant.goodsExportStatus.REMAIN
          : constant.goodsExportStatus.EMPTY;

      let param = {
        id: this.form.id,
        location_destination_id: this.exportForm.location_destination_id,
        ETD_date: this.exportForm.ETD_date.format("YYYY-MM-DD"),
        ETD_time: this.exportForm.ETD_time.format("H:mm"),
        status: status,
        data: data,
      };
      axios
        .post(`c-order-customer/export-store`, param)
        .then((response) => {
          if (response.data.errorCode != 0) {
            this.loading = false;
            this.$message.error(response.data.errorMessage);
            return;
          }

          this.form.status_goods = status;
          this.form.location_destination_id = this.exportForm.location_destination_id;
          this.form.name_of_location_destination_id = this.exportForm.name_of_location_destination_id;
          this.form.ETD_date = this.exportForm.ETD_date;
          this.form.ETD_time = this.exportForm.ETD_time.format("H:mm");
          this.form.list_goods.map((p) => {
            p.quantity_out = p.quantity_out + p.quantity_out_export;
            p.quantity_out_export = p.quantity - p.quantity_out;
            if(p.quantity === p.quantity_out){
              this.disabled = true;
            }
            else{
              this.disabled = false;
            }
          });

          if (this.form.list_goods.length === 0) {
            this.disabled = true;
          }

          this.isShowExportModal = false;

          this.$message.success(this.title + " thành công");
          this.$emit("saveStatus", 0);
        })
        .catch((error) => {
          this.$message.error(error.message);
          this.loading = false;

        });

    },

  },
};
</script>
<style scoped lang="scss">
.header {
  display: flex;
  font-weight: bold;
}
.goods {
  &.title {
    width: 250px;
  }
  &.quantity {
    width: 150px;
  }
  &.quantity_out {
    width: 150px;
  }
  &.quantity_remain {
    width: 150px;
  }
}
</style>