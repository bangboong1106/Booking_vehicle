<template>
  <div>
    <o-list
      :entity="entity"
      :isAction="false"
      @openRecord="openRecord($event)"
      :widthModal="width"
      :bodyStyleModal="bodyStyle"
      :supportRole="supportRole"
    ></o-list>
    <o-show :show="show" :form="detail" @hideModal ="hideModal"/>
  </div>
</template>
<script>
import BaseList from "@/pages/BasePage/BaseList";
import Show from "@/pages/OrderClient/Show";
import constant from "@/constant";
import axios from "axios";

const entity = "order-client";

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
      detail: {},
      width: "800px",
      bodyStyle: {
        padding: "16px",
        width: "800px",
        height: "70vh",
        overflowY: "scroll",
        overflowX: "hidden",
      },
      supportRole: [constant.customerType.CLIENT],
    };
  },
  mounted() {},
  methods: {
    hideModal(){
      this.show = false;
    },
    openRecord(record) {
      axios
        .get(`c-order-customer/detail?id=${record.id}`)
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
        })
        .catch((error) => {
          this.$message.error(error.message);
        });
    },
  },
};
</script>
