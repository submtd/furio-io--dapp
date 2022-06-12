<template>
    <div class="bg-light text-dark rounded p-5">
        <p>Please come back on June 14th for the project launch!</p>
    </div>
</template>

<script>
import { onMounted } from "vue";
import useBalances from "../composables/useBalances";

export default {
    setup () {
        const balances = useBalances();

        onMounted(async () => {
            balances.refresh();
        });
    }
}
</script>
