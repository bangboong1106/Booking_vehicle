<template>
  <o-list
    :entity="entity"
    :formData="formData"
  ></o-list>
</template>
<script>
import BaseList from "@/pages/BasePage/BaseList";

const entity = "role";

export default {
  components: {
    "o-list": BaseList,
  },
  data() {
    return {
      entity,
      title,
      formData: {
        code: "",
        name: "",
        description: "",
      },
    };
  },
  mounted() {},
  methods: {},
};
</script>
