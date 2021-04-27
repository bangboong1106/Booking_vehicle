<template>
  <div class="order-info list-car">
    <a-form>
      <a-form-item label="Điểm nhận xe">

        <common-select
            :textHolder="'Chọn điểm nhận xe'"
            :selectValue = "formData.locationDestination"
            :icon="'environment'"
        ></common-select>
      </a-form-item>
      <a-form-item label="Điểm trả xe" >
        <common-select
            :textHolder="'Chọn điểm trả xe'"
            :selectValue ="formData.locationArrival"
            :icon="'environment'"
        ></common-select>

      </a-form-item>
      <a-form-item label="Ngày nhận">
        <a-date-picker
            type="date"
            :value="formData.startTime.value ? moment(`${formData.startTime.value}`, dateFormat) : null"
            @change="setDateForm"
            placeholder="Chọn ngày nhận"
            style="width: 100%"
        />
      </a-form-item>
    </a-form>
  </div>
</template>
<script >
import moment from 'moment';
import CommonSelect from "@/components/Select/CommonSelect";

export default {
  components:{
    'common-select' : CommonSelect
  },
  data(){
    return{
      dateFormat: 'YYYY/MM/DD',
      formData : {}
    }
  },
  created() {
    this.getData();
  },
  destroyed() {
  },
  methods:{
    moment,
    getData(){
      this.formData = JSON.parse(localStorage.getItem('cart'));
    },
    setDateForm(date,dateString){
      this.formData.startTime = {value : dateString}
      localStorage.setItem('cart',JSON.stringify(this.formData))
    }
  },
  mounted() {

  }
};
</script>
<style scoped>
.ant-row.ant-form-item{
  margin-bottom: 8px;
}
@media (max-width: 576px) {
  .list-car{

  }


}

</style>
