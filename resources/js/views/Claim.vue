<template>
    <div class="bg-light text-dark rounded p-5">
        <h1>Claim</h1>
        <div class="row rows-cols-1 rows-cols-md-4">
            <div class="col mb-4">
                <p>You have <strong>{{ available }}</strong> $FUR tokens available to claim.</p>
            </div>
        </div>
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

        const available = ref(0);

        onMounted(async () => {
            await getNfts();
        });

        const getNfts = async () => {
            try {
                const contract = new web3.eth.Contract(JSON.parse(store.state.settings.claim_abi), store.state.settings.claim_address);
                available.value = await contract.methods.getOwnerValue(store.state.wallet.address).call();
            } catch (error) {
                alerts.danger(error.message);
            }
        }

        return {
            available,
        }
    }
}
</script>
