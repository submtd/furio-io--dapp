<template>
    <h1>Airdrop</h1>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <h2>Referrals</h2>
            <ul>
                <li v-for="referral in referrals">{{ referral }}</li>
            </ul>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Airdrops Received</p>
                            <p class="card-text"><strong>{{ received }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Airdrops Sent</p>
                            <p class="card-text"><strong>{{ sent }} $FUR</strong></p>
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
        const loading = ref(false);
        const participant = ref(null);
        const referrals = ref([]);

        const received = computed(() => {
            if(!participant.value) {
                return 0;
            }
            return displayCurrency.format(participant.value.airdropReceived);
        });

        const sent = computed(() => {
            if(!participant.value) {
                return 0;
            }
            return displayCurrency.format(participant.value.airdropSent);
        });

        onMounted(async () => {
            await update();
        });

        const vaultContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.vault_abi), store.state.settings.vault_address);
        }

        const update = async () => {
            loading.value = true;
            try {
                const vault = vaultContract();
                participant.value = await vault.methods.getParticipant(store.state.wallet.address).call();
                referrals.value = await vault.methods.getReferrals(store.state.wallet.address).call();
            } catch (error) {
                alerts.danger(error.message);
                console.log(error.message);
            }
            loading.value = false;
        }

        return {
            loading,
            referrals,
            received,
            sent,
        }
    }
}
</script>
