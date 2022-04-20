<template>
    <h1>Presale</h1>
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
                <div v-show="store.state.presaleNft.max > 0">
                    <h4>Purchase Presale NFTs</h4>
                    <input v-show="store.state.presaleNft.max > 1" v-model="quantity" :disabled="!buyButtonEnabled" :max="store.state.presaleNft.max" min="1" type="number" class="form-control mb-2" id="quantity">
                    <button @click="purchase" :disabled="!buyButtonEnabled" class="btn btn-lg btn-primary col-12 text-light">Purchase ({{ totalPrice / 1000000 }} USDC)</button>
                </div>
                <div v-show="store.state.presaleNft.max == 0">
                    <div v-show="showTimer">
                        <p class="mb-3">{{ store.state.presaleNft.nextRound }} starts in <strong>{{ countdown.days }}</strong> days, <strong>{{ countdown.hours }}</strong> hours, <strong>{{ countdown.minutes }}</strong> minutes, <strong>{{ countdown.seconds }}</strong> seconds.</p>
                    </div>
                    <p>No presales are available at this time.</p>
                </div>
            </div>
            <p class="text-center mt-5"><a href="#" @click="update">refresh contract data</a></p>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Remaining Supply</p>
                            <p class="card-text"><strong>{{ store.state.presaleNft.supply }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Value per NFT</p>
                            <p class="card-text"><strong>{{ store.state.presaleNft.value / 1000000000000000000 }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Price per NFT</p>
                            <p class="card-text"><strong>{{ store.state.presaleNft.price / 1000000 }} USDT</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Max Available</p>
                            <p class="card-text"><strong>{{ store.state.presaleNft.max }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Owned Value</p>
                            <p class="card-text"><strong>{{ store.state.presaleNft.ownedValue / 1000000000000000000 }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Balance</p>
                            <p class="card-text"><strong>{{ store.state.presaleNft.balance }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <p>USDC Address: {{ store.state.settings.usdcAddress }}</p>
            <p>NFT Address: {{ store.state.settings.presaleNftAddress }}</p>
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
        const email = ref(null);
        const emailButtonEnabled = ref(true);
        const emailVerification = ref(null);
        const emailVerificationButtonEnabled = ref(true);
        const quantity = ref(1);
        const buyButtonEnabled = ref(true);
        const totalPrice = computed(() => {
            return quantity.value * store.state.presaleNft.price;
        });
        const countdown = ref(useTimer(0));
        const showTimer = ref(false);
        const timerDone = computed(() => {
            return  countdown.value.days == 0 &&
                    countdown.value.hours == 0 &&
                    countdown.value.minutes == 0 &&
                    countdown.value.seconds == 0;
        });

        onMounted(async () => {
            await update();
        });

        watch(timerDone, async (newValue, oldValue) => {
            if(oldValue) {
                return;
            }
            if(!newValue) {
                return;
            }
            await update();
            alert("Done!");
        });

        const update = async () => {
            if(!store.state.wallet.loggedIn) {
                return;
            }
            if(store.state.presaleNft.updated) {
                return;
            }
            email.value = store.state.wallet.email;
            await presale.getContractData();
            quantity.value = store.state.presaleNft.max;
            if(store.state.presaleNft.claimStart > Date.now() / 1000) {
                if(store.state.settings.showClaimTimer) {
                    showTimer.value = true;
                }
                countdown.value.restart(store.state.presaleNft.claimStart * 1000);
            }
            if(store.state.presaleNft.presaleThreeStart > Date.now() / 1000) {
                if(store.state.settings.showPresaleThreeTimer) {
                    showTimer.value = true;
                }
                countdown.value.restart(store.state.presaleNft.presaleThreeStart * 1000);
            }
            if(store.state.presaleNft.presaleTwoStart > Date.now() / 1000) {
                if(store.state.settings.showPresaleTwoTimer) {
                    showTimer.value = true;
                }
                countdown.value.restart(store.state.presaleNft.presaleTwoStart * 1000);
            }
            if(store.state.presaleNft.presaleOneStart > Date.now() / 1000) {
                if(store.state.settings.showPresaleOneTimer) {
                    showTimer.value = true;
                }
                countdown.value.restart(store.state.presaleNft.presaleOneStart * 1000);
            }
        }

        const submitEmail = async () => {
            emailButtonEnabled.value = false;
            const wallet = store.state.wallet;
            await axios.post("/api/v1/address", {
                address: wallet.address,
                email: email.value,
            }).then(response => {
                wallet.email = response.data.email;
                store.commit("wallet", wallet);
            }).catch(error => {
                alerts.danger(error.message);
            });
            emailButtonEnabled.value = true;
        }

        const submitEmailVerification = async () => {
            emailVerificationButtonEnabled.value = false;
            alert(emailVerification.value);
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
            buyButtonEnabled.value = false;
            alerts.info("Waiting on response from wallet");
            try {
                const usdc = presale.getPaymentContract();
                const nft = presale.getContract();
                const gasPrice = Math.round(await web3.eth.getGasPrice());
                const allowance = await usdc.methods.allowance(store.state.wallet.address, store.state.settings.presaleNftAddress).call();
                if(allowance < quantity.value * store.state.presaleNft.price) {
                    const approveGas = Math.round(await usdc.methods.approve(store.state.settings.presaleNftAddress, quantity.value * store.state.presaleNft.price).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * 2);
                    await usdc.methods.approve(store.state.settings.presaleNftAddress, quantity.value * store.state.presaleNft.price).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                }
                const buyGas = Math.round(await nft.methods.buy(quantity.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice}) * 2);
                const result = await nft.methods.buy(quantity.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: buyGas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
                await presale.getContractData();
            } catch (error) {
                alerts.danger(error.message);
            }
            buyButtonEnabled.value = true;
        }

        return {
            store,
            presale,
            email,
            emailButtonEnabled,
            emailVerification,
            emailVerificationButtonEnabled,
            submitEmail,
            submitEmailVerification,
            updateEmail,
            quantity,
            buyButtonEnabled,
            totalPrice,
            purchase,
            showTimer,
            countdown,
            update,
        }
    }
}
</script>
