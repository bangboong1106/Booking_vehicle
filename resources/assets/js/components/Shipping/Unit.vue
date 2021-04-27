<template>
  <div>
    <a-radio-group name="radioGroup" :default-value="1">
      <a-list item-layout="vertical" size="large" :data-source="listData">
        <a-list-item slot="renderItem" key="item.title" slot-scope="item, index">
          <a-radio :value="item.value"  style="margin-left: -100px">
            {{item.title}}
          </a-radio>
          <img
              slot="extra"
              width="80"
              height="80"
              alt="logo"
              style="margin-top: 30px;margin-right: 20px;object-fit: cover"
              src="https://ceta.vn/wp-content/uploads/2020/12/Picture1.png"
          />
          <a-list-item-meta :description="item.description">
            <a slot="title" :href="item.href"></a>
          </a-list-item-meta>

        </a-list-item>
      </a-list>
    </a-radio-group>
  </div>
</template>
<script>
const listData = [];
for (let i = 0; i < 3; i++) {
  listData.push({
    href: 'https://www.antdv.com/',
    title: `Phương Anh ${i}`,
    value:`${i}`,
     });
}
export default ({
  data() {
    return {

      listData,

    }
  },
  components: {}
})
</script>