<template>
    <h1>Swap</h1>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
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
            <div v-show="showVault && vault && !referrer" class="form-group">
                <label for="referrer">Referrer</label>
                <input v-model="referrer" class="form-control" id="referrer"/>
            </div>
            <div class="row mt-3">
                <div class="col-10">
                    <button @click="swap" class="btn btn-lg btn-info btn-block">Swap</button>
                </div>
                <div class="col-2">
                    <button @click="swapToFrom" class="btn btn-lg btn-secondary btn-block"><i class="bi bi-arrow-down-up"></i></button>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">USDC Balance</p>
                            <p class="card-text"><strong>{{ usdcBalance }} USDC</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">$FUR Balance</p>
                            <p class="card-text"><strong>{{ furBalance }} $FUR</strong></p>
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

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const balances = useBalances();
        const displayCurrency = useDisplayCurrency();
        const swapActive = ref("active");
        const buyActive = ref("");
        const fromCurrency = ref("USDC");
        const toCurrency = ref("$FUR");
        const to = ref(null);
        const from = ref(0);
        const vault = ref(true);
        const usdcBalance = ref(0);
        const furBalance = ref(0);
        const referrer = ref(null);
        const output = ref(0);

        const showVault = computed(() => {
            return toCurrency.value == "$FUR";
        });

        onMounted(async () => {
            balances.refresh();
        });

        watch(from, async (value) => {
            getOutput();
        });

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
            from.value = output.value;
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
                const swap = new web3.eth.Contract(JSON.parse(store.state.settings.swap_abi), store.state.settings.swap_address);
                const amount = BigInt(from.value * 1000000000000000000);
                if(fromCurrency.value == "$FUR") {
                    output.value = displayCurrency.format(await swap.methods.sellOutput(amount).call());
                }
                if(fromCurrency.value == "USDC") {
                    output.value = displayCurrency.format(await swap.methods.buyOutput(amount).call());
                }
            } catch (error) {
                alerts.danger(error.message);
            }
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
            max,
            output,
        }
    }
}
</script>
