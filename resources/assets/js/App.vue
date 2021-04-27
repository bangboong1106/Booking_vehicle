<template>
    <div :class="{'nav-open': $sidebar.showSidebar}">
        <router-view></router-view>
    </div>
</template>

<script>
    export default {};
</script>
<style>
div#app{
  background: #F4F4F4;
}
</style>
