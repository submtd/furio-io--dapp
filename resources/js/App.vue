<template>
    <Navbar/>
    <Alerts/>
    <div class="container mt-5">
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
// Components.
import Alerts from "./components/Alerts.vue";
import Footer from "./components/Footer.vue";
import Navbar from "./components/Navbar.vue";
import { onMounted } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import router from "./router";

export default {
    components: {
        Alerts,
        Footer,
        Navbar,
    },
    setup() {
        const store = useStore();

        onMounted(() => {
            if(!store.state.wallet.loggedIn && useRoute().name != 'Connect') {
                router.push("/connect");
            }
        });
    }
}
</script>
