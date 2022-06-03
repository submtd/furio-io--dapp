<template>
    <h1>Profile</h1>
    <div class="bg-light text-dark rounded p-5">
        <div class="form-group">
            <label for="name">Name</label>
            <input v-model="name" class="form-control" id="name"/>
        </div>
        <button @click="update" class="btn btn-lg btn-info btn-block mb-2">Update</button>
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
        const name = ref(store.state.wallet.name);

        const update = async () => {
            await axios.post("/api/v1/address", {
                address: store.state.wallet.address,
                name: name.value,
            }).then(response => {
                const wallet = store.state.wallet;
                wallet.name = name.value;
                store.commit("wallet", wallet);
                alerts.success("Profile updated");
            }).catch(error => {
                console.log(error.response);
                alerts.danger(error.response.data.message);
            });
        }

        return {
            name,
            update,
        }
    }
}
</script>
