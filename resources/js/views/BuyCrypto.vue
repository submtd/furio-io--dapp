<template>
    <h1>Trade Crypto</h1>
    <p class="mb-5">Buy your USDC and BNB to use in the Furio ecosystem here.<br/>
                    Purchasing through our integrated partner allows you to buy Binanace Smart Chain<br/>
                    tokens directly that will be sent to your crypto wallet after successful completion.</p>
    <div class="text-dark d-flex flex-row justify-content-center flex-wrap align-items-center">
        <div class="m-3 bg-white d-none d-sm-block" :style="{maxWidth: '500px', height: '450px', borderRadius: '30px', padding: '25px 25px 25px 25px'}">
            <h4 :style="{margin: '0', fontWeight: 'bold', textAlign: 'center', marginBottom: '20px'}">Buy/Sell BNB</h4>
            <iframe :style="{width: '450px', height: '500px', position: 'relative', border: 'none', outline: 'none'}" src="https://guardarian.com/calculator/v1?partner_api_token=9bcb3757-214c-47ea-8091-1afd7ef1dd71&theme=orange&type=narrow&default_fiat_amount=100&default_fiat_currency=USD&default_crypto_currency=BNB&crypto_currencies_list=%5B%7B%22ticker%22%3A%22BNB%22%2C%22network%22%3A%22BSC%22%7D%5D&fiat_currencies_list=%5B%7B%22ticker%22%3A%22EUR%22%2C%22network%22%3A%22EUR%22%7D%2C%7B%22ticker%22%3A%22USD%22%2C%22network%22%3A%22USD%22%7D%2C%7B%22ticker%22%3A%22GBP%22%2C%22network%22%3A%22GBP%22%7D%2C%7B%22ticker%22%3A%22CHF%22%2C%22network%22%3A%22CHF%22%7D%2C%7B%22ticker%22%3A%22JPY%22%2C%22network%22%3A%22JPY%22%7D%2C%7B%22ticker%22%3A%22CAD%22%2C%22network%22%3A%22CAD%22%7D%2C%7B%22ticker%22%3A%22AUD%22%2C%22network%22%3A%22AUD%22%7D%2C%7B%22ticker%22%3A%22NZD%22%2C%22network%22%3A%22NZD%22%7D%2C%7B%22ticker%22%3A%22SGD%22%2C%22network%22%3A%22SGD%22%7D%5D"></iframe>
        </div>
        <div class="m-3 d-none d-sm-block bg-white" :style="{maxWidth: '500px', height: '450px', borderRadius: '30px', padding: '25px 25px 25px 25px'}">
            <h4 :style="{margin: '0', fontWeight: 'bold', textAlign: 'center', marginBottom: '20px'}">Buy/Sell USDC</h4>
            <iframe :style="{width: '450px', height: '500px', position: 'relative', border: 'none', outline: 'none'}" src="https://guardarian.com/calculator/v1?partner_api_token=9bcb3757-214c-47ea-8091-1afd7ef1dd71&theme=orange&type=narrow&default_fiat_amount=100&default_fiat_currency=USD&default_crypto_currency=BNB&crypto_currencies_list=%5B%7B%22ticker%22%3A%22USDC%22%2C%22network%22%3A%22BSC%22%7D%5D&fiat_currencies_list=%5B%7B%22ticker%22%3A%22EUR%22%2C%22network%22%3A%22EUR%22%7D%2C%7B%22ticker%22%3A%22USD%22%2C%22network%22%3A%22USD%22%7D%2C%7B%22ticker%22%3A%22GBP%22%2C%22network%22%3A%22GBP%22%7D%2C%7B%22ticker%22%3A%22CHF%22%2C%22network%22%3A%22CHF%22%7D%2C%7B%22ticker%22%3A%22JPY%22%2C%22network%22%3A%22JPY%22%7D%2C%7B%22ticker%22%3A%22CAD%22%2C%22network%22%3A%22CAD%22%7D%2C%7B%22ticker%22%3A%22AUD%22%2C%22network%22%3A%22AUD%22%7D%2C%7B%22ticker%22%3A%22NZD%22%2C%22network%22%3A%22NZD%22%7D%2C%7B%22ticker%22%3A%22SGD%22%2C%22network%22%3A%22SGD%22%7D%5D"></iframe>
        </div>

        <div class="d-block d-sm-none container-fluid" :style="{marginBottom: '50px', maxWidth: '500px', height: '420px', borderRadius: '30px', padding:'10px 0 10px 0', backgroundColor: '#FF5C00'}">
            <h4 :style="{margin: '0', fontWeight: 'bold', textAlign: 'center', marginBottom: '10px'}">Buy/Sell BNB</h4>
            <iframe :style="{maxWidth: '100%', width: '450px', height: '500px', position: 'relative', border: 'none', outline: 'none'}" src="https://guardarian.com/calculator/v1?partner_api_token=9bcb3757-214c-47ea-8091-1afd7ef1dd71&theme=orange&type=narrow&default_fiat_amount=100&default_fiat_currency=USD&default_crypto_currency=BNB&crypto_currencies_list=%5B%7B%22ticker%22%3A%22BNB%22%2C%22network%22%3A%22BSC%22%7D%5D&fiat_currencies_list=%5B%7B%22ticker%22%3A%22EUR%22%2C%22network%22%3A%22EUR%22%7D%2C%7B%22ticker%22%3A%22USD%22%2C%22network%22%3A%22USD%22%7D%2C%7B%22ticker%22%3A%22GBP%22%2C%22network%22%3A%22GBP%22%7D%2C%7B%22ticker%22%3A%22CHF%22%2C%22network%22%3A%22CHF%22%7D%2C%7B%22ticker%22%3A%22JPY%22%2C%22network%22%3A%22JPY%22%7D%2C%7B%22ticker%22%3A%22CAD%22%2C%22network%22%3A%22CAD%22%7D%2C%7B%22ticker%22%3A%22AUD%22%2C%22network%22%3A%22AUD%22%7D%2C%7B%22ticker%22%3A%22NZD%22%2C%22network%22%3A%22NZD%22%7D%2C%7B%22ticker%22%3A%22SGD%22%2C%22network%22%3A%22SGD%22%7D%5D"></iframe>
        </div>

        <div class="d-block d-sm-none container-fluid" :style="{maxWidth: '500px', height: '420px', borderRadius: '30px', padding:'10px 0 10px 0', backgroundColor: '#FF5C00'}">
            <h4 :style="{margin: '0', fontWeight: 'bold', textAlign: 'center', marginBottom: '10px'}">Buy/Sell USDC</h4>
            <iframe :style="{maxWidth:'100%', width: '450px', height: '500px', position: 'relative', border: 'none', outline: 'none'}" src="https://guardarian.com/calculator/v1?partner_api_token=9bcb3757-214c-47ea-8091-1afd7ef1dd71&theme=orange&type=narrow&default_fiat_amount=100&default_fiat_currency=USD&default_crypto_currency=BNB&crypto_currencies_list=%5B%7B%22ticker%22%3A%22USDC%22%2C%22network%22%3A%22BSC%22%7D%5D&fiat_currencies_list=%5B%7B%22ticker%22%3A%22EUR%22%2C%22network%22%3A%22EUR%22%7D%2C%7B%22ticker%22%3A%22USD%22%2C%22network%22%3A%22USD%22%7D%2C%7B%22ticker%22%3A%22GBP%22%2C%22network%22%3A%22GBP%22%7D%2C%7B%22ticker%22%3A%22CHF%22%2C%22network%22%3A%22CHF%22%7D%2C%7B%22ticker%22%3A%22JPY%22%2C%22network%22%3A%22JPY%22%7D%2C%7B%22ticker%22%3A%22CAD%22%2C%22network%22%3A%22CAD%22%7D%2C%7B%22ticker%22%3A%22AUD%22%2C%22network%22%3A%22AUD%22%7D%2C%7B%22ticker%22%3A%22NZD%22%2C%22network%22%3A%22NZD%22%7D%2C%7B%22ticker%22%3A%22SGD%22%2C%22network%22%3A%22SGD%22%7D%5D"></iframe>
        </div>

    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { useStore } from "vuex";
