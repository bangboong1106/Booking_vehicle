<template>
  <div>
    <a-menu
        style="background: #11509b"
        mode="horizontal"
        :style="{ lineHeight: '42px', color: 'white' }"
    >
      <a-menu-item
          key="notification"
          :style="{ padding: '0 8px' }"
          @click="showNotification"
      >
        <a-badge
            :count="totalUnreadNotification"
        >
          <a-icon type="bell" :style="{ marginRight: 0, fontSize: '20px' }"/>
        </a-badge>
      </a-menu-item>
      <a-menu-item
          key="user"
          :style="{ padding: '0 8px' }"
          @click="showUserInfo"
      >
        <a-icon type="user" :style="{ marginRight: 0, fontSize: '20px' }"/>
      </a-menu-item>
    </a-menu>
    <a-drawer
        width="320"
        placement="right"
        :closable="false"
        :visible="notificationVisible"
        @close="onCloseNotification"
        :bodyStyle="style"
    >
      <div class="noti-heading">
        <h3 style="margin: 0">Thông báo (<span style="color: red">{{ totalUnreadNotification }}</span>)</h3>
        <a @click="onReadAllNotification" style="font-size: 12px">Đánh dấu đọc tất cả</a>
      </div>

      <a-divider style="margin : 0"/>
      <a-list
          item-layout="vertical"
          size="large"
          :data-source="notificationList"
      >
        <a-empty v-if="totalNotification==0">
          <span slot="description">Không có thông báo</span>
        </a-empty>
        <a-list-item
            slot="renderItem"
            key="item.title"
            slot-scope="item,index"
            @click="showNotificationDetail(item)"
            :style="{position: 'relative',padding:'10px 10px 10px 0px'}"
        >
          <a-list-item-meta  :style="style">

            <a slot="title" @click="showOrderDetail(item,index)">
              <b style="font-size: 12px">
                {{ item.title }}
              </b>
            </a>
            <div v-if="item.read_status == 0" slot="title" style="font-size: 10px;color : #1890ff"><b>{{
                item.ins_date.split(" ")[1] + " " +
                moment(String(item.ins_date)).format("DD-MM-YYYY")
              }}</b></div>

            <div v-else slot="title" style="font-size: 10px"><b>{{
                item.ins_date.split(" ")[1] + " " +
                moment(String(item.ins_date)).format("DD-MM-YYYY")
              }}</b></div>
            <div slot="description">
              <span style="font-size: 12px">{{item.message}}</span>
            </div>
            <a-avatar
                :src="imageNotify"
                slot="avatar"
                style="height: 24px;width:24px;border-radius: 50%;background-color: #11509B"
                alt=""/>

          </a-list-item-meta>
          {{ item.content }}

          <a-badge v-if="item.read_status == 0" status="processing" style="margin-left: 4px;position: absolute;right: -14px;top: 50%"/>
          <a-badge v-else status="default" style="margin-left: 4px;position: absolute;right: -14px;top: 50%"/>

        </a-list-item>
      </a-list>
      <div
          v-if="showButton"
          :style="{
          position: 'absolute',
          bottom: 0,
          width: '100%',
          borderTop: '1px solid #e8e8e8',
          padding: '6px 12px',
          textAlign: 'right',
          left: 0,
          background: '#fff',
          borderRadius: '0 0 4px 4px',
        }"
      >
        <a-button style="width: 100%" @click="showMore" :loading="loading">
          Xem thêm
        </a-button>
      </div>

    </a-drawer>

    <a-drawer
        width="320"
        placement="right"
        :closable="false"
        :visible="visible"
        @close="onClose"
    >
      <a-row>
        <a-col :span="8"></a-col>
        <a-col :span="12">
          <a-avatar shape="square" :size="64" icon="user"/>
        </a-col>
      </a-row>
      <a-row>
        <a-col :span="24">
          <a-descriptions
              title="Thông tin người dùng"
              :column="1"
              :size="'middle'"
          >
            <a-descriptions-item label="Họ và tên">
              {{ this.$auth.user().full_name }}
            </a-descriptions-item>
            <a-descriptions-item label="Email">
              {{ this.$auth.user().email }}
            </a-descriptions-item>
            <a-descriptions-item label="Tên đăng nhập">
              {{ this.$auth.user().username }}
            </a-descriptions-item>
            <a-descriptions-item label="Số điện thoại">
              {{ this.$auth.user().mobile_no }}
            </a-descriptions-item>
          </a-descriptions>
        </a-col>
      </a-row
      >
      <a-button type="danger" block @click="logout"> Đăng xuất</a-button>
    </a-drawer>
    <o-show :show="show" :form="detail" @hideModal="hideModal" @saveStatus="saveStatus($event)"/>

  </div>
</template>

<script>
import firebase from "firebase";
import axios from "axios";
import mixins from "@/mixins";
import EventBus from "@/event-bus";
import Show from "@/pages/OrderCustomer/Show";
import moment from "moment/moment";

