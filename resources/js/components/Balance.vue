<template>
    <div v-show="store.state.wallet.loggedIn">
        <div class="container-fluid bal-gradient px-5 m-0 d-flex flex-row align-items-center justify-content-between">
            <div>
                <img src="../../images/fur.svg"  alt="FUR" width="25" height="25"/>
                <span class="bal-font pl-2 pr-4">$FUR Balance: {{ store.state.balances.token }}</span>
            
                <img src="../../images/usdc.svg" alt="USDC" width="25" height="25"/>
                <span class="bal-font pl-2 pr-4">USDC Balance: {{ store.state.balances.payment }}</span>
            
                <img src="../../images/fur.svg"  alt="FUR" width="25" height="25"/>
                <span class="bal-font pl-2 pr-4">Vault Balance: {{ store.state.balances.vault }}</span>
            </div>
            <a href="https://furio.io" target="_blank" class="link">Website</a>
        </div>
    </div>
</template>

<script>

import { useStore } from 'vuex';

export default {
    setup() {
        const store = useStore();
        console.log("wallet: ", store.state.wallet.loggedIn)
        return {
            store
        }
    }
}

</script>