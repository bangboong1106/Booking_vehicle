<template>
  <div>
    <div style="background: #ececec; padding: 16px">
      <a-row :gutter="16">
        <a-col :span="3" v-for="(item, index) in statusList" :key="index">
          <a-card>
            <a-statistic
              :title="item.text"
              :value="item.count"
              :value-style="{ color: '#3f8600', position:'absolute',bottom : '0'}"
              style="height: 160px;position: relative;display: flex;justify-content: center"
            >
            </a-statistic>
          </a-card>
        </a-col>
      </a-row>
    </div>
    <div class="chart">
      <chart-card
        class="chart-order-container"
        :chartOptions="chartOptions"
        :chartData="chartData"
        title="Thống kê đơn hàng theo thời gian"
      >
        <template v-slot:params-content> </template>
      </chart-card>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import moment from "moment/moment";
import VueApexCharts from "vue-apexcharts";
import constant from "@/constant";
import ChartCard from "@/components/Chart/Chart";

export default {
  components: { ChartCard },
  data() {
    let statusList = constant.orderCustomerStatus;
    statusList.forEach((element) => {
      element.count = 0;
    });
    return {
      type: "client",
      groupStatusData: [],
      statusList,
      chartData: [],
      chartOptions: {
        chart: {
          id: "vuechart",
        },
        xaxis: {
          categories: [],
        },
        chart: {
          toolbar: {
            show: false,
          },
        },
        dataLabels: {
          enabled: true,
        },
        markers: {
          size: 6,
        },
        grid: {
          borderColor: "#e7e7e7",
          row: {
            colors: ["#f3f3f3", "transparent"],
            opacity: 0.5,
          },
        },
      },
    };
  },
  mounted() {
    this.loadStatusData();
    this.loadOrderByRangeTime();
  },
  methods: {
    loadStatusData() {
      axios
        .post(`c-dashboard/status`, {
          type: this.type,
          id: this.$auth.user().customer_id,
        })
        .then((response) => {
          if (response.data.errorCode != 0) {
            return;
          }
          let items = response.data.data;
          items.forEach((element) => {
            let item = this.statusList.find((p) => p.value == element.status);
            if (item) {
              item.count = element.count;
            }
          });
        });
    },
    acceptParams: function () {
      this.loadOrderByRangeTime();
    },
    loadOrderByRangeTime: function () {
      const fromDate = moment().startOf("month").format("YYYY-MM-DD"),
        toDate = moment().endOf("month").format("YYYY-MM-DD");
      axios
        .post("c-dashboard/order", {
          type: this.type,
          id: this.$auth.user().customer_id,
          fromDate,
          toDate,
        })
        .then((response) => {
          if (response.data.errorCode != 0) {
            return;
          }
          let data = response.data.data;
          this.chartOptions = {
            xaxis: {
              categories: data.map((x) => x.date),
              labels: {
                rotate: -20,
                rotateAlways: true,
              },
            },
          };
          this.chartData = [
            {
              data: data.map((x) => x.count),
            },
          ];
          this.totalOrders = data.reduce(
            (total, current) => total + current.count,
            0
          );
        })
        .finally(() => {});
    },
  },
};
</script>