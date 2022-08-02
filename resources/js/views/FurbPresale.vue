<template>
    <h1>$FURB Presale</h1>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div v-show="loading" class="text-center">
                <div class="spinner-border m-5" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div v-show="!loading">
                <div v-show="!canBuy">
                    <p>You are not able to purchase $FURB at this time.</p>
                </div>
                <div v-show="canBuy">
                    <h5>Buy $FURB</h5>
                    <div class="form-group">
                        <label for="buy-quantity">Quantity</label>
                        <input v-model="quantity" :max="available" min="0" type="number" class="form-control" id="buy-quantity"/>
                    </div>
                    <button @click="buy" class="btn btn-sm btn-info btn-block mb-2">Buy ({{ displayPrice * quantity }} USDC)</button>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Sold</p>
                            <p class="card-text"><strong>{{ sold }} out of {{ maxForSale }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Purchased</p>
                            <p class="card-text"><strong>{{ purchased }} out of {{ maxPerAddress }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Minimum Reward Rate</p>
                            <p class="card-text"><strong>{{ minRewardRate / 100 }}%</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Minimum Vault Balance</p>
                            <p class="card-text"><strong>{{ displayCurrency.format(minVaultBalance) }} $FUR</strong></p>
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
import useDisplayCurrency from '../composables/useDisplayCurrency';

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const displayCurrency = useDisplayCurrency();
        const loading = ref(false);
        const type = ref(0);
        const sold = ref(0);
        const purchased = ref(0);
        const maxForSale = ref(0);
        const maxPerAddress = ref(0);
        const minRewardRate = ref(0);
        const minVaultBalance = ref(0);
        const price = ref(0);
        const canBuy = ref(false);
        const quantity = ref(0);

        const displayPrice = computed(() => {
            return displayCurrency.format(price.value);
        });

        const available = computed(() => {
            let totalAvailable = maxForSale.value - sold.value;
            let addressAvailable = maxPerAddress.value - purchased.value;
            if(totalAvailable > addressAvailable) {
                return addressAvailable;
            } else {
                return totalAvailable;
            }
        });

        addEventListener("refresh", async () => {
            await update();
        });

        onMounted(async () => {
            await update();
        });

        const presaleContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.furbpresale_abi), store.state.settings.furbpresale_address);
        }

        const vaultContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.vault_abi), store.state.settings.vault_address);
        }

        const paymentContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.payment_abi), store.state.settings.payment_address);
        }

        const update = async () => {
            loading.value = true;
            try {
                const presale = presaleContract();
                const vault = vaultContract();
                type.value = await presale.methods.presaleType().call();
                if(type.value > 0) {
                    sold.value = displayCurrency.format(await presale.methods.getSold().call());
                    purchased.value = displayCurrency.format(await presale.methods.getPurchased(store.state.wallet.address).call());
                }
                if(type.value > 0 && type.value < 6) {
                    canBuy.value = true;
                    maxForSale.value = displayCurrency.format(await presale.methods.getMaxForSale(type.value).call());
                    maxPerAddress.value = displayCurrency.format(await presale.methods.getMaxPerAddress(type.value).call());
                    minRewardRate.value = await presale.methods.getMinRewardRate(type.value).call();
                    minVaultBalance.value = displayCurrency.format(await presale.methods.getMinVaultBalance(type.value).call());
                    price.value = await presale.methods.getPrice(type.value).call();
                    if(sold.value >= maxForSale.value) {
                        alerts.warning("Presale is sold out");
                        canBuy.value = false;
                    }
                    if(purchased.value >= maxPerAddress.value) {
                        alerts.warning("You have reached your maximum purchase limit");
                        canBuy.value = false;
                    }
                    if(minRewardRate.value > 0) {
                        const rewardRate = vault.methods.rewardRate(store.state.wallet.address).call();
                        if(rewardRate < minRewardRate) {
                            alerts.warning("You need to increase your reward rate to " + minRewardRate / 100 + "% to buy $FURB.");
                            canBuy.value = false;
                        }
                    }
                    if(minVaultBalance.value > 0) {
                        const vaultBalance = vault.methods.participantBalance(store.state.wallet.address).call();
                        if(vaultBalance < minVaultBalance) {
                            alerts.warning("You need to increase your vault balance to " + displayCurrency.format(minVaultBalance) + " to buy $FURB.");
                            canBuy.value = false;
                        }
                    }
                }
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        const buy = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const presale = presaleContract();
                const payment = paymentContract();
                const gasPriceMultiplier = 1.2;
                const gasMultiplier = 1.2;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const amount = BigInt(quantity.value * price.value);
                alert(amount);
                const allowance = await payment.methods.allowance(store.state.wallet.address, store.state.settings.furbpresale_address).call();
                if(allowance < amount) {
                    const approveGas = Math.round(await payment.methods.approve(store.state.settings.furbpresale_address, amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                    await payment.methods.approve(store.state.settings.furbpresale_address, amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                }
                const gas = Math.round(await presale.methods.presale(quantity.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await presale.methods.presale(quantity.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
                alerts.clear();
            } catch (error) {
                alerts.danger(error.message);
            }
            dispatchEvent(new Event("refresh"));
            loading.value = false;
        }

        return {
            store,
            displayCurrency,
            loading,
            type,
            sold,
            purchased,
            maxForSale,
            maxPerAddress,
            minRewardRate,
            minVaultBalance,
            displayPrice,
            canBuy,
            buy,
            quantity,
            available,
        }
    }

}
</script>
