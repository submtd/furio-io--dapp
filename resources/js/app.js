// jQuery
window.$ = window.jQuery = require("jquery");
// Popper
window.Popper = require("popper.js");
// Bootstrap
require("bootstrap");
// Axios
window.axios = require("axios");
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Web3
import Web3 from "web3";
window.web3 = new Web3();
// Vue
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
//import useSettings from "./composables/useSettings";
//useSettings();
createApp({
    components: {
        App,
    }
})
    .use(router)
    .use(store)
    .mount("#app");
