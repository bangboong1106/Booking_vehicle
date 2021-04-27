<template>
  <o-list :entity="entity" :supportRole="supportRole"></o-list>
</template>
<script>
import BaseList from "@/pages/BasePage/BaseList";
import constant from "@/constant";

const entity = "goods-unit";

export default {
  components: {
    "o-list": BaseList,
  },
  data() {
    return {
      entity,
      supportRole: [
        constant.customerType.CUSTOMER,
        constant.customerType.STAFF,
      ],
    };
  },
  mounted() {},
  methods: {},
};
</script>