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

export default {
    components: {
        Alerts,
        Footer,
        Navbar,
    },
    setup() {
        // Get referrer from query string and set a cookie
        const params = new Proxy(new URLSearchParams(window.location.search), {
            get: (searchParams, prop) => searchParams.get(prop),
        });
        let referrer = params.ref;
        alert(referrer);
        console.log(referrer);
    }
}
</script>
