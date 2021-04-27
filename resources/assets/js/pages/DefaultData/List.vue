<template>
  <o-list :supportRole="supportRole" :entity="entity" :widthModal="width"></o-list>
</template>
<script>
import BaseList from "@/pages/BasePage/BaseList";
import constant from "@/constant";

const entity = "default-data";

export default {
  components: {
    "o-list": BaseList,
  },
  data() {
    return {
      entity,
      width:'600px',
      supportRole: [
        constant.customerType.CUSTOMER,
        constant.customerType.STAFF,
      ],
    };
  },
};
</script>