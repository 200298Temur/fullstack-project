import Vue from "vue";

window.Vue = require("vue");
import router from "./router";
 import ViewUi from 'view-design'
 Vue.use(ViewUi)
import "iview/dist/styles/iview.css";

import common from './common'
Vue.mixin(common)

Vue.component("mainapp", require("./components/mainapp.vue").default);
const app = new Vue({
    el: "#app",
    router
});
