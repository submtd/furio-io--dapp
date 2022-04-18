// Axios
window.axios = require("axios");
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Web3
import Web3 from "web3";
window.web3 = new Web3();
// Vue components
import Navbar from "./components/Navbar";
// Vue
import { createApp } from "vue";
createApp({
    components: {
        Navbar,
    }
})
    .mount("#app");
