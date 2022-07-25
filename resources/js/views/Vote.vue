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
                <p v-show="!active" class="text-muted">This initiative is not active yet.</p>
                <div v-show="active && !voted">
                    <div class="row">
                        <div class="col-md-6">
                            <button @click="voteYes" class="btn btn-lg btn-info btn-block mb-2">Yes</button>
                        </div>
                        <div class="col-md-6">
                            <button @click="voteNo" class="btn btn-lg btn-info btn-block mb-2">No</button>
                        </div>
                    </div>
                </div>
                <p v-show="active && voted" class="text-muted">You have already voted on this initiative.</p>
            </div>
        </div>
        <div class="col-lg-5">
            <div v-show="voted" class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Yes Votes</p>
                            <p class="card-text"><strong>{{ yesVotes }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">No Votes</p>
                            <p class="card-text"><strong>{{ totalVotes - yesVotes }}</strong></p>
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
import useAlerts from "../composables/useAlerts";

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const loading = ref(false);
        const initiative = ref(null);
        const voted = ref(false);

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

        const active = computed(() => {
            if(! initiative.value) {
                return false;
            }
            const timestamp = Date.now() / 1000;
            return timestamp >= initiative.value[2] && timestamp <= initiative.value[3];
        });

        const totalVotes = computed(() => {
            if(! initiative.value) {
                return 0;
            }
            return initiative.value[4];
        });

        const yesVotes = computed(() => {
            if(! initiative.value) {
                return 0;
            }
            return initiative.value[5];
        });

        addEventListener("refresh", async () => {
            await update();
        });

        onMounted(async () => {
            await update();
        });

        const update = async () => {
            loading.value = true;
            try {
                const contract = voteContract();
                initiative.value = await contract.methods.getInitiative(1).call();
                voted.value = await contract.methods.voted(1, store.state.wallet.address).call();
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        const voteYes = async () => {
            await vote(true);
        }

        const voteNo = async () => {
            await vote(false);
        }

        const vote = async (decision) => {
            loading.value = true;
            try {
                const contract = voteContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await contract.methods.vote(1, decision).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await contract.methods.vote(1, decision).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
                await update();
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        return {
            loading,
            name,
            description,
            active,
            voteYes,
            voteNo,
            totalVotes,
            yesVotes,
            voted,
        }
    }

}
</script>
