<template>
    <div class="bg-light text-dark rounded p-5">
        <h1>Claim</h1>
        <p>Please come back on June 14th for the project launch!</p>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { useStore } from "vuex";
import useAlerts from "../composables/useAlerts";

export default {
    setup () {
        const alerts = useAlerts();
        const store = useStore();

        onMounted(async () => {
            await getNfts();
        });

        const getNfts = async () => {
            try {
                const contract = web3.eth.Contract(JSON.parse(store.state.settings.claim_abi), store.state.settings.claim_address);
                const nfts = await contract.methods.owned(store.state.wallet.address).call();
                console.log(nfts);
            } catch (error) {
                alerts.danger(error.message);
            }
        }
        return {
            getNfts,
        }
    }
}
</script>
