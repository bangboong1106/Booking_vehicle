<template>
  <div class="book-car container">
    <a-row
        :gutter="[16, 16]"

    >
      <a-col
          :lg="{ span: 6, gutter: [16, 16] }"
          :xs="{ span: 24, gutter: [16, 16] }"
          :sm="{ span: 12, gutter: [16, 16] }"
      >
        <common-select
            :textHolder='"Chọn địa điểm nhận"'
            :icon="'environment'"
            :isDiaplay ="true"
            @setLocationValue = "getLocationDestination"
        ></common-select>
      </a-col>
      <a-col
          :lg="{ span: 6, gutter: [16, 16] }"
          :xs="{ span: 24, gutter: [16, 16] }"
          :sm="{ span: 12, gutter: [16, 16] }"
      >
        <common-select
            :textHolder='"Chọn địa điểm trả"'
            :icon="'environment'"
            :isDiaplay ="true"
            @setLocationValue = "getLocationArrival"
        ></common-select>
      </a-col>
      <a-col
          :lg="{ span: 6, gutter: [32, 32] }"
          :xs="{ span: 24, gutter: [32, 32] }"
          :sm="{ span: 12, gutter: [32, 32] }"
      >
        <a-space direction="vertical" style="width:100%">
          <a-date-picker
              format="YYYY-MM-DD"
              :disabled-date="disabledDate"
              @change="startDate"
              placeholder="Chọn ngày nhận xe"/>
        </a-space>
      </a-col>


      <a-col
          :lg="{ span: 6, gutter: [16, 16] }"

          :sm="{ span: 16, gutter: [16, 16] }"
          class="book-btn"
      >
        <a-button class="primary-background-color"  type="primary" @click="order" style="width: 100%"
        >Đặt xe</a-button
        >
      </a-col>
    </a-row>
  </div>
</template>

<script>
import CommonSelect from "@/components/Select/CommonSelect";
import moment from 'moment';
export default {
  components: {
    'common-select': CommonSelect
  },
  data(){
    return {
      form : {
        locationArrival : "",
        locationDestination : "",
        startTime : null
      },

    }
  },
  methods: {
    moment,
    disabledDate(current) {
      // Can not select days before today and today
      return current && current < moment().subtract(1,'day').endOf('day');
    },
    startDate(date,dateString){
      this.form.startTime = {value : dateString}
    },
    order(){

      localStorage.setItem('cart',JSON.stringify(this.form));
      this.$router.push('order-vehicle');
    },
    getLocationDestination(value){
      this.form.locationDestination = value;
    },
    getLocationArrival(value){
      this.form.locationArrival = value;
    },

  },
  computed:{

  }
}
</script>

<style scoped>



.ant-calendar-picker {
  width: 100%;
}

.book-car {
  background-color: #fff;
  padding: 70px 40px;
  margin: auto;
  box-shadow: 0 0 5px rgb(0 0 0 / 50%);
  position: relative;
  margin-top: -86px !important;
}

.select {
  width: 100%;
}

@media (max-width: 576px) {
  .book-car {
    padding: 20px;

    margin: 16px 15px !important;
  }

  .select {
    width: 100%;
  }

  .ant-calendar-picker {
    width: 100%;
  }

  .book-btn {
    margin: auto;
  }

}

@media (min-width: 577px) and (max-width: 890px) {
  .book-car {
    padding: 20px;
    margin: auto;
    margin-top: 0px!important;
  }

  .select {
    width: 100%;
  }

  .ant-calendar-picker {
    width: 100%;
  }

  .book-btn {
    width: 100%;
    margin: auto;
  }
}
</style>