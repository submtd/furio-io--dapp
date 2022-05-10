<template>
    <h1>Presale Two</h1>
    <div v-show="!store.state.wallet.loggedIn" class="bg-light text-dark rounded p-5">
        <p>Please connect your wallet to get presale information</p>
        <router-link :to="{ name: 'Connect' }" class="btn btn-lg text-light btn-primary" active-class="active">CONNECT</router-link>
    </div>
    <div v-show="store.state.wallet.loggedIn" class="row flex-row-reverse gx-5">
        <div class="col-lg-6 bg-light text-dark rounded p-5 mb-4">
            <div v-show="!store.state.wallet.email">
                <div class="mb-3">
                    <label for="email" class="form-label">Enter your email address</label>
                    <input v-model="email" type="email" class="form-control" id="email">
                </div>
                <button @click="submitEmail" :disabled="!emailButtonEnabled" class="btn btn-lg btn-primary text-light">Submit</button>
            </div>
            <div v-show="store.state.wallet.email && !store.state.wallet.emailVerifiedAt">
                <div class="mb-3">
                    <label for="email-verification" class="form-label">Enter the verification code sent to {{ store.state.wallet.email }}</label>
                    <input v-model="emailVerification" type="text" class="form-control" id="email-verification">
                </div>
                <button @click="submitEmailVerification" :disabled="!emailVerificationButtonEnabled" class="btn btn-lg btn-primary text-light">Submit</button>
                <button @click="updateEmail" :disabled="!emailVerificationButtonEnabled" class="ms-2 btn btn-lg btn-secondary text-light">Update Email Address</button>
            </div>
            <div v-show="store.state.wallet.email && store.state.wallet.emailVerifiedAt">
                <div v-show="available > 0">
                    <h4>Purchase Presale NFTs</h4>
                    <input v-show="available > 1" v-model="quantity" :disabled="!buyButtonEnabled" :max="available" min="1" type="number" class="form-control mb-2" id="quantity">
                    <button @click="purchase" :disabled="!buyButtonEnabled" class="btn btn-lg btn-primary col-12 text-light">Purchase ({{ totalPrice }} USDC)</button>
                    <div class="row mt-2">
                        <div v-show="available && reservedTimer == '00:00'">
                            You have <strong>{{ available }}</strong> available for purchase.
                        </div>
                        <div v-show="reservedTimer != '00:00'">
                            You have <strong>{{ reservedTimer }}</strong> to complete this transaction.
                        </div>
                    </div>
                    <div v-show="showTimer" class="mt-5 text-center">
                        {{ nextState }} starts in <strong>{{ timer }}</strong>
                    </div>
                </div>
                <div v-show="available == 0">
                    <div v-show="showTimer && nextState" class="text-center">
                        <strong>{{ nextState }} starts in</strong><h1 class="text-center">{{ timer }}</h1>
                    </div>
                    <div v-show="!showTimer && nextState">
                        <strong>Check back soon for the next presale!</strong>
                    </div>
                    <div v-show="!showTimer && !nextState">
                        <strong>Come back on the launch date to claim your presale NFTs!</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Max Per Wallet</p>
                            <p class="card-text"><strong>10</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Value per NFT</p>
                            <p class="card-text"><strong>100 $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Price per NFT</p>
                            <p class="card-text"><strong>150 USDC</strong></p>
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
import { computed, onMounted, ref, watch } from "vue";
import { useStore } from "vuex";
import { useTimer } from "vue-timer-hook";
import useAlerts from "../composables/useAlerts";
import usePresale from "../composables/usePresale";
export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const presale = usePresale();
        const state = ref(null);
        const nextState = ref(null);
        const purchased = ref(0);
        const max = ref(0);
        const available = ref(0);
        const sold = ref(0);
        const price = ref(0);
        const value = ref(0);
        const total = ref(0);
        const signature = ref(null);
        const email = ref(null);
        const emailButtonEnabled = ref(true);
        const emailVerification = ref(null);
        const emailVerificationButtonEnabled = ref(true);
        const quantity = ref(1);
        const buyButtonEnabled = ref(true);
        const totalPrice = computed(() => {
            return quantity.value * price.value;
        });
        const countdown = ref(useTimer(Date.now() / 1000));
        const reserved = ref(useTimer(Date.now() / 1000));
        const showTimer = ref(false);
        const timer = computed(() => {
            return String(countdown.value.days * 24 + countdown.value.hours).padStart(2, '0') + ":" + String(countdown.value.minutes).padStart(2, "0") + ":" + String(countdown.value.seconds).padStart(2, "0");
        });
        const reservedTimer = computed(() => {
            return String((reserved.value.days * 24 * 60) + (reserved.value.hours * 60) + reserved.value.minutes).padStart(2, "0") + ":" + String(reserved.value.seconds).padStart(2, "0");
        });
        const timerDone = computed(() => {
            return countdown.value.days == 0 && countdown.value.hours == 0 && countdown.value.minutes == 0 && countdown.value.seconds == 0;
        });

        onMounted(() => {
            update();
        });

        watch(timerDone, (newValue, oldValue) => {
            if(oldValue) {
                return;
            }
            if(!newValue) {
                return;
            }
            update();
        });

        const update = async () => {
            const currentTime = Date.now() / 1000;
            if(store.state.settings.presale_one_start > currentTime) {
                state.value = "Presale Coming Soon";
                nextState.value = "Presale One";
                countdown.value.restart((parseInt(store.state.settings.presale_one_start) + 5) * 1000);
                reserved.value.restart(Date.now() / 1000);
                purchased.value = 0;
                max.value = store.state.settings.presale_one_max;
                price.value = store.state.settings.presale_one_price;
                value.value = store.state.settings.presale_one_value;
                total.value = store.state.settings.presale_one_total;
                if(store.state.settings.show_presale_one_timer == 1) {
                    showTimer.value = true;
                }
            }
            if(store.state.settings.presale_one_start <= currentTime) {
                state.value = "Presale One";
                nextState.value = "Presale Two";
                countdown.value.restart((parseInt(store.state.settings.presale_two_start) + 5) * 1000);
                reserved.value.restart(Date.now() / 1000);
                quantity.value = store.state.settings.presale_one_max;
                purchased.value = 0;
                max.value = store.state.settings.presale_one_max;
                price.value = store.state.settings.presale_one_price;
                value.value = store.state.settings.presale_one_value;
                total.value = store.state.settings.presale_one_total;
                if(store.state.settings.show_presale_two_timer == 1) {
                    showTimer.value = true;
                }
            }
            if(store.state.settings.presale_two_start <= currentTime) {
                state.value = "Presale Two";
                nextState.value = "Presale Three";
                countdown.value.restart((parseInt(store.state.settings.presale_three_start) + 5) * 1000);
                reserved.value.restart(Date.now() / 1000);
                quantity.value = store.state.settings.presale_two_max;
                purchased.value = 0;
                max.value = store.state.settings.presale_two_max;
                price.value = store.state.settings.presale_two_price;
                value.value = store.state.settings.presale_two_value;
                total.value = store.state.settings.presale_two_total;
                if(store.state.settings.show_presale_three_timer == 1) {
                    showTimer.value = true;
                }
            }
            if(store.state.settings.presale_three_start <= currentTime) {
                state.value = "Presale Three";
                nextState.value = null;
                countdown.value.restart((parseInt(store.state.settings.claim_start) + 5) * 1000);
                reserved.value.restart(Date.now() / 1000);
                quantity.value = store.state.settings.presale_three_max;
                purchased.value = 0;
                max.value = store.state.settings.presale_three_max;
                price.value = store.state.settings.presale_three_price;
                value.value = store.state.settings.presale_three_value;
                total.value = store.state.settings.presale_three_total;
                showTimer.value = false;
            }
            email.value = store.state.wallet.email;
            if(state.value && state.value != "Presale Coming Soon") {
                available.value = await presale.getAvailable(max.value, price.value, value.value, total.value);
                sold.value = await presale.getSold(max.value, price.value, value.value, total.value);
                quantity.value = available.value;
            }
        }

        const submitEmail = async () => {
            emailButtonEnabled.value = false;
            const wallet = store.state.wallet;
            await axios.post("/api/v1/address", {
                address: wallet.address,
                email: email.value,
            }).then(response => {
                if(response.data.error) {
                    alerts.danger(response.data.message);
                    emailButtonEnabled.value = true;
                    return false;
                }
                wallet.email = response.data.email;
                store.commit("wallet", wallet);
            }).catch(error => {
                alerts.danger(error.message);
            });
            emailButtonEnabled.value = true;
        }

        const submitEmailVerification = async () => {
            emailVerificationButtonEnabled.value = false;
            const wallet = store.state.wallet;
            await axios.post("/api/v1/address", {
                address: wallet.address,
                email: wallet.email,
                email_verification_code: emailVerification.value,
            }).then(response => {
                wallet.emailVerifiedAt = response.data.email_verified_at;
                store.commit("wallet", wallet);
            }).catch(error => {
                alerts.danger(error.message);
            });
            emailVerificationButtonEnabled.value = true;
        }

        const updateEmail = () => {
            const wallet = store.state.wallet;
            wallet.email = null;
            store.commit("wallet", wallet);
        }

        const purchase = async () => {
            if(quantity.value > available.value) {
                alerts.danger("Quantity is too high");
                return false;
            }
            if(quantity.value < 1) {
                alerts.danger("Quantity is too low");
                return false;
            }
            buyButtonEnabled.value = false;
            await axios.get("/api/v1/presalesignature?quantity=" + quantity.value + "&sold=" + sold.value).then(response => {
                if(response.data.available == 0) {
                    alerts.danger("No presales are currently available. Please check back shortly");
                    buyButtonEnabled.value = true;
                    return false;
                }
                signature.value = response.data;
            }).catch(error => {
                alerts.danger(error.message);
                return false;
            });
            reserved.value.restart((parseInt(signature.value.expiration)) * 1000);
            await presale.buy(signature.value.signature, quantity.value, max.value, price.value, value.value, total.value, signature.value.expiration);
            available.value -= quantity.value;
            reserved.value.restart(Date.now() / 1000);
            update();
            buyButtonEnabled.value = true;
        }

        return {
            store,
            state,
            nextState,
            max,
            price,
            value,
            email,
            emailButtonEnabled,
            emailVerification,
            emailVerificationButtonEnabled,
            quantity,
            buyButtonEnabled,
            totalPrice,
            countdown,
            timer,
            reservedTimer,
            showTimer,
            timerDone,
            update,
            submitEmail,
            submitEmailVerification,
            updateEmail,
            purchase,
            purchased,
            available,
            sold,
        }
    }
}
</script>
