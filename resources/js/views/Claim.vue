<template>
    <div class="bg-light text-dark rounded p-5">
        <h1>Claim</h1>
        <h2>NFTs</h2>
        <div v-for="nft in nfts" class="card" style="width: 18rem;">
            <img src="../../images/furio-presale.png" class="card-img-top" alt="presale"/>
            <div class="card-body">
                {{ nft }}
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

        const nfts = ref([]);

        onMounted(async () => {
            await getNfts();
        });

        const getNfts = async () => {
            try {
                const contract = new web3.eth.Contract(JSON.parse(store.state.settings.claim_abi), store.state.settings.claim_address);
                nfts.value = await contract.methods.owned(store.state.wallet.address).call();
                console.log(nfts);
            } catch (error) {
                alerts.danger(error.message);
            }
        }
        return {
            nfts,
        }
    }
}
</script>
