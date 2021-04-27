<template>
  <div class="form-type-ship">
    <a-radio-group name="radioGroup" v-model="value" @change="onChange">
    <a-list item-layout="vertical" size="large" :data-source="listData">
      <a-list-item slot="renderItem" key="item.title" slot-scope="item, index">
        <a-radio :value="item.value" style="margin-left: -150px">
          {{item.title}}
        </a-radio>
        <img
            slot="extra"
            width="130"
            alt="logo"
            style="margin-top: 30px;margin-right: 20px"
            :src="item.avatar"
        />
        <a-list-item-meta :description="item.description">

        </a-list-item-meta>

      </a-list-item>
    </a-list>
      </a-radio-group>
  </div>
</template>
<script>

export default ({
  data() {
    const listData = [
      {
        title : 'Loại hình vận chuyển mở',
        avatar : 'https://vantaiduongbien.com.vn/upload/detail/2018/08/images/van-tai-duong-bien-1(2).jpg',
        description : 'Vận tải ô tô mở là loại hình vận chuyển ô tô phổ biến nhất . Vận chuyển bằng ô tô mở dễ sắp xếp hơn vận chuyển bằng ô tô kín . Do các hãng xe thùng hở có thể chứa 8 đến 10 xe.'
      },{
        title : 'Loại hình vận chuyển khép kín',
        avatar : 'https://vantaiduongbien.com.vn/upload/detail/2018/08/images/van-tai-duong-bien-1(2).jpg',
        description : 'Vận tải ô tô mở là loại hình vận chuyển ô tô phổ biến nhất . Vận chuyển bằng ô tô mở dễ sắp xếp hơn vận chuyển bằng ô tô kín . Do các hãng xe thùng hở có thể chứa 8 đến 10 xe.'
      },{
        title : 'Loại hình vận chuyển đường biển',
        avatar : 'https://vantaiduongbien.com.vn/upload/detail/2018/08/images/van-tai-duong-bien-1(2).jpg',
        description : 'Vận tải ô tô mở là loại hình vận chuyển ô tô phổ biến nhất . Vận chuyển bằng ô tô mở dễ sắp xếp hơn vận chuyển bằng ô tô kín . Do các hãng xe thùng hở có thể chứa 8 đến 10 xe.'
      }
    ]
    return {
      value:1,
      listData,

    }
  },
  methods:{
    onChange(e){
      console.log(e.target.value)
    }
  },
  components: {}
})
</script>
<style scoped>

</style>