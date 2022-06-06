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
                            <p class="card-text"><strong>{{ initialDeposit }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Total Deposit</p>
                            <p class="card-text"><strong>{{ totalDeposit }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Claimed</p>
                            <p class="card-text"><strong>{{ totalClaim }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Direct Referrals</p>
                            <p class="card-text"><strong>0</strong></p>
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

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const initialDeposit = ref(0);
        const totalDeposit = ref(0);
        const totalClaim = ref(0);

        const quantity = ref(0);

        onMounted(async () => {
            try {
                const contract = vaultContract();
                initialDeposit.value = await contract.methods.initialDeposit(store.state.wallet.address).call();
                totalDeposit.value = await contract.methods.totalDeposit(store.state.wallet.address).call();
                totalClaim.value = await contract.methods.totalClaim(store.state.wallet.address).call();
            } catch (error) {
                alerts.danger(error.message);
            }
        });

        const vaultContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.vault_abi), store.state.settings.vault_address);
        }

        const deposit = async () => {

        }

        const compound = async () => {

        }

        const claim = async () => {

        }

        return {
            quantity,
            initialDeposit,
            totalDeposit,
            totalClaim,
            deposit,
            compound,
            claim,
        }
    }
}
</script>
