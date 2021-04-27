<template>
  <section id="funfact-area" class="overlay section-padding">
    <div class="container">
      <a-row class="row">
        <a-col :span="24">
          <div class="funfact-content-wrap">
            <a-row :gutter="[16, 16]">

              <a-col :lg="{ span: 8 }" :xs="{ span: 24 }">
                <div class="single-funfact">
                  <div class="funfact-icon primary-color">
                    <a-icon type="smile"/>
                  </div>
                  <div class="funfact-content">
                    <p><span class="counter">550</span>+</p>
                    <h4>SỰ HÀI LÒNG CỦA KHÁCH HÀNG</h4>
                  </div>
                </div>
              </a-col>


              <a-col :lg="{ span: 8 }" :xs="{ span: 24 }">
                <div class="single-funfact">
                  <div class="funfact-icon primary-color">
                    <a-icon type="car"/>
                  </div>
                  <div class="funfact-content">
                    <p><span class="counter">250</span>+</p>
                    <h4>XE TRONG KHO</h4>
                  </div>
                </div>
              </a-col>


              <a-col :lg="{ span: 8 }" :xs="{ span: 24 }">
                <div class="single-funfact">
                  <div class="funfact-icon primary-color">
                    <a-icon type="home"/>
                  </div>
                  <div class="funfact-content">
                    <p><span class="counter">50</span>+</p>
                    <h4>VĂN PHÒNG TRONG THÀNH PHỐ</h4>
                  </div>
                </div>
              </a-col>

            </a-row>
          </div>
        </a-col>
      </a-row>
    </div>
  </section>
</template>
<script>

</script>
<style scoped>
.single-funfact {
  justify-content: space-around;
  display: flex;
  align-items: center;
  background: rgba(0, 0, 0, 0.6);
  padding: 25px 40px;
  text-align: center;
}
.funfact-icon {
  font-size: 50px;
}
.funfact-content {
  font-size: 40px;
  font-weight: 700;
  padding-left: 25px;
}
.funfact-content p {
  margin : 0;
  padding : 0;
}
.funfact-content h4 {
  color: #eaeaea;
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
}
#funfact-area {
  background-image: url('../../assets/img/funfact-bg.jpg');
  background-size: cover;
  background-position: center center;
  color: #fff;
}
.overlay:after {
  background-color: rgba(0, 0, 0, 0.8);
  content: "";
  left: 0;
  position: absolute;
  top: 0;
  height: 100%;
  width: 100%;
  z-index: -1;
}
.section-padding {
  padding: 100px 0;
}
.funfact-icon, .funfact-content {
  display: table-cell;
  vertical-align: middle;
}
</style>
