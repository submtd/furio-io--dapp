<template>
    <div>
        <h1>Update Pool APR</h1>
        <div v-show="!store.state.wallet.loggedIn" class="bg-light text-dark rounded p-5">
            <p>Please connect your wallet to update pool rates</p>
            <router-link :to="{ name: 'Connect' }" class="btn btn-lg text-light btn-primary" active-class="active">CONNECT</router-link>
        </div>
        <div v-show="store.state.wallet.loggedIn" class="row flex-row-reverse gx-5">
            <div class="col-lg-6 bg-light text-dark rounded p-5 mb-4">
                <div v-show="store.state.wallet.admin">
                    <div class="form-group">
                        <label for="alltime">All time APR</label>
                        <input v-model="alltime" class="form-control mb-2" id="alltime">
                    </div>
                    <div class="form-group">
                        <label for="fourteenday">14 Day APR</label>
                        <input v-model="fourteenday" class="form-control mb-2" id="fourteenday">
                    </div>
                    <button @click="update" class="btn btn-lg btn-primary col-12 text-light">Update</button>
                </div>
                <div v-show="!store.state.wallet.admin">
                    <p>You do not have permission to update the pool APR</p>
                </div>
            </div>
            <div class="col-lg-6">
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";
import { useStore } from "vuex";
import useAlerts from "../composables/useAlerts";
export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const alltime = ref(0);
        const fourteenday = ref(0);

        const update = async () => {
            const sign = await web3.eth.personal.sign(store.state.wallet.nonce, store.state.wallet.address, "");
            await axios.post("/api/v1/updatepoolapr", {
                address: store.state.wallet.address,
                nonce: store.state.wallet.nonce,
                signature: sign,
                alltime: alltime.value,
                fourteenday: fourteenday.value,
            }).then(response => {
                alerts.success("Pool APR updated");
            }).catch(error => {
                alerts.danger(error.response.data.message);
            });
        }

        return {
            store,
            update,
            alltime,
            fourteenday,
        }
    }
}
</script>
