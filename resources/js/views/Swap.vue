<template>
    <h1>Furswap</h1>
    <p class="mb-5">Swap USDC for FUR tokens here.<br/>
                    FUR token runs the Furio ecosystem.<br/>
                    Earn up to 2.5% daily rewards by depositing your tokens in to Furvault.</p>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div v-show="loading" class="text-center">
                <div class="spinner-border m-5" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div v-show="!loading">
                <p>Current $FUR price: <strong>{{ price }} USDC</strong></p>
                <div class="form-group">
                    <label for="from">From</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><strong>{{ fromCurrency }}</strong></div>
                        </div>
                        <input v-model="from" class="form-control" id="from"/>
                    </div>
                    <div class="text-right">
                        <button @click="max" class="btn btn-link"><small class="form-text text-muted">max</small></button>
                    </div>
                </div>
                <div v-show="warning" class="alert alert-danger">
                    <strong>{{ warning }}</strong>
                </div>
                <div class="form-group mb-3">
                    <label for="to">To</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><strong>{{ toCurrency }}</strong></div>
                        </div>
                        <input v-model="output" class="form-control" id="to" disabled/>
                    </div>
                </div>
                <div v-show="showVault" class="form-group">
                    <div class="form-check">
                        <input v-model="vault" class="form-check-input" type="checkbox" id="vault"/>
                        <label for="vault" class="form-check-label">Deposit directly into the <router-link :to="{ name: 'Vault' }"><strong>Vault</strong></router-link></label>
                    </div>
                </div>
                <div v-show="showVault && vault && showReferrer" class="form-group">
                    <div v-show="!referrer">
                        <div class="alert alert-danger">
                            <strong>You do not currently have a referrer set. By leaving this field blank, your referrer will default to the dev wallet.</strong>
                        </div>
                    </div>
                    <label for="referrer">Referrer</label>
                    <input v-model="referrer" class="form-control" id="referrer"/>
                </div>
                <div class="row mt-3">
                    <div class="col-10">
                        <div v-show="store.state.wallet.loggedIn">
                            <button @click="swap" class="btn btn-lg btn-info btn-block">Swap</button>
                        </div>
                        <div v-show="!store.state.wallet.loggedIn">
                            <button @click="swap" class="btn btn-lg btn-info btn-block" data-toggle="modal" data-target="#loginmodal">Connect Wallet</button>
                        </div>
                    </div>
                    <div class="col-2">
                        <button @click="swapToFrom" class="btn btn-lg btn-secondary btn-block"><i class="bi bi-arrow-down-up"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="../../images/usdc.svg" class="mx-auto d-block mb-3" alt="USDC" width="75" height="75"/>
                            <p class="card-title">USDC Balance</p>
                            <p class="card-text"><strong>{{ store.state.balances.payment }} USDC</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="../../images/fur.svg" class="mx-auto d-block mb-3" alt="FUR" width="75" height="75"/>
                            <p class="card-title">$FUR Balance</p>
                            <p class="card-text"><strong>{{ store.state.balances.token }} $FUR</strong></p>
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
import useDisplayCurrency from "../composables/useDisplayCurrency";
import useSettings from "../composables/useSettings";
import Web3 from "web3";

