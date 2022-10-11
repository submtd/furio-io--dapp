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
                <div v-show="!loading && activeSale != 0">
                    <h5>Buy $FURBOT</h5>
                    <div class="form-group">
                        <label for="buy-quantity">Quantity</label>
                        <input v-model="quantity" type="number" class="form-control" id="buy-quantity"/>
                    </div>
                    <button @click="buy" class="btn btn-sm btn-info btn-block mb-2">Buy ({{ displayCurrency.format(activeSalePrice * quantity) }} USDC)</button>
                </div>
                <div v-show="!loading && activeSale == 0">
                    <p>There are no active sales at this time.</p>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="../../images/usdc.svg" class="mx-auto d-block mb-3" alt="USDC" width="75" height="75"/>
                                <p class="card-title">Cost Per NFT</p>
                                <p class="card-text"><strong>{{ displayCurrency.format(activeSalePrice) }} USDC</strong></p>
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
                    <div v-show="nextSale != 0" class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <p class="card-title">Next Sale Start</p>
                                <p class="card-text"><strong>{{ nextSaleInSeconds }}</strong></p>
                            </div>
                        </div>
                    </div>
                    <div v-show="nextSale != 0" class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <p class="card-title">Next Sale Price</p>
                                <p class="card-text"><strong>{{ displayCurrency.format(nextSalePrice) }}</strong></p>
                            </div>
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
import useDisplayCurrency from "../composables/useDisplayCurrency";

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const displayCurrency = useDisplayCurrency();
        const loading = ref(true);
        const nftsOwned = ref(0);
        const quantity = ref(0);
        const blockTime = ref(0);
        const activeSale = ref(0);
        const activeSalePrice = ref(0);
        const activeSaleStart = ref(0);
        const activeSaleEnd = ref(0);
        const activeSaleRestricted = ref(true);
        const nextSale = ref(0);
        const nextSalePrice = ref(0);
        const nextSaleStart = ref(0);
        const nextSaleEnd = ref(0);
        const nextSaleRestricted = ref(true);

        addEventListener("refresh", async () => {
            await update();
        });

        onMounted(async () => {
            update();
        });

        const nextSaleInSeconds = computed(() => {
            return nextSaleStart.value - blockTime.value;
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
                blockTime.value = await contract.methods.getBlockTime().call();
                activeSale.value = await contract.methods.getActiveSale().call();
                activeSalePrice.value = await contract.methods.getActiveSalePrice().call();
                activeSaleStart.value = await contract.methods.getActiveSaleStart().call();
                activeSaleEnd.value = await contract.methods.getActiveSaleEnd().call();
                activeSaleRestricted.value = await contract.methods.getActiveSaleRestricted().call();
                nextSale.value = await contract.methods.getNextSale().call();
                nextSalePrice.value = await contract.methods.getNextSalePrice().call();
                nextSaleStart.value = await contract.methods.getNextSaleStart().call();
                nextSaleEnd.value = await contract.methods.getNextSaleEnd().call();
                nextSaleRestricted.value = await contract.methods.getNextSaleRestricted().call();
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        const buy = async () => {
            loading.value = true;
            try {
                if(activeSale == 0) {
                    alerts.danger("No active sale.");
                    loading.value = false;
                    return;
                }
                const furbot = furbotContract();
                const payment = paymentContract();
                const gasPriceMultiplier = 1.2;
                const gasMultiplier = 1.2;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const amount = BigInt(quantity.value * activeSalePrice.value * "1000000000000000000");
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
            displayCurrency,
            loading,
            nftsOwned,
            quantity,
            buy,
            activeSale,
            activeSalePrice,
            activeSaleStart,
            activeSaleEnd,
            activeSaleRestricted,
            nextSale,
            nextSalePrice,
            nextSaleStart,
            nextSaleEnd,
            nextSaleRestricted,
            nextSaleInSeconds,
        }
    }
}
</script>
