<template>
    <h1>Vault</h1>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div class="form-group">
                <label for="quantity">Deposit $FUR</label>
                <input v-model="quantity" type="number" class="form-control" id="quantity"/>
            </div>
            <button @click="deposit" class="btn btn-lg btn-info btn-block mb-2">Deposit</button>
            <div class="card-group mt-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Compound</h5>
                        <button @click="compound" class="btn btn-sm btn-info btn-block">Compound</button>
                    </div>
                </div>
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Claim</h5>
                        <button @click="claim" class="btn btn-sm btn-secondary btn-block">Claim</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Initial Deposit</p>
                            <p class="card-text"><strong>{{ initialDepositDisplay }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Total Deposit</p>
                            <p class="card-text"><strong>{{ totalDepositDisplay }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Claimed</p>
                            <p class="card-text"><strong>{{ totalClaimDisplay }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Available Rewards</p>
                            <p class="card-text"><strong>{{ rewardAvailableDisplay }}</strong></p>
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
        const initialDeposit = ref(0);
        const totalDeposit = ref(0);
        const totalClaim = ref(0);
        const rewardAvailable = ref(0);
        const quantity = ref(0);
        const balance = ref(0);

        const initialDepositDisplay = computed(() => {
            return displayCurrency.format(initialDeposit.value);
        });

        const totalDepositDisplay = computed(() => {
            return displayCurrency.format(totalDeposit.value);
        });

        const totalClaimDisplay = computed(() => {
            return displayCurrency.format(totalClaim.value);
        });

        const rewardAvailableDisplay = computed(() => {
            return displayCurrency.format(rewardAvailable.value);
        });

        onMounted(async () => {
            await update();
        });

        const update = async () => {
            try {
                const contract = vaultContract();
                initialDeposit.value = await contract.methods.initialDeposit(store.state.wallet.address).call();
                totalDeposit.value = await contract.methods.totalDeposit(store.state.wallet.address).call();
                totalClaim.value = await contract.methods.totalClaim(store.state.wallet.address).call();
                rewardAvailable.value = await contract.methods.rewardAvailable(store.state.wallet.address).call();
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
                const gasMultipler = 1;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const amount = BigInt(quantity.value) * BigInt("1000000000000000000");
                const approveGas = Math.round(await token.methods.approve(store.state.settings.vault_address, amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                await token.methods.approve(store.state.settings.vault_address, amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                const gas = Math.round(await contract.methods.deposit(amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultipler);
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
                const gasMultipler = 1;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await contract.methods.compound().estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultipler);
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
                const gasMultipler = 1;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await contract.methods.claim().estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultipler);
                const result = await contract.methods.claim().send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await claim();
        }

        return {
            quantity,
            initialDeposit,
            initialDepositDisplay,
            totalDeposit,
            totalDepositDisplay,
            totalClaim,
            totalClaimDisplay,
            rewardAvailable,
            rewardAvailableDisplay,
            deposit,
            compound,
            claim,
        }
    }
}
</script>
