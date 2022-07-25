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
                <h5>{{ name }}</h5>
                <p>{{ description }}</p>
            </div>
        </div>
        <div class="col-lg-5">
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import useAlerts from "../composables/useAlerts";

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const loading = ref(false);
        const initiative = ref(null);

        const voteContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.vote_abi), store.state.settings.vote_address);
        }

        const name = computed(() => {
            if(! initiative.value) {
                return "";
            }
            return initiative.value[0];
        });

        const description = computed(() => {
            if(! initiative.value) {
                return "";
            }
            return initiative.value[1];
        });

        onMounted(async () => {
            await update();
        });

        const update = async () => {
            loading.value = true;
            try {
                const contract = voteContract();
                initiative.value = await contract.methods.getInitiative(1).call();
                console.log(initiative.value);
            } catch (error) {
                alert(error.message);
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        return {
            loading,
            name,
            description,
        }
    }

}
</script>
