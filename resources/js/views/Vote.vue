<template>
    <h1>Vote</h1>
    <p class="mb-t">Cast your vote on Furio initiatives.</p>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div v-show="loading" class="text-center">
                <div class="spinner-border m-5" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div v-show="!loading">
            </div>
        </div>
        <div class="col-lg-5">
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { useStore } from "vuex";
import useAlerts from "../composables/useAlerts";

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const loading = ref(false);

        const voteContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.vote_abi), store.state.settings.vote_address);
        }

        onMounted(async () => {
            await update();
        });

        const update = async () => {
            loading.value = true;
            try {
                const contract = voteContract();
                const initiative = await contract.methods.getIniative(1).call();
                console.log(initiative);
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        return {
        }
    }

}
</script>
