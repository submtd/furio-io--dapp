<template>
    <div v-if="store.state.alert" class="alert alert-dismissible fade show" :class="store.state.alert.type" role="alert">
        {{ store.state.alert.message }}
        <button @click="alerts.clear" type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>

<script>
import { useStore } from "vuex";
import useAlerts from "../composables/useAlerts";
export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        return {
            alerts,
            store,
        }
    }
}
</script>
