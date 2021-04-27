<template>
  <o-list :supportRole="supportRole" :entity="entity"></o-list>
</template>
<script>
import BaseList from "@/pages/BasePage/BaseList";
import constant from "@/constant";

const entity = "location-group";
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