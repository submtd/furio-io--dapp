<template>
    <h1 v-show="!store.state.wallet.loggedIn">Connect</h1>
    <h1 v-show="store.state.wallet.loggedIn">Connected</h1>
    <div v-show="!store.state.wallet.loggedIn" class="bg-light text-dark rounded p-5">
        <div class="row">
            <div class="col text-center">
                <a @click="wallet.metamask" href="#">
                    <img class="img-fluid mb-2" width="300" src="../../images/metamask.svg"/><br/>
                    Connect with MetaMask
                </a>
            </div>
            <div class="col text-center">
                <a @click="wallet.walletconnect" href="#">
                    <img class="img-fluid mb-2" width="300" src="../../images/walletconnect.svg"/><br/>
                    Connect with WalletConnect
                </a>
            </div>
            <div class="col text-center">
                <button @click="wallet.connectWeb3">connect</button>
            </div>
        </div>
    </div>
</template>

<script>
import { onMounted } from "vue";
import { useStore } from "vuex";
import useSettings from "../composables/useSettings";
import useWallet from "../composables/useWallet";
export default {
    setup () {
        const store = useStore();
        //const settings = useSettings();
        const wallet = useWallet();

        onMounted(async () => {
            //await settings.update();
        })

        return {
            store,
            wallet,
        }
    }
}
</script>
