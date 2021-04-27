<template>
  <div class="single-we-do we-do-bg-1">
    <div class="we-do-content">
      <div class="display-table">
        <div class="display-table-cell">
          <h3>{{ title }}</h3>
          <p>{{ description }}</p>
          <a class="primary-background-color" href="#">Read More</a>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    title: String,
    description: String,
    imageId : String
  }
}
</script>

<style scoped>
.single-we-do {
  background-position: center center;
  background-size: cover;
  height: 300px;
  overflow: hidden;
  position: relative;
  -webkit-transition: .4s;
  transition: .4s;
}
.we-do-bg-1 {
  background-image:url("../../assets/img/vanchuyenmo.jpg");
}
.we-do-content {
  background-color: rgba(0,0,0,.7);
  bottom: -100%;
  color: #fff;
  height: 100%;
  left: 0;
  position: absolute;
  text-align: center;
  -webkit-transition: all .4s ease 0s;
  transition: all .4s ease 0s;
  width: 100%;
}
.display-table {
  height: 100%;
  width: 100%;
  display: table;
}
.display-table-cell {
  vertical-align: middle;
  display: table-cell;
}
.we-do-content h3 {
  color: rgb(0, 177, 79);
  font-size: 22px;
}
.we-do-content p {
  margin: 15px 0 20px;
}
.we-do-content a {
  border-radius: 2px;
  color: #000;
  display: inline-block;
  font-size: 13px;
  font-weight: 600;
  padding: 8px 15px;
}
.single-we-do:hover .we-do-content {
  bottom: 0;
}
</style>