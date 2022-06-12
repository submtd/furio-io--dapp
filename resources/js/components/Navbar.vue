<template>
    <div v-show="store.state.wallet.loggedIn">
        <nav class="navbar navbar-dark navbar-expand-xl pt-4">
            <div class="container">
                <router-link :to="{ name: 'Home' }" class="navbar-brand">
                    <img class="d-none d-lg-block" src="../../images/furio-logo.svg" alt="Furio Logo" height="60"/>
                    <img class="d-lg-none" src="../../images/furio-icon.svg" alt="Furio Icon" height="60"/>
                </router-link>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <router-link :to="{ name: 'Home' }" class="nav-link" active-class="active">Home</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{ name: 'Claim' }" class="nav-link" active-class="active">Claim</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{ name: 'Swap' }" class="nav-link" active-class="active">Swap</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{ name: 'Vault' }" class="nav-link" active-class="active">Vault</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{ name: 'Team' }" class="nav-link" active-class="active">Team</router-link>
                        </li>
                    </ul>
                    <div class="d-flex">
                        <ul class="navbar-nav flex-column text-right">
                            <li class="nav-item">
                                <button @click="profileLink" class="btn btn-link nav-link">{{ name }}</button>
                            </li>
                            <li class="nav-item">
                                <button @click="wallet.disconnect" class="btn btn-sm btn-secondary">Disconnect</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <div class="text-right mb-5">
            $FUR Balance: <strong>{{ tokenBalanceDisplay }}</strong>
            USDC Balance: <strong>{{ paymentBalanceDisplay }}</strong>
            <button @click="refreshBalances" class="btn btn-link">refresh <i class="bi bi-arrow-clockwise"></i></button>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import router from "../router";
import useWallet from "../composables/useWallet";
import useAlerts from "../composables/useAlerts";
import useDisplayCurrency from "../composables/useDisplayCurrency";

export default {
    setup () {
        const store = useStore();
        const wallet = useWallet();
        const alerts = useAlerts();
        const displayCurrency = useDisplayCurrency();
        const tokenBalance = ref(0);
        const paymentBalance = ref(0);

        const tokenBalanceDisplay = computed(() => {
            return displayCurrency.format(tokenBalance.value);
        });

        const paymentBalanceDisplay = computed(() => {
            return displayCurrency.format(paymentBalance.value);
        });

        const name = computed(() => {
            return store.state.wallet.name ?? store.state.wallet.shortAddress;
        });

        if(!store.state.wallet.loggedIn && useRoute().name != 'Connect') {
            router.push("/connect");
        }

        onMounted(async () => {
            if(store.state.wallet.loggedIn) {
                refreshBalances();
            }
        });

        const profileLink = () => {
            router.push("/participant/" + store.state.wallet.address);
        }

        const refreshBalances = async () => {
            try {
                const token = new web3.eth.Contract(JSON.parse(store.state.settings.token_abi), store.state.settings.token_address);
                const payment = new web3.eth.Contract(JSON.parse(store.state.settings.payment_abi), store.state.settings.payment_address);
                tokenBalance.value = await token.methods.balanceOf(store.state.wallet.address).call();
                paymentBalance.value = await payment.methods.balanceOf(store.state.wallet.address).call();
            } catch (error) {
                alerts.danger(error.message);
            }
        }

        return {
            store,
            wallet,
            name,
            profileLink,
            tokenBalanceDisplay,
            paymentBalanceDisplay,
            refreshBalances,
        }
    }
}
</script>
