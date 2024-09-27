import Vue from "vue";

window.Vue = require("vue");
import router from "./router";
import store from "./store";
import ViewUi from "view-design";
Vue.use(ViewUi);
import "iview/dist/styles/iview.css";

import Editor from "vue-editor-js";
Vue.use(Editor);

import common from "./common";
Vue.mixin(common);

import jsonToHtml from './jsonToHtml'
Vue.mixin(jsonToHtml)

Vue.component("mainapp", require("./components/mainapp.vue").default);
const app = new Vue({
    el: "#app",
    router,
    store,
});
