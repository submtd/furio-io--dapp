<template>
    <h1>Vault</h1>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div class="form-group">
                <label for="quantity">Deposit $FUR</label>
                <input v-model="quantity" type="number" class="form-control" id="quantity"/>
            </div>
            <button @click="deposit" class="btn btn-lg btn-info btn-block mb-2">Deposit</button>
            <div class="row mt-3">
                <div class="col-6">
                    <button @click="compound" class="btn btn-sm btn-info btn-block">Compound {{ availableDisplay }} $FUR</button>
                </div>
                <div class="col-6">
                    <button @click="claim" class="btn btn-sm btn-secondary btn-block">Claim {{ availableDisplay }} $FUR</button>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Vault Balance</p>
                            <p class="card-text"><strong>{{ vaultBalanceDisplay }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Claimed</p>
                            <p class="card-text"><strong>{{ claimedDisplay }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Reward Rate</p>
                            <p class="card-text"><strong>{{ rewardRate }}%</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Available Rewards</p>
                            <p class="card-text"><strong>{{ availableDisplay }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex';
import useAlerts from '../composables/useAlerts';
import useDisplayCurrency from '../composables/useDisplayCurrency';

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const displayCurrency = useDisplayCurrency();
        const vaultBalance = ref(0);
        const claimed = ref(0);
        const rewardRate = ref(0);
        const available = ref(0);
        const quantity = ref(0);
        const balance = ref(0);

        const vaultBalanceDisplay = computed(() => {
            return displayCurrency.format(vaultBalance.value);
        });

        const claimedDisplay = computed(() => {
            return displayCurrency.format(claimed.value);
        });

        const availableDisplay = computed(() => {
            return displayCurrency.format(available.value);
        });

        onMounted(async () => {
            await update();
        });

        const update = async () => {
            try {
                const contract = vaultContract();
                vaultBalance.value = await contract.methods.totalDeposit(store.state.wallet.address).call();
                claimed.value = await contract.methods.totalClaim(store.state.wallet.address).call();
                available.value = await contract.methods.rewardAvailable(store.state.wallet.address).call();
                rewardRate.value = await contract.methods.rewardPercent(store.state.wallet.address).call() / 100;
                const token = tokenContract();
                balance.value = await token.methods.balanceOf(store.state.wallet.address).call();
            } catch (error) {
                alerts.danger(error.message);
            }
        }

        const vaultContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.vault_abi), store.state.settings.vault_address);
        }

        const tokenContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.token_abi), store.state.settings.token_address);
        }

        const deposit = async () => {
            try {
                const contract = vaultContract();
                const token = tokenContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const amount = BigInt(quantity.value * 1000000000000000000);
                const allowance = await token.methods.allowance(store.state.wallet.address, store.state.settings.vault_address).call();
                if(allowance < amount) {
                    const approveGas = Math.round(await token.methods.approve(store.state.settings.vault_address, amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                    await token.methods.approve(store.state.settings.vault_address, amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                }
                const gas = Math.round(await contract.methods.deposit(amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await contract.methods.deposit(amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await update();
        }

        const compound = async () => {
            try {
                const contract = vaultContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await contract.methods.compound().estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await contract.methods.compound().send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await update();
        }

        const claim = async () => {
            try {
                const contract = vaultContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await contract.methods.claim().estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await contract.methods.claim().send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await update();
        }

        return {
            quantity,
            vaultBalance,
            vaultBalanceDisplay,
            claimed,
            claimedDisplay,
            rewardRate,
            available,
            availableDisplay,
            deposit,
            compound,
            claim,
        }
    }
}
</script>
