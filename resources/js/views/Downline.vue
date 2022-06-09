<template>
    <h1>Downline</h1>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div v-show="loading" class="text-center">
                <div class="spinner-border m-5" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div v-show="!loading">
                <div v-show="available">
                    <h2>Buy</h2>
                    <div class="form-group">
                        <label for="buy-quantity">Quantity</label>
                        <input v-model="buyQuantity" :max="available" min="0" type="number" class="form-control" id="buy-quantity"/>
                    </div>
                    <button @click="buy" class="btn btn-lg btn-info btn-block mb-2">Buy</button>
                </div>
                <div v-show="owned">
                    <h2>Sell</h2>
                    <div class="form-group">
                        <label for="sell-quantity">Quantity</label>
                        <input v-model="sellQuantity" :max="owned" min="0" type="number" class="form-control" id="sell-quantity"/>
                    </div>
                    <button @click="sell" class="btn btn-lg btn-info btn-block mb-2">Sell</button>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Total Supply</p>
                            <p class="card-text"><strong>{{ totalSupply }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Owned</p>
                            <p class="card-text"><strong>{{ owned }}</strong></p>
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
        const totalSupply = ref(0);
        const owned = ref(0);
        const buyQuantity = ref(0);
        const sellQuantity = ref(0);

        const available = computed(() => {
            return 15 - owned;
        });

        onMounted(async () => {
            await update();
        });

        const downlineContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.downline_abi), store.state.settings.downline_address);
        }

        const update = async () => {
            loading.value = true;
            try {
                const contract = downlineContract();
                totalSupply.value = await contract.methods.totalSupply().call();
                owned.value = await contract.methods.balanceOf(store.state.wallet.address).call();
                buyQuantity.value = 15 - owned.value;
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        const buy = async () => {

        }

        const sell = async () => {

        }

        return {
            loading,
            totalSupply,
            owned,
            buyQuantity,
            sellQuantity,
            available,
            buy,
            sell,
        }
    }

}
</script>
