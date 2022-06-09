<template>
    <h1>Downline</h1>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div v-show="loading" class="text-center">
                <div class="spinner-border m-5" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div v-show="!loading">
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Total Supply</p>
                            <p class="card-text"><strong>{{ totalSupply }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <p class="card-title">Owned</p>
                            <p class="card-text"><strong>{{ owned }}</strong></p>
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

export default {
    setup () {
        const store = useStore();
        const totalSupply = ref(0);
        const owned = ref(0);

        onMounted(async () => {
            await update();
        });

        const downlineContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.downline_abi), store.state.settings.downline_address);
        }

        const update = async () => {
            const contract = downlineContract();
            totalSupply.value = await contract.methods.totalSupply().call();
            owned.value = await contract.methods.balanceOf(store.state.wallet.address).call();
        }

        return {
            totalSupply,
            owned,
        }
    }

}
</script>
