<template>
    <nav class="navbar navbar-dark navbar-expand-xl pt-4">
        <div class="container">
            <router-link :to="{ name: 'Presale' }" class="navbar-brand">
                <img class="d-none d-lg-block" src="../../images/furio-logo.svg" alt="Furio Logo" height="60"/>
                <img class="d-lg-none" src="../../images/furio-icon.svg" alt="Furio Icon" height="60"/>
            </router-link>
            <form class="d-flex">
                <router-link v-show="!store.state.wallet.loggedIn" :to="{ name: 'Connect' }" class="btn btn-lg text-light btn-primary col-12" active-class="active">CONNECT</router-link>
                <button v-show="store.state.wallet.loggedIn" @click="wallet.disconnect" class="btn btn-danger">DISCONNECT {{ store.state.wallet.shortAddress }}</button>
            </form>
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
