<template>
    <h1>Swap</h1>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <ul class="nav nav-pills mb-3">
                <li class="nav-item">
                    <a @click="activateSwap" class="nav-link" :class="swapActive" href="#">Swap</a>
                </li>
                <li class="nav-item">
                    <a @click="activateBuy" class="nav-link" :class="buyActive" href="#">Buy USDC</a>
                </li>
            </ul>
            <div v-show="swapActive == 'active'">
                <div class="form-group">
                    <label for="from">From</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><strong>{{ fromCurrency }}</strong></div>
                        </div>
                        <input v-model="from" class="form-control" id="from"/>
                    </div>
                    <small class="form-text text-muted text-right"><a href="#">max</a></small>
                </div>
                <div class="text-center">
                    <button @click="swapToFrom" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-down-up"></i></button>
                </div>
                <div class="form-group mb-3">
                    <label for="to">To</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><strong>{{ toCurrency }}</strong></div>
                        </div>
                        <input v-model="to" class="form-control" id="to" disabled/>
                    </div>
                </div>
                <button @click="swap" class="btn btn-lg btn-info btn-block mb-2">Swap</button>
            </div>
            <div v-show="buyActive == 'active'">
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
import { ref } from "vue";

export default {
    setup () {
        const swapActive = ref("active");
        const buyActive = ref("");
        const fromCurrency = ref("USDC");
        const toCurrency = ref("$FUR");
        const to = ref(null);
        const from = ref(null);
        const usdcBalance = ref(0);
        const furBalance = ref(0);

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
            toCurrency.value = tmp;
        }

        return {
            swapActive,
            buyActive,
            fromCurrency,
            toCurrency,
            to,
            from,
            usdcBalance,
            furBalance,
            activateSwap,
            activateBuy,
            swapToFrom,
        }
    }
}
</script>
