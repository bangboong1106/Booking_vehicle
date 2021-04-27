<template>
  <div ref="notifications" class="notifications-container">
    <div class="noti-header-container">
      <ul>
        <li @click="tabChange" v-bind:class="{'selected': isViewUnRead}">Chưa đọc</li>
        <li @click="tabChange" v-bind:class="{'selected': !isViewUnRead}">Tất cả</li>
      </ul>
      <!-- <div class="read-all" @click="readAll()">Đánh dấu đã đọc tất cả</div> -->
    </div>
    <div class="noti-container" @scroll="onScrollNotification">
      <div
        @click="getOrderHistory(item.id,item.action_id, item)"
        class="noti-item"
        v-bind:class="{'noti-item-unread': +item.read_status === 0 && !isViewUnRead}"
        v-for="item in notificationList"
        :key="item.id"
      >
        <span class="title">{{item.title}}</span>
        <div class="noti-body">
          <div class="noti-content">
            <p v-bind:class="{unread: +item.read_status === 0}">{{item.content}}</p>
            <p class="ins-date">{{item.ins_date}}</p>
          </div>
          <!-- <button class="view-detail">Xem chi tiết</button> -->
        </div>
      </div>

      <div class="total">Tổng số: {{totalCount}}</div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import EventBus from "@/event-bus";

export default {
  components: {
  },
  data() {
    return {
      notificationList: [],
      orderTracking: {},
      orderSelected: {},
      pageIndex: 1,
      isLoadingNotification: false,
      isViewUnRead: true,
      isLoadAll: false,
      totalCount: 0
    };
  },
  created() {
    this.loadNotification(this.pageIndex);
  },
  methods: {
    tabChange() {
      this.isViewUnRead = !this.isViewUnRead;
      this.pageIndex = 1;
      this.notificationList = [];
      this.loadNotification(this.pageIndex);
    },
    onScrollNotification({
      target: { scrollTop, clientHeight, scrollHeight }
    }) {
      if (
        !this.isLoadAll &&
        scrollTop + clientHeight >= scrollHeight &&
        !this.isLoadingNotification
      ) {
        this.loadNotification(++this.pageIndex);
      }
    },
    loadNotification(pageIndex) {
      let loader = this.$loading.show();
      if (!this.isLoadingNotification) {
        this.isLoadingNotification = true;
        axios
          .get(`c-notification/list`, {
            params: {
              pageIndex,
              isViewUnRead: this.isViewUnRead
            }
          })
          .then(response => {
            // this.countNotification = response.data.countUnread;
            this.notificationList = this.notificationList.concat(
              response.data.notification
            );
            this.totalCount = +response.data.count;
            this.isLoadAll =
              this.notificationList.length >= response.data.count;
          })
          .catch(error => {})
          .finally(() => {
            if (loader) {
              loader.hide();
            }
            this.isLoadingNotification = false;
          });
      }
    },
    readAll() {
      let loader = this.$loading.show();
      axios
        .get(`c-notification/read`)
        .then(response => {})
        .catch(error => {})
        .finally(() => {
          if (loader) {
            loader.hide();
          }
        });
    },
    getOrderHistory(id, orderId, notiItem) {
      let loader = this.$loading.show();
      axios
        .get("c-order/tracking?id=" + orderId)
        .then(response => {
          this.$refs.form_tracking.openNav();
          this.orderTracking = response.data;
          this.orderSelected = response.data.order;
          this.updateNotificationLog(id);
          if (notiItem.read_status === 0) {
            EventBus.$emit("read-notification", id);
          }
          notiItem.read_status = 1;
          loader.hide();
        })
        .catch(error => {
          loader.hide();
        });
    },
    updateNotificationLog(id) {
      axios
        .post("c-notification/save", {
          id: id
        })
        .then(response => {
          //this.loadNotification();
        })
        .catch(error => {});
    }
  }
};
</script>
<style lang="scss" scoped>
.notifications-container {
  .noti-header-container {
    display: flex;
    background-color: #eeeeee;
    justify-content: space-between;
    align-items: center;
    ul {
      display: flex;
      margin: 0;
      padding: 0;
      li {
        list-style-type: none;
        padding: 10px 20px;
        width: 120px;
        text-align: center;
        cursor: pointer;
      }
      li.selected {
        background-color: #ffffff;
      }
    }
    .read-all {
      height: 35px;
      line-height: 35px;
      color: rgba(0, 0, 0, 0.26);
      text-align: right;
      padding-right: 35px;
      cursor: pointer;
    }
  }

  .noti-container {
    height: calc(100vh - 142px);
    overflow: auto;
    margin-bottom: 48px;
    .noti-item {
      display: flex;
      justify-content: flex-start;
      align-items: center;
      background-color: #ffffff;
      padding: 20px;
      cursor: pointer;
      &:hover {
        background-color: #e8e8e8;
      }
      .title {
        border-radius: 5px;
        min-width: 80px;
        min-height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #007c91;
        font-weight: bold;
        color: #ffffff;
        font-size: 15px;
        margin-right: 20px;
      }
      .noti-body {
        display: flex;
        justify-content: space-between;
        width: 100%;
        align-items: center;
        button {
          border: 1px solid rgba(0, 0, 0, 0.09);
          color: rgba(0, 0, 0, 0.8);
          cursor: pointer;
          outline: none;
          padding: 0 0.4375rem;
          background-color: #fff;
          font-weight: 350;
          text-transform: capitalize;
          border-radius: 2px;
          letter-spacing: 0px;
        }
        .noti-content {
          .ins-date {
            color: rgba(0, 0, 0, 0.54);
            font-size: 13px;
          }
          .unread {
            font-weight: bold;
          }
        }
      }
    }
    .noti-item-unread {
      background-color: #e8f5e9;
    }

    .total {
      position: absolute;
      bottom: 0;
      height: 48px;
      background: #ffffff;
      left: 0;
      right: 0;
      display: flex;
      align-items: center;
      border-top: 1px solid #cccccc;
    }
  }
}
</style>
