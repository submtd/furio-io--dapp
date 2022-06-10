<template>
    <h1>{{ shortAddress }}</h1>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div v-show="loading" class="text-center">
                <div class="spinner-border m-5" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div v-show="!loading">
                <table class="table">
                    <tbody>
                        <tr>
                            <th scope="row">Participant Status</th>
                            <td>{{ participantStatusDisplay }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Reward Rate</th>
                            <td>{{ rewardRate }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Vault Balance</th>
                            <td>{{ displayCurrency.format(getProperty("balance")) }} $FUR</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Vault Balance</p>
                            <p class="card-text"><strong>{{ displayCurrency.format(getProperty("balance")) }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Deposited</p>
                            <p class="card-text"><strong>{{ displayCurrency.format(getProperty("deposited")) }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Compounded</p>
                            <p class="card-text"><strong>{{ displayCurrency.format(getProperty("compounded")) }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Claimed</p>
                            <p class="card-text"><strong>{{ displayCurrency.format(getProperty("claimed")) }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Referral Rewards</p>
                            <p class="card-text"><strong>{{ displayCurrency.format(getProperty("awarded")) }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Taxes Paid</p>
                            <p class="card-text"><strong>{{ displayCurrency.format(getProperty("taxed")) }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Airdrops Sent</p>
                            <p class="card-text"><strong>{{ displayCurrency.format(getProperty("airdropSent")) }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Airdrops Received</p>
                            <p class="card-text"><strong>{{ displayCurrency.format(getProperty("airdropReceived")) }} $FUR</strong></p>
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
                console.log(participant.value);
            } catch (error) {
                alerts.danger(error.message);
            }
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
        }
    }

}
</script>
