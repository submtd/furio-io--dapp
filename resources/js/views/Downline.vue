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
                    <h2>Buy Downline NFTs</h2>
                    <div class="form-group">
                        <label for="buy-quantity">Quantity</label>
                        <input v-model="buyQuantity" :max="available" min="0" type="number" class="form-control" id="buy-quantity"/>
                    </div>
                    <button @click="buy" class="btn btn-lg btn-info btn-block mb-2">Buy ({{ buyQuantity * 5 }} $FUR)</button>
                </div>
                <div v-show="owned > 0">
                    <h2>Sell Downline NFTs</h2>
                    <div class="form-group">
                        <label for="sell-quantity">Quantity</label>
                        <input v-model="sellQuantity" :max="owned" min="0" type="number" class="form-control" id="sell-quantity"/>
                    </div>
                    <button @click="sell" class="btn btn-lg btn-info btn-block mb-2">Sell ({{ sellQuantity * 4 }}</button>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Max Supply</p>
                            <p class="card-text"><strong>{{ maxSupply }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Total in Circulation</p>
                            <p class="card-text"><strong>{{ totalSupply }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">NFTs Owned</p>
                            <p class="card-text"><strong>{{ owned }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Team Wallet</p>
                            <p class="card-text"><strong>{{ teamWallet }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Direct Referrals</p>
                            <p class="card-text"><strong>{{ referrals }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Rewarded</p>
                            <p class="card-text"><strong>{{ rewarded }} $FUR</strong></p>
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
        const maxSupply = ref(0);
        const totalSupply = ref(0);
        const owned = ref(0);
        const buyQuantity = ref(0);
        const sellQuantity = ref(0);
        const participant = ref(null);

        const available = computed(() => {
            const remaining = maxSupply.value - totalSupply.value;
            const ableToBuy = 15 - owned.value;
            if(ableToBuy > remaining) {
                return remaining;
            }
            return ableToBuy;
        });

        const referrals = computed(() => {
            if(!participant.value) {
                return 0;
            }
            return participant.value.directReferrals;
        });

        const rewarded = computed(() => {
            if(!participant.value) {
                return 0;
            }
            return displayCurrency.format(participant.value.awarded);
        });

        const teamWallet = computed(() => {
            const tw = "No";
            if(!participant.value) {
                return tw;
            }
            if(participant.value.teamWallet) {
                tw = "Yes";
            }
            return tw;
        })

        onMounted(async () => {
            await update();
        });

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
                const contract = downlineContract();
                const vault = vaultContract();
                totalSupply.value = await contract.methods.totalSupply().call();
                maxSupply.value = await contract.methods.maxSupply().call();
                owned.value = await contract.methods.balanceOf(store.state.wallet.address).call();
                participant.value = await vault.methods.getParticipant(store.state.wallet.address).call();
                buyQuantity.value = 15 - owned.value;
                sellQuantity.value = owned.value;
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
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const downline = downlineContract();
                const gasPriceMultiplier = 1.5;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await downline.methods.sell(sellQuantity.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await downline.methods.sell(sellQuantity.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await update();
            loading.value = false;
        }

        return {
            loading,
            totalSupply,
            maxSupply,
            owned,
            buyQuantity,
            sellQuantity,
            teamWallet,
            available,
            buy,
            sell,
            referrals,
            rewarded,
        }
    }

}
</script>
