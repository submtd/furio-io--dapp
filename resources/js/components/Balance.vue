<template>
        <div class="container-fluid bal-gradient px-5 m-0 flex-row align-items-center justify-content-between d-none d-xs-none d-sm-none d-md-flex">
            <div>
                <img src="../../images/fur.svg"  alt="FUR" width="25" height="25"/>
                <span class="bal-font pl-2 pr-4">$FUR Balance: {{ store.state.balances.token ?? 0 }}</span>
            
                <img src="../../images/usdc.svg" alt="USDC" width="25" height="25"/>
                <span class="bal-font pl-2 pr-4">USDC Balance: {{ store.state.balances.payment ?? 0 }}</span>
            
                <img src="../../images/fur.svg"  alt="FUR" width="25" height="25"/>
                <span class="bal-font pl-2 pr-4">Vault Balance: {{ store.state.balances.vault ?? 0 }}</span>
            </div>
            <div>
                <router-link :to="{ name: 'BuyCrypto' }" class="link pr-4">Trade Crypto
                </router-link>
                <a href="https://furio.io/whitepaper" target="_blank" class="link pr-4">Whitepaper</a>
                <a href="https://furio.io" target="_blank" class="link">Website</a>
            </div>
        </div>
</template>

<script>

import { useStore } from 'vuex';

export default {
    setup() {
        const store = useStore();
        return {
            store
        }
    }
}

</script>