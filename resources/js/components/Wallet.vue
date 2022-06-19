<template>

</template>

<script>
import { ref, computed } from "vue";
import useWallet from "../composables/useWallet";

export default {
    setup () {
        const wallet = useWallet();

        return {
            wallet,
        }
    }
}
</script>
