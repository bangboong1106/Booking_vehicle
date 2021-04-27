<template>
  <div>
    <o-list
      :entity="entity"
      :isAction="false"
      ref="list"
      @openRecord="openRecord($event)"
      @emitRecord="emitRecord($event)"
      :widthModal="width"
      :bodyStyleModal="bodyStyle"
      :supportRole="supportRole"
    ></o-list>
    <o-show :show="show" :disabled="disabled" :form="detail" @saveStatus="saveStatus($event)" @hideModal="hideModal"/>
  </div>
</template>
<script>
import BaseList from "@/pages/BasePage/BaseList";
import Show from "@/pages/OrderCustomer/Show";
import constant from "@/constant";
import axios from "axios";
const entity = "order-customer";

export default {
  mixins: [constant],
  components: {
    "o-list": BaseList,
    "o-show": Show,
  },
  data() {
    return {
      entity,
      show: false,
      disabled : false,
      detail: {},
      width: "800px",
      bodyStyle: {
        padding: "16px",
        width: "800px",
        height: "70vh",
        overflowY: "scroll",
        overflowX: "hidden",
      },
      supportRole: [
        constant.customerType.CUSTOMER,
        constant.customerType.STAFF,
      ],
    };
  },
  mounted() {},
  methods: {
    emitRecord($event) {
      this.$message.error($event.action);
    },
    hideModal(){
      this.show = false;
    },
    openRecord(record) {
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
          this.detail = response.data.data;
          this.show = true;
          this.disabled = false;
        })
        .catch((error) => {
          this.$message.error(error.message);
        });
    },
    saveStatus($event) {
      if (this.$refs.list) {
        this.$refs.list.reload();
      }
    },
  },
};
</script>
