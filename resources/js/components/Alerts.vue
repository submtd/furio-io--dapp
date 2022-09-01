<template>
    
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