const entity = 'order-customer';
export default {
  mixins: [mixins],
  components: {
    "o-show": Show,
  },
  computed: {

  },

  data() {

    return {
      entity,
      moment,
      show: false,
      detail: {},
      loading: false,
      params: {
        pageIndex: 1
      },
      imageNotify: require("@/assets/img/McLean-logo-3.png"),
      totalUnreadNotification: 0,
      totalNotification : 0,
      notificationList: [],
      notificationVisible: false,
      visible: false,
      showButton: true,
      style: {
        padding: '39px 16px 52px 16px',
      },

    };
  },
  methods: {
    hideModal() {
      this.show = false;
    },
    showOrderDetail(item,index) {
      axios
          .get(`c-${this.entity}/detail?id=${item.action_id}`)
          .then((response) => {


            if (response.data.errorCode != 0) {
              if (Array.isArray(response.data.errorMessage)) {
                this.$message.error(
                    response.data.errorMessage.map((p) => p.errorMessage).join("\n")
                );
              } else {
                this.$message.error(response.data.errorMessage);
              }
              return;
            }
            this.detail = response.data.data;
            this.show = true;
            this.notificationList[index].read_status = 1;
            this.updateNotificationLog(item.id);
          })
          .catch((error) => {
            this.$message.error(error.message);
          });
    },
    saveStatus($event) {
      if (this.$refs.list) {
        this.$refs.list.reload();
      }
    },
    showNotification() {
      this.notificationVisible = true;
    },
    onCloseNotification() {
      this.notificationVisible = false;
    },
    showNotificationDetail(item) {
      // this.notificationVisible = false;
      if (item.action_screen == 1) {
        this.$router.push({path: "order-customer"});
      }
    },
    showUserInfo() {
      this.visible = true;
    },
    onClose() {
      this.visible = false;
    },
    logout() {
      this.$auth.logout();
      this.$router.push("login");
    },
    registerFcmToken() {
      var vm = this;
      var config = {
        apiKey: "AIzaSyBc5a0MW_MJ7Jg4TfMNdAUC2j7EqcoN-WY",
        messagingSenderId: "670679497477",
        projectId: "mclean-910c7",
        storageBucket: "mclean-910c7.appspot.com",
        appId: "1:670679497477:web:9a441f96a1cc98aa3e156c",
      };
      if (!firebase.apps.length) {
        firebase.initializeApp(config);
      }
      const messaging = firebase.messaging();

      messaging
          .requestPermission()
          .then(function () {
            return messaging.getToken();
          })
          .then(function (token) {
            vm.updateFcmToken(token);
          })
          .catch(function (err) {
            console.log("*****Unable to get permission to notify.", err);
          });

      messaging.onMessage(function (payload) {
        if (payload.data.webAdmin == "false") {
          // vm.$notifications.setOptions({
          //   type: "String",
          //   timeout: 20000,
          //   horizontalAlign: "right",
          //   verticalAlign: "top",
          // });
          // vm.$notify({
          //   type: "success",
          //   message: payload.data.message,
          //   title: payload.data.title,
          //   icon: payload.data.imageUrl,
          // });

          this.$message.success(payload.data.message, 5);
        }
      });
    },
    updateFcmToken(token) {
      axios
          .post("c-notification/update-token-fcm", {
            token: token,
          })
          .then((response) => {
          })
          .catch((error) => {
          });
    },
    showMore() {
      this.params.pageIndex++;
      this.loadNotification();
      this.loading = true;
    },
    loadNotification() {
      var notiList = [];
      axios
          .post(`c-notification/list?pageIndex=${this.params.pageIndex}`)
          .then((response) => {
            if (response.data.data.totalPage == this.params.pageIndex) {
              this.showButton = false;
            }
            if (response.data.errorCode != 0) return;
            this.totalUnreadNotification = response.data.data.countUnread;
            notiList = response.data.data.items;
            this.notificationList = this.notificationList.concat(notiList);
            this.totalNotification = response.data.data.totalCount;
            this.loading = false;

          })
          .catch((error) => {
          });
    },
    updateNotificationLog(id) {
      axios
          .post("c-notification/save", {
            id: id,
          })
          .then((response) => {
          })
          .catch((error) => {
          });
    },
    onReadAllNotification(){
      axios
      .post("c-notification/read")
      .then(response=>{
        this.notificationList.forEach(item => {
          item.read_status = 1;
        })
      })
      .catch(error=>{})
    }
  },

  created() {
    this.loadNotification();
    this.registerFcmToken();
  },

  mounted() {
    EventBus.$on("read-notification", (id) => {
      --this.totalUnreadNotification;
    });
  },
};
</script>
<style lang="scss">
.noti-heading {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: absolute;
  top: 0px;
  width: 100%;
  border-top: 1px solid rgb(232, 232, 232);
  padding: 8px 12px;
  text-align: right;
  left: 0px;
  background: rgb(255, 255, 255);
  z-index: 10;
}

.ant-list-vertical .ant-list-item-meta {
  margin: 0 !important;
}

.ant-list-vertical .ant-list-item-meta-title {
  margin-bottom: 0 !important;
}

</style>
