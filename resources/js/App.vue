<template>
    <Balance/>
    <Navbar/>
    <Alerts/>
    <div class="container-fluid mt-5">
        <!-- BEGIN PAGE CONTENT -->
        <div class="container">
            <div class="mb-5 py-5">
                <router-view/>
            </div>
        </div>
        <div class="mt-5 py-5">
            <h5 class="mb-3">Furio Rewards Responsibilty</h5>
            <p>Participation within the Furio Ecosystem is entirely at your own discretion. Please conduct your own research and read all of the available information. Remember that crypto currencies and the performance of projects carry no guarantees and you should not take on unnecessary risks. Material published by Furio should not be considered as financial advice.</p>
        </div>
        <!-- END PAGE CONTENT -->
    </div>
    <Footer/>
</template>

<script>
import { useStore } from "vuex";
// Components.
import Alerts from "./components/Alerts.vue";
import Footer from "./components/Footer.vue";
import Navbar from "./components/Navbar.vue";
import useReferral from "./composables/useReferral";
import useSettings from "./composables/useSettings";
import Balance from "./components/Balance.vue";

export default {
    components: {
    Alerts,
    Footer,
    Navbar,
    Balance
},
    setup() {
        const store = useStore();
        store.commit("loading", true);
        useReferral();
        useSettings().update();
        store.commit("loading", false);
    }
}
</script>
