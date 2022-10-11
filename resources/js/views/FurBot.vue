<template>
    <div>
        <h1>Furbot</h1>
        <p class="mb-5">Furbot description goes here.</p>
        <div class="row flex-row-reverse gx-5">
            <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
                <div v-show="loading" class="text-center">
                    <div class="spinner-border m-5" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div v-show="!loading">
                    <h5>Buy $FURBOT</h5>
                    <div class="form-group">
                        <label for="buy-quantity">Quantity</label>
                        <input v-model="quantity" type="number" class="form-control" id="buy-quantity"/>
                    </div>
                    <button @click="buy" class="btn btn-sm btn-info btn-block mb-2">Buy ({{ price * quantity }} USDC)</button>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="../../images/usdc.svg" class="mx-auto d-block mb-3" alt="USDC" width="75" height="75"/>
                                <p class="card-title">Cost Per NFT</p>
                                <p class="card-text"><strong>{{ price }} USDC</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="../../images/fur.svg" class="mx-auto d-block mb-3" alt="FUR" width="75" height="75"/>
                                <p class="card-title">NFTs Owned</p>
                                <p class="card-text"><strong>{{ nftsOwned }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
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
        const store = useStore();
        const alerts = useAlerts();
        const loading = ref(true);
        const nftsOwned = ref(0);
        const quantity = ref(0);
        const price = ref(200);

        addEventListener("refresh", async () => {
            await update();
        });

        onMounted(async () => {
            update();
        });

        const furbotContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.furbot_abi), store.state.settings.furbot_address);
        }

        const paymentContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.payment_abi), store.state.settings.payment_address);
        }

        const update = async () => {
            loading.value = true;
            try {
                const contract = furbotContract();
                nftsOwned.value = await contract.methods.balanceOf(store.state.wallet.address).call();
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        const buy = async () => {
            loading.value = true;
            try {
                const furbot = furbotContract();
                const payment = paymentContract();
                const gasPriceMultiplier = 1.2;
                const gasMultiplier = 1.2;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const amount = BigInt(quantity.value * price.value * "1000000000000000000");
                const approveGas = Math.round(await payment.methods.approve(store.state.settings.furbot_address, amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                await payment.methods.approve(store.state.settings.furbot_address, amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                const buyGas = Math.round(await furbot.methods.buy(quantity.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                await furbot.methods.buy(quantity.value).send({from: store.state.wallet.address, gasPrice: gasPrice, gas: buyGas });
                alerts.success("NFTs purchased successfully!");
                update();
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        return {
            store,
            availableDividends,
            nftsOwned,
            quantity,
            price,
            buy,
        }
    }
}
</script>
