<template>
    <h1>Address Book</h1>
    <div class="bg-light text-dark rounded p-5">
        <div v-show="!store.state.wallet.loggedIn">
            <p>Please connect your wallet</p>
            <router-link :to="{ name: 'Connect' }" class="btn btn-lg text-light btn-primary" active-class="active">CONNECT</router-link>
        </div>
        <div v-show="store.state.wallet.loggedIn">
            {{ store.state.settings.claim_address }}
        </div>
    </div>
</template>

<script>
import { onMounted } from "vue";
import { useStore } from "vuex";
import useSettings from "../composables/useSettings";
export default {
    setup () {
        const store = useStore();
        const settings = useSettings();

        onMounted(async () => {
            settings.update();
        });

        return {
            store,
        }
    }
}
</script>
