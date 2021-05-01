import Vue from "vue";
import axios from "axios";
import VueAxios from "vue-axios";
import App from "./App";
import router from "./router/index";
import Antd from "ant-design-vue";
import "ant-design-vue/dist/antd.css";
import PaperDashboard from "./plugins/paperDashboard";
import Loading from "vue-loading-overlay";
import firebase from "firebase";

import "vue-loading-overlay/dist/vue-loading.css";
import VueAuth from "@websanova/vue-auth";
import * as VueGoogleMaps from "vue2-google-maps";
import VueQrcode from "@chenfengyuan/vue-qrcode";
import { FormModel } from "ant-design-vue";

//Vuex
import store from './store/index'

Vue.component(VueQrcode.name, VueQrcode);
function debounce(fn, delay = 300) {
  var timeoutID = null;

  return function () {
    clearTimeout(timeoutID);

    var args = arguments;
    var that = this;

    timeoutID = setTimeout(function () {
      fn.apply(that, args);
    }, delay);
  };
}

Vue.use(VueGoogleMaps, {
  load: {
    key: "AIzaSyDh7atycbDhWM6Qz-H4R9ZiTY4j0LnMA8w",
    libraries: "places",
    region: "VI",
    language: "vi",
  },
});

// this is where we integrate the v-debounce directive!
// We can add it globally (like now) or locally!
Vue.directive("debounce", (el, binding) => {
  if (binding.value !== binding.oldValue) {
    // window.debounce is our global function what we defined at the very top!
    el.oninput = debounce((ev) => {
      el.dispatchEvent(new Event("change"));
    }, parseInt(binding.value) || 300);
  }
});
Vue.use(Antd);
Vue.use(FormModel);
Vue.use(PaperDashboard);
Vue.use(VueAxios, axios);
Vue.use(Loading, {
  canCancel: true,
  onCancel: this.onCancel,
  color: "#008000",
});
axios.defaults.baseURL = "/api";
axios.interceptors.request.use(
  function (config) {
    return config;
  },
  function (err) {
    if (error.response.status === 401 || error.response.status === 403) {
      Vue.auth.logout({
        redirect: "login",
        makeRequest: false,
      });
    } else if (error.response.status === 500) {
      window.toastr.error(error.response.data.message, "Error");
      Vue.router.push("error-500");
    }
    return Promise.reject(err);
  }
);

Vue.router = router;
Vue.use(require("@websanova/vue-auth"), {
  auth: require("@websanova/vue-auth/drivers/auth/bearer.js"),
  http: require("@websanova/vue-auth/drivers/http/axios.1.x.js"),
  router: require("@websanova/vue-auth/drivers/router/vue-router.2.x.js"),

  notFoundRedirect: {path: "/home"},
});
Vue.use(VueAuth, {
  notFoundRedirect: {
    path: "/home",
  },
});
App.router = Vue.router;
/* eslint-disable no-new */

new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount("#app");

/*FCM Push Notification*/
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

Vue.prototype.$messaging = firebase.messaging();

navigator.serviceWorker
  .register("/firebase-messaging-sw.js")
  .then((registration) => {
    Vue.prototype.$messaging.useServiceWorker(registration);
  })
  .catch((err) => {
    console.log(err);
  });
const EventBus = new Vue();
Object.defineProperties(Vue.prototype, {
  $bus: {
    get: function () {
      return EventBus;
    },
  },
});
