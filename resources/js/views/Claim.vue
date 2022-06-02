<template>
    <h1>Claim</h1>
    <div class="bg-light text-dark rounded p-5">
        <div v-show="available < 1" class="row">
            <p>You do not have any $FUR tokens available to claim.</p>
        </div>
        <div v-show="available > 0" class="row">
            <div class="col-md-6">
                <p>You have <strong>{{ available }}</strong> $FUR tokens available to claim.</p>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input v-model="quantity" :max="available" min="0" type="number" class="form-control" id="quantity"/>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input v-model="address" class="form-control" id="address"/>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input v-model="vault" class="form-check-input" type="checkbox" id="vault"/>
                        <label for="vault" class="form-check-label">Send to Vault</label>
                    </div>
                </div>
                <button class="btn btn-lg btn-primary btn-block">Claim</button>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { useStore } from "vuex";
import useAlerts from "../composables/useAlerts";

export default {
    setup () {
        const alerts = useAlerts();
        const store = useStore();

        const available = ref(0);
        const quantity = ref(0);
        const address = ref(0);
        const vault = ref(true);

        onMounted(async () => {
            await getAvailable();
            quantity.value = available.value;
            address.value = store.state.wallet.address;
        });

        const getAvailable = async () => {
            try {
                const contract = new web3.eth.Contract(JSON.parse(store.state.settings.claim_abi), store.state.settings.claim_address);
                available.value = await contract.methods.getOwnerValue(store.state.wallet.address).call();
            } catch (error) {
                alerts.danger(error.message);
            }
        }

        return {
            available,
            quantity,
            address,
            vault,
        }
    }
}
</script>
