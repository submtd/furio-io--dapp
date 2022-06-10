<template>
    <h1>{{ shortAddress }}</h1>
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
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Vault Balance</p>
                            <p class="card-text"><strong>{{ get("balance") }}</strong></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import useAlerts from "../composables/useAlerts";

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const route = useRoute();
        const loading = ref(true);
        const address = ref(null);
        const participant = ref(null);

        const shortAddress = computed(() => {
            if(!address.value) {
                return null;
            }
            return address.value.substr(0, 4) + "..." + address.value.substr(-4);
        });

        onMounted(async () => {
            address.value = route.params.address;
            await update();
        });

        const get = (property) => {
            if(!participant.value) {
                return null;
            }
            if(!participant.value.property) {
                return null;
            }
            return participant.value.property;
        }

        const downlineContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.downline_abi), store.state.settings.downline_address);
        }

        const tokenContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.token_abi), store.state.settings.token_address);
        }

        const vaultContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.vault_abi), store.state.settings.vault_address);
        }

        const update = async () => {
            loading.value = true;
            try {
                const vault = vaultContract();
                participant.value = await vault.methods.getParticipant(address.value).call();
                console.log(participant.value);
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        return {
            loading,
            address,
            shortAddress,
            get,
        }
    }

}
</script>
