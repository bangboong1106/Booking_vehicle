<template>
<div class="order-detail">
    <div class="ant-descriptions-title">Loại hình vận chuyển</div>
    <div class="type-ship">
        <div class="list-items-ship">
            <div class="item-ship-content" v-for="(item,index) in listItemShipType" :key="index">
                <a-radio-group name="radioGroup" v-model="value" @change="onChangeRadio">
                    <a-radio :value="`${item.title} :giá ${item.amount}`">
                        {{ item.title }}
                    </a-radio>
                    <div class="item-ship">
                        <div class="item-ship-avatar">
                            <img slot="extra" alt="logo" :src="item.avatar" />
                        </div>
                        <div class="item-ship-description">
                            <span>{{ item.description }}</span>
                        </div>
                        <div class="item-ship-delivery-time">
                            <span>{{ item.estimated_delivery_time }}</span>
                        </div>
                        <div class="item-ship-delivery-time">
                            <span>Giá :{{ item.amount }}</span>
                        </div>

                    </div>
                </a-radio-group>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import TypeShip from "@/components/Shipping/TypeShip";
import Unit from "@/components/Shipping/Unit";
import EventBus from "@/event-bus";

export default ({
    data() {
        const listItemShipType = [{
                id: 1,
                title: 'Loại hình vận chuyển mở',
                avatar: 'https://d1d9oqfe9g83w6.cloudfront.net/assets/prod/images/shutterstock_1553555501.jpg?979e447bb833d9f8',
                description: 'Vận tải ô tô mở là loại hình dịch vụ vận chuyển ô tô phổ biến nhất.  Vận chuyển bằng ô tô mở dễ sắp xếp hơn so với vận chuyển ô tô kín.  ',
                estimated_delivery_time: 'Thời gian giao hàng dự kiến từ 2-4 ngày',
                amount: '1.000.000'
            }, {
                id: 2,
                title: 'Loại hình vận chuyển khép kín',
                avatar: 'https://d1d9oqfe9g83w6.cloudfront.net/assets/prod/images/enclosed-car-transport-prices.jpg?9df9b9e0474f0646',
                description: 'Vận tải ô tô kín là loại hình vận tải ô tô có thêm tính năng bảo vệ cho phương tiện.  Đó là một lựa chọn vận chuyển bằng ô tô tuyệt vời nếu bạn cần vận chuyển một chiếc xe cổ điển hoặc sang trọng.  ',
                estimated_delivery_time: 'Thời gian giao hàng dự kiến từ 2-4 ngày',
                amount: '1.500.000'
            }, {
                id: 3,
                title: 'Loại hình vận chuyển đường biển',
                avatar: 'https://vantaiduongbien.com.vn/upload/detail/2018/08/images/van-tai-duong-bien-1(2).jpg',
                description: 'Vận tải ô tô mở là loại hình vận chuyển ô tô phổ biến nhất . Vận chuyển bằng ô tô mở dễ sắp xếp hơn vận chuyển bằng ô tô kín . Do các hãng xe thùng hở có thể chứa 8 đến 10 xe.',
                estimated_delivery_time: 'Thời gian giao hàng dự kiến từ 2-4 ngày',
                amount: '2.000.000'
            }

        ]
        return {
            listItemShipType,
            value: ""
        }
    },
    components: {
        TypeShip,
        Unit
    },
    computed: {
        value() {
            console.log(this.value)
        }
    },
    methods: {
        onChangeRadio(e) {
            EventBus.$emit('getTitleTypeShip', e.target.value)
        },
    }
})
</script>

<style>
.ant-descriptions-title {
    color: rgb(0, 177, 79) !important;
}

li.ant-list-item {
    flex-direction: row-reverse;
}

.ant-list-item-extra {
    margin-left: 0 !important;
}

.header>b {
    font-size: 16px;
}

.item-ship-description {
    margin-top: 16px;
}

.item-ship-content {
    padding: 0 0 16px 0;
}

.item-ship-avatar {
    margin-left: 40px;
}

.item-ship-avatar>img {
    width: 100%;
    margin-top: 16px;
}

.item-ship-description {
    margin-left: 40px;
}

.item-ship-amount {
    margin-left: 40px;
    color: red;
    font-size: 18px;
    font-weight: 600;
    margin-top: 8px;
}

.item-ship-delivery-time {
    margin-left: 40px;
    color: rgb(0, 177, 79);
    font-weight: 600;
    margin-top: 8px;
}

.order-detail {
    padding: 16px 16px 0 0;
}
</style>
