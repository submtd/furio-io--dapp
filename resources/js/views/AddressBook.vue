<template>
    <h1>Address Book</h1>
    <div class="bg-light text-dark rounded p-5">
        <div v-show="!store.state.wallet.loggedIn">
            <p>Please connect your wallet</p>
            <router-link :to="{ name: 'Connect' }" class="btn btn-lg text-light btn-primary" active-class="active">CONNECT</router-link>
        </div>
        <div v-show="store.state.wallet.loggedIn">
            <dl class="row">
                <dt class="col-sm-3">Claim Contract</dt>
                <dd class="col-sm-9"><a href="{{ store.state.settings.block_explorer_url }}/address/{{ store.state.settings.claim_address }}">{{ store.state.settings.claim_address }}</a></dd>
                <dt class="col-sm-3">Downline NFT Contract</dt>
                <dd class="col-sm-9"><a href="{{ store.state.settings.block_explorer_url }}/address/{{ store.state.settings.downline_address }}">{{ store.state.settings.downline_address }}</a></dd>
                <dt class="col-sm-3">Presale NFT Contract</dt>
                <dd class="col-sm-9"><a href="{{ store.state.settings.block_explorer_url }}/address/{{ store.state.settings.presale_address }}">{{ store.state.settings.presale_address }}</a></dd>
                <dt class="col-sm-3">Swap Contract</dt>
                <dd class="col-sm-9"><a href="{{ store.state.settings.block_explorer_url }}/address/{{ store.state.settings.swap_address }}">{{ store.state.settings.swap_address }}</a></dd>
                <dt class="col-sm-3">Token Contract</dt>
                <dd class="col-sm-9"><a href="{{ store.state.settings.block_explorer_url }}/address/{{ store.state.settings.token_address }}">{{ store.state.settings.token_address }}</a></dd>
                <dt class="col-sm-3">Payment Contract</dt>
                <dd class="col-sm-9"><a href="{{ store.state.settings.block_explorer_url }}/address/{{ store.state.settings.payment_address }}">{{ store.state.settings.payment_address }}</a></dd>
                <dt class="col-sm-3">Vault Contract</dt>
                <dd class="col-sm-9"><a href="{{ store.state.settings.block_explorer_url }}/address/{{ store.state.settings.vault_address }}">{{ store.state.settings.vault_address }}</a></dd>
            </dl>
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
