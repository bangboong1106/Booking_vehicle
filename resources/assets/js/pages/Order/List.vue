<template>
  <div>
    <o-list
      :entity="entity"
      :isAdd="false"
      :isAction="false"
      @openRecord="openRecord($event)"
      :supportRole="supportRole"
    ></o-list>
    <o-show :show="show" :form="detail" @hideModal = "hideModal"/>
  </div>
</template>
<script>
import BaseList from "@/pages/BasePage/BaseList";
import Show from "@/pages/Order/Show";
import axios from "axios";
import constant from "@/constant";

const entity = "order";

export default {
  components: {
    "o-list": BaseList,
    "o-show": Show,
  },
  data() {
    return {
      entity,
      show: false,
      detail: {},
      supportRole: [
        constant.customerType.CUSTOMER,
        constant.customerType.STAFF,
      ],
    };
  },
  mounted() {},
  methods: {
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
        })
        .catch((error) => {
          this.$message.error(error.message);
        });
    },
  },
};
</script>
