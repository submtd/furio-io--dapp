<template>
    <h1>Furpool</h1>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div v-show="!loading">
                <div class="row">
                    <div class="form-group col-8">
                        <label for="quantity">Amount</label>
                        <input v-model="quantity" type="number" class="form-control" id="quantity"/>
                    </div>
                    <div class="form-group col-4">
                        <label for="duration">Duration</label>
                        <select v-model="duration" class="form-control" id="duration">
                            <option value="0">No limit</option>
                            <option value="1">30 days</option>
                            <option value="3">60 days</option>
                            <option value="4">90 days</option>
                        </select>
                    </div>
                </div>
                <button @click="stake" class="btn btn-lg btn-info btn-block mb-2">Stake</button>
                <div class="row mt-3">
                    <div class="col-6">
                        <button @click="claim" class="btn btn-lg btn-info btn-block">Claim {{ available }}</button>
                    </div>
                    <div class="col-6">
                        <button @click="withdraw" class="btn btn-lg btn-secondary btn-block">Withdraw {{ balance }}</button>
                    </div>
                </div>
            </div>
            <div v-show="loading" class="text-center">
                <div class="spinner-border m-5" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Total Stakers</p>
                            <p class="card-text"><strong>{{ totalStakers }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Total Staked</p>
                            <p class="card-text"><strong>{{ totalStaked }} USDC</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useStore } from 'vuex';
import useAlerts from '../composables/useAlerts';
import useBalances from '../composables/useBalances';
import useDisplayCurrency from '../composables/useDisplayCurrency';

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const balances = useBalances();
        const displayCurrency = useDisplayCurrency();
        const loading = ref(false);
        const quantity = ref(0);
        const duration = ref(0);
        const available = ref(0);
        const balance = ref(0);
        const totalStakers = ref(0);
        const totalStaked = ref(0);

        addEventListener("refresh", async () => {
            await update();
        });

        onMounted(async () => {
            await update();
        });

        const stakingContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.lpstaking_abi), store.state.settings.lpstaking_address);
        }

        const paymentContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.payment_abi), store.state.settings.payment_address);
        }

        const factoryContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.factory_abi), store.state.settings.factory_address);
        }

        const update = async () => {
            loading.value = true;
            try {
                const contract = stakingContract();
                available.value = await contract.methods.pendingReward(store.state.wallet.address).call();
                balance.value = await contract.methods.balanceOf(store.state.wallet.address).call();
                totalStakers.value = await contract.methods.stakerNum().call();
                totalStaked.value = await contract.methods.totalStakingAmount().call();
            } catch (error) {
                //alerts.danger(error.message);
            }
            balances.refresh();
            loading.value = false;
        }

        const stake = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const staking = stakingContract();
                const payment = paymentContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const amount = BigInt(quantity.value * "1000000000000000000");
                const allowance = await payment.methods.allowance(store.state.wallet.address, store.state.settings.lpstaking_address).call();
                if(allowance < amount) {
                    const approveGas = Math.round(await payment.methods.approve(store.state.settings.lpstaking_address, amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                    await payment.methods.approve(store.state.settings.lpstaking_address, amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                }
                const gas = Math.round(await staking.methods.stake(store.state.settings.payment_address, amount, duration.value).estimateGas({ from: store.state.wallet.address, value: "0", gasPrice: gasPrice }) * gasMultiplier);
                const result = await staking.methods.stake(store.state.settings.payment_address, amount, duration.value).send({ from: store.state.wallet.address, value: "0", gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
                dispatchEvent(new Event("refresh"));
                await update();
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        const claim = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const contract = stakingContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await contract.methods.claimRewards().estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await contract.methods.claimRewards().send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            dispatchEvent(new Event("refresh"));
            await update();
            loading.value = false;
        }

        const withdraw = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const contract = stakingContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await contract.methods.unstake().estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await contract.methods.unstake().send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            dispatchEvent(new Event("refresh"));
            await update();
            loading.value = false;
        }

        return {
            loading,
            displayCurrency,
            quantity,
            duration,
            available,
            balance,
            totalStakers,
            totalStaked,
            stake,
            claim,
            withdraw,
        }
    }
}
</script>