export default {
    setup () {
        var web3 = new Web3(Web3.givenProvider || 'ws://some.local-or-remote.node:8546');
        const store = useStore();
        const settings = useSettings();
        const alerts = useAlerts();
        const balances = useBalances();
        const displayCurrency = useDisplayCurrency();
        const loading = ref(false);
        const swapActive = ref("active");
        const buyActive = ref("");
        const fromCurrency = ref("USDC");
        const toCurrency = ref("$FUR");
        const to = ref(null);
        const from = ref(0);
        const vault = ref(false);
        const usdcBalance = ref(0);
        const furBalance = ref(0);
        const referrer = ref(store.state.referrer);
        const output = ref(0);
        const participant = ref(null);
        const warning = ref(null);
        const price = ref(0);
        const onCooldown = ref(false);

        const showVault = computed(() => {
            return toCurrency.value == "$FUR";
        });

        const showReferrer = computed(() => {
            if(!participant.value) {
                return false;
            }
            return participant.value.referrer == "0x0000000000000000000000000000000000000000";
        });

        addEventListener("refresh", async () => {
            await update();
        });

        onMounted(async () => {
            await settings.update();
            if(!store.state.wallet.loggedIn) return;
            update();
        });

        watch(from, async (value) => {
            getOutput();
        });


        const update = async () => {
            try {
                const vault = new web3.eth.Contract(JSON.parse(store.state.settings.vault_abi), store.state.settings.vault_address);
                const swap = new web3.eth.Contract(JSON.parse(store.state.settings.swap_abi), store.state.settings.swap_address);
                const token = new web3.eth.Contract(JSON.parse(store.state.settings.token_abi), store.state.settings.token_address);
                price.value = displayCurrency.format(await swap.methods.sellOutput("1000000000000000000").call());

                if(store.state.wallet.loggedIn) {
                    onCooldown.value = await swap.methods.onCooldown(store.state.wallet.address).call();
                    participant.value = await vault.methods.getParticipant(store.state.wallet.address).call();
                }

                balances.refresh();
            } catch (error) {
                console.error(error);
                alerts.danger(error.message);
            }
        }

        const activateSwap = () => {
            swapActive.value = "active";
            buyActive.value = "";
        }

        const activateBuy = () => {
            buyActive.value = "active";
            swapActive.value = "";
        }

        const swapToFrom = () => {
            const tmp = fromCurrency.value;
            fromCurrency.value = toCurrency.value;
            from.value = 0;
            toCurrency.value = tmp;
        }

        const max = () => {
            if(fromCurrency.value == "$FUR") {
                from.value = store.state.balances.token;
            }
            if(fromCurrency.value == "USDC") {
                from.value = store.state.balances.payment;
            }
        }

        const getOutput = async () => {
            try {
                warning.value = null;
                let amount = BigInt(from.value * 1000000000000000000);
                const swap = new web3.eth.Contract(JSON.parse(store.state.settings.swap_abi), store.state.settings.swap_address);
                if(amount == 0) {
                    output.value = 0;
                    return;
                }
                if(fromCurrency.value == "$FUR") {
                    if(participant.value.balance )
                    if(amount > participant.value.balance * .25 && !participant.value.maxed) {
                        amount = BigInt(from.value * 400000000000000000);
                        warning.value = "WARNING: The sell amount is greater than 25% of your vault balance and will receive a pump and dump tax of 60%!";
                    }
                    else {
                        amount = BigInt(from.value * 900000000000000000);
                        warning.value = "WARNING: All sales of $FUR will be taxed at a rate of 10%.";
                    }
                    output.value = displayCurrency.format(await swap.methods.sellOutput(amount).call());
                }
                if(fromCurrency.value == "USDC") {
                    output.value = displayCurrency.format(await swap.methods.buyOutput(store.state.settings.payment_address, amount).call());
                }
            } catch (error) {
                alerts.danger(error.message);
            }
        }

        const swap = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const swap = new web3.eth.Contract(JSON.parse(store.state.settings.swap_abi), store.state.settings.swap_address);
                const amount = BigInt(from.value * 1000000000000000000);
                let token;
                let method;
                if(fromCurrency.value == "$FUR") {
                    if(onCooldown.value) {
                        alerts.danger("You are currently on a sell cooldown period. Please try again later");
                        loading.value = false;
                        return;
                    }
                    token = new web3.eth.Contract(JSON.parse(store.state.settings.token_abi), store.state.settings.token_address);
                    method = "sell";
                }
                if(fromCurrency.value == "USDC") {
                    token = new web3.eth.Contract(JSON.parse(store.state.settings.payment_abi), store.state.settings.payment_address);
                    method = "buy";
                }
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1.2;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const allowance = await token.methods.allowance(store.state.wallet.address, store.state.settings.swap_address).call();
                if(allowance < amount) {
                    const approveGas = Math.round(await token.methods.approve(store.state.settings.swap_address, amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                    await token.methods.approve(store.state.settings.swap_address, amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                }
                let gas;
                let result;
                if(fromCurrency.value == "USDC" && vault.value == true) {
                    if(referrer.value) {
                        gas = Math.round(await swap.methods.depositBuy(store.state.settings.payment_address, amount, referrer.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                        result = await swap.methods.depositBuy(store.state.settings.payment_address, amount, referrer.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                    } else {
                        gas = Math.round(await swap.methods.depositBuy(store.state.settings.payment_address, amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                        result = await swap.methods.depositBuy(store.state.settings.payment_address, amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                    }
                } else {
                    if(method == "buy"){
                        gas = Math.round(await swap.methods[method](store.state.settings.payment_address, amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                        result = await swap.methods[method](store.state.settings.payment_address, amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                    }
                    else {
                        gas = Math.round(await swap.methods[method](amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                        result = await swap.methods[method](amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                    }
                }
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            dispatchEvent(new Event("refresh"));
            update();
            loading.value = false;
        }

        return {
            store,
            swapActive,
            buyActive,
            fromCurrency,
            toCurrency,
            to,
            from,
            vault,
            showVault,
            usdcBalance,
            furBalance,
            activateSwap,
            activateBuy,
            swapToFrom,
            referrer,
            showReferrer,
            max,
            output,
            swap,
            loading,
            warning,
            price,
            onCooldown,
        }
    }
}
</script>
