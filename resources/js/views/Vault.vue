<template>
    <h1>Vault</h1>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div v-show="!loading && !statusDrop">
                <div class="form-group">
                    <label for="quantity">Deposit $FUR</label>
                    <input v-model="quantity" type="number" class="form-control" id="quantity"/>
                </div>
                <button @click="deposit" class="btn btn-lg btn-info btn-block mb-2">Deposit</button>
                <div class="row mt-3">
                    <div class="col-6">
                        <button @click="compound" class="btn btn-lg btn-info btn-block">Compound {{ availableDisplay }}</button>
                    </div>
                    <div class="col-6">
                        <button @click="claim" class="btn btn-lg btn-secondary btn-block">Claim {{ availableDisplay }}</button>
                    </div>
                </div>
            </div>
            <div v-show="!loading && statusDrop">
                <p>Claiming now will lower your reward rate from <strong>{{ rewardRate }}%</strong> to <strong>{{ newRewardRate }}%</strong>. Are you <strong><em>sure</em></strong> you want to continue?</p>
                <div class="row">
                    <div class="col-sm-6">
                        <button @click="cancel" class="btn btn-lg btn-info btn-block mb-2">Cancel</button>
                    </div>
                    <div class="col-sm-6">
                        <button @click="claim" class="btn btn-lg btn-danger btn-block mb-2">Continue</button>
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
                            <p class="card-title">Balance</p>
                            <p class="card-text"><strong>{{ depositedDisplay }} $FUR</strong></p>
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
                            <p class="card-title">Participant Status</p>
                            <p class="card-text"><strong>{{ participantStatusDisplay }}</strong></p>
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
        const rewardRate = ref(0);
        const newRewardRate = ref(0);
        const available = ref(0);
        const participantStatus = ref(0);
        const quantity = ref(0);
        const balance = ref(0);
        const loading = ref(false);
        const statusDrop = ref(false);

        const stats = ref(null);
        const properties = ref(null);
        const participant = ref(null);

        const depositedDisplay = computed(() => {
            if(!participant.value) {
                return 0;
            }
            return displayCurrency.format(participant.value.balance);
        });

        const claimedDisplay = computed(() => {
            if(!participant.value) {
                return 0;
            }
            return displayCurrency.format(participant.value.claimed);
        });

        const availableDisplay = computed(() => {
            if(!available.value) {
                return 0;
            }
            return displayCurrency.format(available.value);
        });

        const participantStatusDisplay = computed(() => {
            switch(participantStatus.value) {
                case "1":
                    return 'Negative';
                case "2":
                    return 'Neutral';
                case "3":
                    return 'Positive';
            }
        });

        onMounted(async () => {
            await update();
        });

        const update = async () => {
            loading.value = true;
            try {
                const contract = vaultContract();
                stats.value = await contract.methods.getStats().call();
                properties.value = await contract.methods.getProperties().call();
                participant.value = await contract.methods.getParticipant(store.state.wallet.address).call();
                rewardRate.value = await contract.methods.rewardRate(store.state.wallet.address).call() / 100;
                available.value = await contract.methods.availableRewards(store.state.wallet.address).call();
                participantStatus.value = await contract.methods.participantStatus(store.state.wallet.address).call();
                console.log(stats.value);
                console.log(properties.vaue);
                console.log(participant.value);
                const token = tokenContract();
                balance.value = await token.methods.balanceOf(store.state.wallet.address).call();
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        const vaultContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.vault_abi), store.state.settings.vault_address);
        }

        const tokenContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.token_abi), store.state.settings.token_address);
        }

        const deposit = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
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
            loading.value = false;
        }

        const compound = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
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
            loading.value = false;
        }

        const claim = async () => {
            try {
                const contract = vaultContract();
                newRewardRate.value = await contract.methods.claimPrecheck(store.state.wallet.address).call() / 100;
                if(newRewardRate.value < rewardRate.value && !statusDrop.value) {
                    statusDrop.value = true;
                    return;
                }
                alerts.warning("waiting on response from wallet");
                loading.value = true;
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
            statusDrop.value = false;
            loading.value = false;
        }

        const cancel = () => {
            statusDrop.value = false;
        }

        return {
            quantity,
            depositedDisplay,
            claimedDisplay,
            rewardRate,
            newRewardRate,
            available,
            availableDisplay,
            participantStatus,
            participantStatusDisplay,
            referrer,
            deposit,
            compound,
            claim,
            cancel,
            loading,
            statusDrop,
            stats,
            properties,
            participant,
        }
    }
}
</script>
