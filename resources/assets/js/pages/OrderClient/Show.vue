<template>
  <div>
    <a-modal
      v-model="show"
      title="Chi tiết đơn hàng"
      :width="width"
      centered
      :bodyStyle="{
        padding: '16px',
        height: '80vh',
        overflowY: 'scroll',
        overflowX: 'hidden',
      }"
    >
      <template slot="footer">
        <a-button key="back" @click="hideModal"> Thoát </a-button>
      </template>
      <o-order :show="show" :form="form" />
    </a-modal>
  </div>
</template>
<script>
import axios from "axios";
import Order from "@/components/Order/Show";

export default {
  // mixins: [constant],
  components: {
    "o-order": Order,
  },
  props: {
    show: Boolean,
    form: {},
  },
  data() {
    return {

      current: null,
      btnZoomName: "Phóng to",
      status: "",
      width: "80%",
      height: "80%",
      title: "",
      data: {
        status: null,
        note: null,
      },
    };
  },
  mounted() {},
  methods: {
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
    hideModal(){
      this.$emit('hideModal')
    }
  },

};
</script>