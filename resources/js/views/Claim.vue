<template>
    <h1>Claim</h1>
    <p class="mb-5">Claim your FUR tokens here. You can deposit them straight into the Furvault by selecting deposit directly into the Vault.</p>
    <div v-show="available < 1" class="bg-light text-dark rounded p-5">
        <p>You do not have any $FUR tokens available to claim.</p>
    </div>
    <div v-show="available > 0" class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div v-show="!showConfirm && !loading">
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
                        <label for="vault" class="form-check-label">Deposit directly into the <router-link :to="{ name: 'Vault' }"><strong>Vault</strong></router-link></label>
                    </div>
                </div>
                <div v-show="showReferrer" class="form-group">
                    <label for="referrer">Referrer</label>
                    <input v-model="referrer" class="form-control" id="referrer"/>
                </div>
                <button @click="confirm" class="btn btn-lg btn-info btn-block mb-2">Claim</button>
            </div>
            <div v-show="!showConfirm && loading" class="text-center">
                <div class="spinner-border m-5" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div v-show="showConfirm && !loading">
                <div v-show="vault">
                    <p>
                        You are about to send <strong>{{ quantity }}</strong> $FUR tokens to the vault on behalf of address <strong>{{ address }}</strong>.
                    </p>
                    <div v-show="showReferrer && !referrer" class="form-group">
                        <div class="alert alert-danger">
                            <strong>You do not currently have a referrer set. By leaving this field blank, your referrer will default to the dev wallet.</strong>
                        </div>
                        <label for="referrer">Referrer</label>
                        <input v-model="referrer" class="form-control" id="referrer"/>
                    </div>
                </div>
                <div v-show="!vault">
                    <p>
                        You are about to send <strong>{{ quantity }}</strong> $FUR tokens to the address <strong>{{ address }}</strong>.
                    </p>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <button @click="cancel" class="btn btn-lg btn-secondary btn-block mb-2">Cancel</button>
                    </div>
                    <div class="col-sm-6">
                        <button @click="claim" class="btn btn-lg btn-info btn-block mb-2">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="../../images/fur.svg" class="mx-auto d-block mb-3" alt="FUR" width="75" height="75"/>
                            <p class="card-title">Available</p>
                            <p class="card-text"><strong>{{ available }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="../../images/fur.svg" class="mx-auto d-block mb-3" alt="FUR" width="75" height="75"/>
                            <p class="card-title">Claimed</p>
                            <p class="card-text"><strong>{{ claimed }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, watch } from "vue";
import { useStore } from "vuex";
import useAlerts from "../composables/useAlerts";
import useBalances from "../composables/useBalances";

export default {
    setup () {
        const alerts = useAlerts();
        const store = useStore();
        const balances = useBalances();
        const available = ref(0);
        const claimed = ref(0);
        const quantity = ref(0);
        const address = ref(0);
        const vault = ref(false);
        const referrer = ref(null);
        const showConfirm = ref(false);
        const loading = ref(false);
        const participant = ref(null);

        const showReferrer = computed(() => {
            if(!participant.value) {
                return false;
            }
            if(!vault.value) {
                return false;
            }
            return participant.value.referrer == "0x0000000000000000000000000000000000000000";
        });

        onMounted(async () => {
            await update();
            quantity.value = available.value;
            address.value = store.state.wallet.address;
        });

        watch(address, async (value) => {
            try {
                const vault = vaultContract();
                participant.value = await vault.methods.getParticipant(value).call();
                if(value != store.state.wallet.address && participant.value.referrer == "0x0000000000000000000000000000000000000000") {
                    referrer.value = store.state.wallet.address;
                }
            } catch (error) {
                alerts.danger(error.message);
            }
        });

        const claimContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.claim_abi), store.state.settings.claim_address);
        }

        const vaultContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.vault_abi), store.state.settings.vault_address);
        }

        const update = async () => {
            loading.value = true;
            try {
                const claim = claimContract();
                available.value = await claim.methods.getOwnerValue(store.state.wallet.address).call();
            } catch (error) {
                alerts.danger(error.message);
            }
            balances.refresh();
            loading.value = false;
        }

        const confirm = () => {
            if(quantity.value < 1 || quantity.value > available.value) {
                alerts.danger("Invalid quantity");
                quantity.value = available.value;
                return;
            }
            showConfirm.value = true;
        }

        const cancel = () => {
            showConfirm.value = false;
        }

        const claim = async () => {
            showConfirm.value = false;
            loading.value = true;
            try {
                const contract = claimContract();
                const gasPriceMultiplier = 1.5;
                const gasMultipler = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                let result;
                if(referrer.value) {
                    const gas = Math.round(await contract.methods.claim(quantity.value, address.value, vault.value, referrer.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultipler);
                    result = await contract.methods.claim(quantity.value, address.value, vault.value, referrer.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                } else {
                    const gas = Math.round(await contract.methods.claim(quantity.value, address.value, vault.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultipler);
                    result = await contract.methods.claim(quantity.value, address.value, vault.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                }
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            update();
            loading.value = false;
        }

        return {
            available,
            claimed,
            quantity,
            address,
            vault,
            referrer,
            showReferrer,
            showConfirm,
            confirm,
            cancel,
            claim,
            loading,
            participant,
        }
    }
}
</script>
