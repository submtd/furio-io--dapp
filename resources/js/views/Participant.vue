<template>
    <h1>{{ shortAddress }}</h1>
    <div class="bg-light text-dark rounded p-5 mb-4">
        <div class="row flex-row-reverse gx-5">
            <div class="col-lg-7">
                <div v-show="loading" class="text-center">
                    <div class="spinner-border m-5" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div v-show="!loading && !airdropDisabled">
                    <h2>Send Airdrop</h2>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input v-model="amount" class="form-control" id="amount"/>
                    </div>
                    <button @click="sendAirdrop" class="btn btn-lg btn-info btn-block mb-2">Send Airdrop</button>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th scope="row">Participant Status</th>
                                <td>{{ participantStatusDisplay }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Reward Rate</th>
                                <td>{{ rewardRate }}%</td>
                            </tr>
                            <tr>
                                <th scope="row">Vault Balance</th>
                                <td>{{ displayCurrency.format(getProperty("balance")) }} $FUR</td>
                            </tr>
                            <tr>
                                <th scope="row">Deposited</th>
                                <td>{{ displayCurrency.format(getProperty("deposited")) }} $FUR</td>
                            </tr>
                            <tr>
                                <th scope="row">Compounded</th>
                                <td>{{ displayCurrency.format(getProperty("compounded")) }} $FUR</td>
                            </tr>
                            <tr>
                                <th scope="row">Claimed</th>
                                <td>{{ displayCurrency.format(getProperty("claimed")) }} $FUR</td>
                            </tr>
                            <tr>
                                <th scope="row">Referral Rewards</th>
                                <td>{{ displayCurrency.format(getProperty("awarded")) }} $FUR</td>
                            </tr>
                            <tr>
                                <th scope="row">Taxes Paid</th>
                                <td>{{ displayCurrency.format(getProperty("taxed")) }} $FUR</td>
                            </tr>
                            <tr>
                                <th scope="row">Airdrops Sent</th>
                                <td>{{ displayCurrency.format(getProperty("airdropSent")) }} $FUR</td>
                            </tr>
                            <tr>
                                <th scope="row">Airdrops Received</th>
                                <td>{{ displayCurrency.format(getProperty("airdropReceived")) }} $FUR</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import useAlerts from "../composables/useAlerts";
import useDisplayCurrency from "../composables/useDisplayCurrency";

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const displayCurrency = useDisplayCurrency();
        const route = useRoute();
        const loading = ref(true);
        const address = ref(null);
        const participant = ref(null);
        const participantStatus = ref(2);
        const rewardRate = ref(0);
        const available = ref(0);
        const amount = ref(0);

        const shortAddress = computed(() => {
            if(!address.value) {
                return null;
            }
            return address.value.substr(0, 4) + "..." + address.value.substr(-4);
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

        const airdropDisabled = computed(() => {
            if(!participant.value) {
                return true;
            }
            if(address.value == store.state.wallet.address) {
                return true;
            }
            if(available.value == 0) {
                return true;
            }
            return false;
        });


        onMounted(async () => {
            address.value = route.params.address;
            await update();
        });

        const getProperty = (property) => {
            if(!participant.value) {
                return null;
            }
            if(!participant.value[property]) {
                return null;
            }
            return participant.value[property];
        }

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
                const vault = vaultContract();
                participant.value = await vault.methods.getParticipant(address.value).call();
                rewardRate.value = await vault.methods.rewardRate(address.value).call() / 100;
                participantStatus.value = await vault.methods.participantStatus(address.value).call();
                available.value = await vault.methods.availableRewards(store.state.wallet.address).call();
                amount.value = displayCurrency.format(available.value);
                console.log(participant.value);
            } catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        }

        const sendAirdrop = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const contract = vaultContract();
                const gasPriceMultiplier = 1.5;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const sendAmount = BigInt(amount.value * 1000000000000000000);
                const gas = Math.round(await contract.methods.airdrop(address.value, sendAmount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await contract.methods.airdrop(address.value, sendAmount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await update();
            loading.value = false;
        }

        return {
            loading,
            address,
            shortAddress,
            getProperty,
            displayCurrency,
            participantStatusDisplay,
            rewardRate,
            available,
            amount,
            sendAirdrop,
            airdropDisabled,
        }
    }

}
</script>
