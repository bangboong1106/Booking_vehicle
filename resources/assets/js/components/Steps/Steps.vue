<template>
<div>
    <a-steps :current="current">
        <a-step v-for="item in steps" :key="item.title" :title="item.title" />
    </a-steps>
    <div class="steps-content">
        {{ steps[current].content }}
    </div>
    <div class="steps-action">
        
    </div>
</div>
</template>

<script>
export default {
    props: {
        current: Number,
        
    },
    data() {
        return {
            current: 0,
            steps: [{
                    title: 'Bước 1',
                    // content: 'First-content',
                },
                {
                    title: 'Bước 2 ',
                    content: 'Chọn loại xe cần gửi',
                },
                {
                    title: 'Bước 3',
                    content: 'Xác nhận lại thông tin cá nhân',
                },
                {
                    title: 'Bước 4',
                    content: 'Hoàn tất',
                },
            ],
        };
    },
    methods: {
        
    },
};
</script>

<style scoped>
.steps-content {
    margin-top: 16px;
    border: 1px dashed #e9e9e9;
    border-radius: 6px;
    background-color: #fafafa;
    text-align: center;
    padding: 10px;
    font-size:22px;
}

.steps-action {
    margin-top: 24px;
}
</style>
