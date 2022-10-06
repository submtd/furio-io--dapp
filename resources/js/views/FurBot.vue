<template>
    <div>
        <h1>Furbot</h1>
        <p class="mb-5">Furbot description goes here.</p>
        <div class="row flex-row-reverse gx-5">
            <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
                <div v-show="loading" class="text-center">
                    <div class="spinner-border m-5" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div v-show="!loading">
                    Furbot
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="../../images/usdc.svg" class="mx-auto d-block mb-3" alt="USDC" width="75" height="75"/>
                                <p class="card-title">Available Dividends</p>
                                <p class="card-text"><strong>{{ displayCurrency.format(availableDividends) }} USDC</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="../../images/fur.svg" class="mx-auto d-block mb-3" alt="FUR" width="75" height="75"/>
                                <p class="card-title">NFTs Owned</p>
                                <p class="card-text"><strong>{{ nftsOwned }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { useStore } from "vuex";
import useAlerts from "../composables/useAlerts";
import useDisplayCurrency from "../composables/useDisplayCurrency";

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const displayCurrency = useDisplayCurrency();
        const availableDividends = ref(0);
        const nftsOwned = ref(0);

        addEventListener("refresh", async () => {
            await update();
        });

        onMounted(async () => {
            update();
        });

        const update = async () => {
            try {
            } catch (error) {
                alerts.danger(error.message);
            }
        }


        return {
            store,
            displayCurrency,
            availableDividends,
            nftsOwned,
        }
    }
}
</script>
