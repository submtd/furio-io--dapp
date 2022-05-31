<template>
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
                        <router-link :to="{ name: 'Downline' }" class="nav-link" active-class="active">Downline</router-link>
                    </li>
                </ul>
                <form class="d-flex">
                    <router-link v-show="!store.state.wallet.loggedIn" :to="{ name: 'Connect' }" class="btn btn-lg text-light btn-primary col-12" active-class="active">CONNECT</router-link>
                    <button v-show="store.state.wallet.loggedIn" @click="wallet.disconnect" class="btn btn-danger">DISCONNECT {{ store.state.wallet.shortAddress }}</button>
                </form>
            </div>
        </div>
    </nav>
</template>

<script>
import { useStore } from "vuex";
export default {
    setup () {
        const store = useStore();
        return {
            store,
        }
    }
}
</script>
