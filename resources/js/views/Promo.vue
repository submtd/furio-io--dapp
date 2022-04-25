<template>
    <h1>Promo</h1>
    <div v-show="!store.state.wallet.loggedIn" class="bg-light text-dark rounded p-5">
        <p>Please connect your wallet to see if you have any promos available</p>
        <router-link :to="{ name: 'Connect' }" class="btn btn-lg text-light btn-primary" active-class="active">CONNECT</router-link>
    </div>
    <div v-show="store.state.wallet.loggedIn" class="row flex-row-reverse gx-5">
        <div class="col-lg-6 bg-light text-dark rounded p-5 mb-4">
            <div v-show="available > 0">
                <h4>Claim Promo NFTs</h4>
                <input v-show="available > 1" v-model="quantity" :disabled="!buyButtonEnabled" :max="available" min="1" type="number" class="form-control mb-2" id="quantity">
                <button @click="purchase" :disabled="!buyButtonEnabled" class="btn btn-lg btn-primary col-12 text-light">Claim ({{ totalPrice }} USDC)</button>
            </div>
            <div v-show="available == 0">
                <p>You do not have any promos available at this time</p>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Available</p>
                            <p class="card-text"><strong>{{ available }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Value per NFT</p>
                            <p class="card-text"><strong>{{ value }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Price per NFT</p>
                            <p class="card-text"><strong>{{ price }} USDC</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <p>USDC Address: {{ store.state.settings.payment_address }}</p>
            <p>Presale NFT Address: {{ store.state.settings.presale_address }}</p>
        </div>
    </div>
</template>

<script>
import { computed, onMounted, ref } from "vue";
import { useStore } from "vuex";
import useAlerts from "../composables/useAlerts";
import usePresale from "../composables/usePresale";
export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const presale = usePresale();
        const max = ref(0);
        const available = ref(0);
        const price = ref(0);
        const value = ref(0);
        const total = ref(0);
        const salt = ref(null);
        const expiration = ref(0);
        const signature = ref(null);
        const quantity = ref(1);
        const buyButtonEnabled = ref(true);
        const totalPrice = computed(() => {
            return quantity.value * price.value;
        });

        onMounted(() => {
            update();
        });

        const update = async () => {
            await axios.get("/api/v1/getpromo").then(response => {
                if(response.data.available) {
                    max.value = response.data.max;
                    price.value = response.data.price;
                    value.value = response.data.value;
                    total.value = response.data.total;
                    salt.value = response.data.salt;
                    expiration.value = response.data.expiration;
                    signature.value = response.data.signature;
                }
            }).catch(error => {
                alerts.danger(error.message);
            });
            if(max.value) {
                available.value = await presale.getAvailable(max.value, price.value, value.value, total.value);
                quantity.value = available.value;
                if(available.value == 0) {
                    alerts.warning("You do not have any promos available at this time");
                }
            }
        }

        const purchase = async () => {
            buyButtonEnabled.value = false;
            await presale.buy(signature.value, quantity.value, max.value, price.value, value.value, total.value, expiration.value);
            available.value -= quantity.value;
            update();
            buyButtonEnabled.value = true;
        }

        return {
            store,
            max,
            price,
            value,
            quantity,
            buyButtonEnabled,
            totalPrice,
            update,
            purchase,
            available,
        }
    }
}
</script>
