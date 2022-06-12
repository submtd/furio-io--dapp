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
        <div class="container text-right mb-5">
            $FUR Balance: <strong>{{ balances.tokenBalance }}</strong><br/>
            USDC Balance: <strong>{{ balances.paymentBalance }}</strong><br/>
            <button @click="balances.refresh" class="btn btn-link">refresh <i class="bi bi-arrow-clockwise"></i></button><br/>
        </div>
    </div>
</template>

<script>
import { computed } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import router from "../router";
import useWallet from "../composables/useWallet";
import useBalances from "../composables/useBalances";

export default {
    setup () {
        const store = useStore();
        const wallet = useWallet();
        const balances = useBalances();

        const name = computed(() => {
            return store.state.wallet.name ?? store.state.wallet.shortAddress;
        });

        if(!store.state.wallet.loggedIn && useRoute().name != 'Connect') {
            router.push("/connect");
        }

        const profileLink = () => {
            router.push("/participant/" + store.state.wallet.address);
        }

        return {
            store,
            wallet,
            name,
            profileLink,
            balances,
        }
    }
}
</script>