import useSettings from "../composables/useSettings";

export default {
    setup() {
        // const store = useStore();
        const settings = useSettings();
        // const claimLink = ref(store.state.settings.block_explorer_url + "/address/" + store.state.settings.claim_address);
        // const downlineLink = ref(store.state.settings.block_explorer_url + "/address/" + store.state.settings.downline_address);
        // const presaleLink = ref(store.state.settings.block_explorer_url + "/address/" + store.state.settings.presale_address);
        // const swapLink = ref(store.state.settings.block_explorer_url + "/address/" + store.state.settings.swap_address);
        // const tokenLink = ref(store.state.settings.block_explorer_url + "/address/" + store.state.settings.token_address);
        // const paymentLink = ref(store.state.settings.block_explorer_url + "/address/" + store.state.settings.payment_address);
        // const vaultLink = ref(store.state.settings.block_explorer_url + "/address/" + store.state.settings.vault_address);
        // const voteLink = ref(store.state.settings.block_explorer_url + "/address/" + store.state.settings.vote_address);
        // const autoCompoundLink = ref(store.state.settings.block_explorer_url + "/address/" + store.state.settings.autocompound_address);
        onMounted(async () => {
            await settings.update();
        });
        return {
            // store,
            // claimLink,
            // downlineLink,
            // presaleLink,
            // swapLink,
            // tokenLink,
            // paymentLink,
            // vaultLink,
            // voteLink,
            // autoCompoundLink,
        };
    }
}
</script>
