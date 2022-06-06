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
                            <router-link :to="{ name: 'Airdrop' }" class="nav-link" active-class="active">Airdrop</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{ name: 'Downline' }" class="nav-link" active-class="active">Downline</router-link>
                        </li>
                    </ul>
                    <div class="d-flex">
                        <ul class="navbar-nav flex-column text-right">
                            <li class="nav-item">
                                <router-link :to="{ name: 'Profile' }" class="nav-link" active-class="active">{{ name }}</router-link>
                            </li>
                            <li class="nav-item">
                                <button @click="wallet.disconnect" class="btn btn-sm btn-secondary">Disconnect {{ store.state.wallet.shortAddress }}</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <div class="container">
            
        </div>
    </div>
</template>

<script>
import { computed } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import router from "../router";
import useWallet from "../composables/useWallet";

export default {
    setup () {
        const store = useStore();
        const wallet = useWallet();

        const name = computed(() => {
            return store.state.wallet.name ?? 'Profile';
        });

        if(!store.state.wallet.loggedIn && useRoute().name != 'Connect') {
            router.push("/connect");
        }

        return {
            store,
            wallet,
            name,
        }
    }
}
</script>
