<template>
    <div class="content dashboard-content">
        <div class="container-fluid">
            <transition name="fade" mode="out-in">
                <!-- your content here -->
                <router-view @changePassword="changePassword()"></router-view>
            </transition>
        </div>
    </div>
</template>
<script>
    export default {
        methods: {
            changePassword() {
                this.$emit('changePassword');
            }
        }
    };
</script>
<style>

    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.1s;
    }

    .fade-enter,
    .fade-leave-to
        /* .fade-leave-active in <2.1.8 */

    {
        opacity: 0;
    }
</style>
