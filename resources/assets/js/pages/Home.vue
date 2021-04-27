<template>
  <div class="home-page">
    <div class="slider">
      <a-carousel :dots="false" autoplay effect="fade">
        <!-- <div class="home-slider-item overlay">
          <img :src="require('./../assets/img/slider/banner5.jpg')" alt="" />
        </div> -->
        <div class="home-slider-item overlay">
          <img :src="require('./../assets/img/slider/banner7.jpg')" alt="" />
        </div>
        <div class="home-slider-item overlay">
          <img :src="require('./../assets/img/slider/banner8.jpg')" alt="" />
        </div>
        <div class="home-slider-item overlay">
          <img :src="require('./../assets/img/slider/banner6.jpg')" alt="" />
        </div>
      </a-carousel>
    </div>
    <order-form></order-form>

    <section id="what-do-area">
      <div class="container">
        <home-title
          :title="'Những gì chúng tôi có thể làm ? '"
          :text="'Mang lại những dịch vụ tốt nhất Việt Nam'"
        >
        </home-title>
        <a-row :gutter="[16, 16]">
          <a-col :span="8" :xs="{ span: 24 }" :sm="{ span: 8 }">
            <home-card
              :imageID="'card1.png'"
              :title="'CAR INSPECTION'"
              :description="'Erspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque'"
            ></home-card>
          </a-col>
          <a-col :span="8" :xs="{ span: 24 }" :sm="{ span: 8 }">
            <home-card
              :imageID="'card2.png'"
              :title="'CAR INSPECTION'"
              :description="'Erspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque'"
            ></home-card>
          </a-col>
          <a-col :span="8" :xs="{ span: 24 }" :sm="{ span: 8 }">
            <home-card
              :imageID="'card3.png'"
              :title="'CAR INSPECTION'"
              :description="'Erspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque'"
            ></home-card>
          </a-col>
        </a-row>
      </div>
    </section>
    <div class="service-area">
      <div class="container">
        <home-title
          :title="'Dịch vụ của chúng tôi'"
          :text="'Những dịch vụ đi đầu trong ngành vận chuyển phương tiện'"
        ></home-title>

        <a-row :gutter="[24, 24]">
          <a-col :lg="{ span: 8 }" :xs="{ span: 24 }">
            <form-service></form-service>
          </a-col>
          <a-col :lg="{ span: 8 }" :xs="{ span: 24 }">
            <form-service></form-service>
          </a-col>
          <a-col :lg="{ span: 8 }" :xs="{ span: 24 }">
            <form-service></form-service>
          </a-col>
        </a-row>
      </div>
    </div>
    <div class="funfact-area">
      <form-funfact></form-funfact>
    </div>
    
  </div>
</template>
<script>
import constant from "@/constant";
import Form from "./OrderVehice/Form";
import HomeTitle from "../pages/Home/Title";
import HomeCard from "@/components/Cards/HomeCard";
import FormService from "@/pages/Home/FormService";
import FormFunfact from "@/pages/Home/FormFunfact";

export default {
  // mounted() {
  //   if (this.$auth.user().customer_type == this.customerType.CLIENT) {
  //     this.$router.push({ path: "home" });
  //   } else {
  //     this.$router.push({ path: "home" });
  //   }
  // },
  components: {
    "order-form": Form,
    "home-title": HomeTitle,
    "home-card": HomeCard,
    "form-service": FormService,
    "form-funfact": FormFunfact,
  },
  data() {
    return {
      customerType: constant.customerType,
    };
  },
  methods: {
    login() {},
  },
};
</script>
<style scoped>
#app {
  background: #f4f4f4;
}

.service-area {
  padding: 100px 0;
}

#what-do-area {
  padding: 100px 0 0 0;
}

.our-car {
  padding: 100px 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

span.title-line::before {
  content: "";
  display: block;
  width: 50px;
  height: 2px;
  background: #333;
  position: absolute;
  top: 15px;
  right: -60px;
}

span.title-line::after {
  content: "";
  display: block;
  width: 50px;
  height: 2px;
  background: #333;
  position: absolute;
  top: 15px;
  left: -60px;
}

.home-slider-item > img {
  width: 100%;
  object-fit: cover;
  height: 600px;
}

@media (max-width: 576px) {
  .home-slider-item > img {
    height: 300px;
    margin-top: 108px;
  }
  .home-page {
    width: 100%;
    overflow: hidden;
  }
  .container {
    margin: 0 15px;
  }
}
@media (min-width: 577px) and (max-width: 890px) {
  .home-page {
    width: 100%;
    overflow: hidden;
  }
  .single-we-do.we-do-bg-1 {
    height: 200px;
  }
}
</style>