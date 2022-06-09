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
                <div v-show="available > 0">
                    <h2>Buy</h2>
                    <div class="form-group">
                        <label for="buy-quantity">Quantity</label>
                        <input v-model="buyQuantity" :max="available" min="0" type="number" class="form-control" id="buy-quantity"/>
                    </div>
                    <button @click="buy" class="btn btn-lg btn-info btn-block mb-2">Buy</button>
                </div>
                <div v-show="owned > 0">
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
            return 15 - owned.value;
        });

        onMounted(async () => {
            await update();
        });

        const downlineContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.downline_abi), store.state.settings.downline_address);
        }

        const tokenContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.token_abi), store.state.settings.token_address);
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
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const downline = downlineContract();
                const token = tokenContract();
                const gasPriceMultiplier = 1.5;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const amount = BigInt(buyQuantity.value * 5 * 1000000000000000000);
                const allowance = await token.methods.allowance(store.state.wallet.address, store.state.settings.downline_address).call();
                if(allowance < amount) {
                    const approveGas = Math.round(await token.methods.approve(store.state.settings.downline_address, amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                    await token.methods.approve(store.state.settings.downline_address, amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                }
                const gas = Math.round(await downline.methods.buy(buyQuantity.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await downline.methods.buy(buyQuantity.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await update();
            loading.value = false;
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
