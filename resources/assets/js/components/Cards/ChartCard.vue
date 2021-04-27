<template>
  <card :class="{'show-full-screen': showingFullScreen}">
    <template slot="header">
      <div class="main-header">
        <div class="title-container">
          <h4 v-if="$slots.title || title" class="card-title">
            <slot name="title">{{title}}</slot>
          </h4>
          <p class="card-category">
            <slot name="subTitle">{{subTitle}}</slot>
          </p>
        </div>
        <div class="chart-option">
          <span title="Nạp" @click="reloadChart()" class="ti-reload"></span>
          <span
            title="Mở rộng"
            v-if="!showingFullScreen && isShowFullScreen"
            class="ti-fullscreen"
            @click="togglerShowFullScreen()"
          ></span>
          <span
            :style="styleIconCollapseScreen"
            v-if="showingFullScreen && isShowFullScreen"
            class="icon-param-img"
            @click="togglerShowFullScreen()"
          ></span>
          <span title="Tham số" class="ti-settings" @click="toggleParams()"></span>
        </div>
      </div>
      <div class="total-container">
        <div class="total-item">{{totalValue}}</div>
      </div>
    </template>
    <div class="chart-body">
      <apexchart
        width="100%"
        height="400"
        class="apexchart"
        ref="chart"
        v-if="!isShowCustomChart"
        :type="chartType"
        :options="chartOptions"
        :series="chartData"
      ></apexchart>
      <div class="custom-chart" v-if="isShowCustomChart">
        <slot name="custom-chart"></slot>
      </div>
      <div class="mobile-chart">
        <slot name="mobile-chart"></slot>
      </div>
      <div class="footer">
        <div class="chart-legend">
          <slot name="legend"></slot>
        </div>
        <hr />
        <div class="stats">
          <slot name="footer"></slot>
        </div>
        <div class="pull-right"></div>
      </div>
    </div>
    <div v-show="isShowParams" class="params">
      <div class="params-header">
        Chọn tham số biểu đồ
        <span class="close-button" @click="toggleParams()">X</span>
      </div>
      <div class="params-content">
        <slot name="params-content"></slot>
      </div>
      <div class="params-footer">
        <button class="cancel" @click="toggleParams()">Hủy bỏ</button>
        <button class="accept" @click="acceptParameter()">Đồng ý</button>
      </div>
    </div>
    <div v-show="isShowParams" class="overlay"></div>
  </card>
</template>
<script>
import Card from "./Card.vue";
import VueApexCharts from "vue-apexcharts";

export default {
  name: "chart-card",
  components: {
    apexchart: VueApexCharts
  },
  props: {
    footerText: {
      type: String,
      default: ""
    },
    title: {
      type: String,
      default: ""
    },
    subTitle: {
      type: String,
      default: ""
    },
    chartType: {
      type: String,
      default: "line" // Line | Pie | Bar
    },
    chartOptions: {
      type: Object,
      default: () => {
        return {};
      }
    },
    chartData: {
      type: Array,
      default: () => {
        return [];
      }
    },
    totalValue: {
      type: String,
      default: ''
    },
    isShowCustomChart: {
      type: Boolean,
      default: false
    },
    isShowFullScreen: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      chartId: "no-id",
      isShowParams: false,
      showingFullScreen: false,
      styleIconCollapseScreen: {
        "background-image": `url(${require("@/assets/img/icons8-collapse-80.png")})`
      }
    };
  },
  methods: {
    toggleParams() {
      this.isShowParams = !this.isShowParams;
    },
    acceptParameter() {
      this.$emit("acceptParams");
      this.toggleParams();
    },
    reloadChart() {
      this.$emit("acceptParams");
    },
    togglerShowFullScreen() {
      this.showingFullScreen = !this.showingFullScreen;
    }
  }
};
</script>
<style lang="scss" scoped>

.mobile-chart {
  display: none;
  margin: auto;
}

.card-title {
  font-size: 20px;
}
.card.show-full-screen {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 5;
}
.card-category {
  margin-top: 10px;
}

.main-header {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
}

.chart-option {
  margin-top: 10px;
  cursor: pointer;
  span {
    margin-left: 15px;
    &.icon-param-img {
      display: inline-block;
      width: 16px;
      height: 16px;
      background-size: 16px;
    }
  }
}

.total-container {
  display: flex;
  margin-top: 10px;
  justify-content: center;
  .total-item {
    padding: 10px;
    border: 1px solid #dfdfdf;
    font-weight: 500;
    border-radius: 3px;
  }
}

.params {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 3;
  background: #ffffff;
  min-width: 350px;
  .params-header {
    background: #11509b;
    color: #ffffff;
    font-weight: 500;
    padding: 10px;
    .close-button {
      float: right;
      cursor: pointer;
    }
  }
  .params-content {
    padding: 15px;
  }
  .params-footer {
    float: right;
    margin-right: 15px;
    margin-bottom: 15px;
    button {
      font-weight: 100;
      border-radius: 5px;
      padding: 5px;
      color: #ffffff;
    }
    .accept {
      background-color: #1976d2;
    }
    .cancel {
      background-color: transparent;
      color: #000000;
      border-color: transparent;
    }
  }
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.4);
  z-index: 2;
}

@media (max-width: 426px) {
  .apexchart {
    display: none;
  }

  .mobile-chart {
    display: table;
  }
}
</style>
