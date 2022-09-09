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
        <div class="container mt-5 py-5">
            <h6 class="mb-3">Furio Rewards Responsibilty</h6>
            <p>Participation within the Furio Ecosystem is entirely at your own discretion. Please conduct your own research and read all of the available information. Remember that crypto currencies and the performance of projects carry no guarantees and you should not take on unnecessary risks. Material published by Furio should not be considered as financial advice.</p>
        </div>
        <div class="mb-5 border-setting">
            <div class="d-flex flex-row align-items-center justify-content-center flex-wrap">
                <a href="https://coinmarketcap.com/currencies/furio/" target="_blank">
                    <img src="../images/coinmarketcap.png" alt="CoinMarketCap" height='60px' class='logo-filter' />
                </a>
                <a href="https://www.coingecko.com/en/coins/furio" target="_blank">
                    <img src="../images/coingecko.png" alt="CoinGecko" height="60px" class='logo-filter'/>
                </a>
                <a href="https://dappradar.com/binance-smart-chain/high-risk/furio" target="_blank">
                    <img src="../images/dappradar.png" alt="DappRadar" height="60px" class='logo-filter'/>
                </a>
                <a href="https://www.dapp.com/app/furio" target="_blank">
                    <img src="../images/dapp.png" alt="DappRadar" height="60px" class='logo-filter'/>
                </a>
            </div>
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
import useWallet from "./composables/useWallet";
import Balance from "./components/Balance.vue";

export default {
    components: {
        Alerts,
        Footer,
        Navbar,
        Balance
    },
    Mounted() {
        console.log("Refesh Page");
        console.log("wallet flag: ", this.store.state.wallet.loggedIn);
    },
    created() {
        console.log("wallet flag", this.store.state.wallet.loggedIn);
    },
    setup() {
        const store = useStore();
        const wallet = useWallet();

        store.commit("loading", true);
        useReferral();
        useSettings().update();
        store.commit("loading", false);

        return {
            store,
            wallet
        };
    }
}
</script>
