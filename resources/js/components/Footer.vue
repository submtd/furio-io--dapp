<template>
    <div class="container footer d-flex flex-row justify-content-around flex-wrap footer-spacing">
        <div class="d-flex flex-column justify-content-around footer-panel footer-custom-spacing p-2 custom-height">
            <div class="d-flex flex-row justify-content-between align-items-center p-2">
                <div class="d-flex flex-row">
                    <img src="../../images/furio-icon.svg" alt="Furio Logo" class="img-furio-size">
                    <div class="d-flex flex-column pl-3">
                        <p :style="{margin: '0 !important', fontSize: '16px'}">$FUR</p>
                        <p :style="{margin: '0 !important', fontWeight: '550', fontSize: '20px', textAlign: 'center'}">{{coinprice}}</p>
                    </div>
                </div>
                
                <button class="btn btn-add-fur" @click="wallet.addFurioToken"><img src="../../images/main-metamask.svg" alt="Add $FUR to Metamask" class="img-metamask-size"></button>
                <button class="btn btn-buy-fur" @click="moveToSwap">Buy $FUR</button>
            </div>

            <div class="d-flex flex-row justify-content-between flex-wrap p-2">
                <a href="https://twitter.com/furiocrypto" target="_blank"><img src="../../images/logo-twitter.svg" width="32" height="32" alt="Twitter Logo"></a>
                <a href="https://t.me/furiocryptochat" target="_blank"><img src="../../images/logo-telegram.svg" width="32" height="32" alt="Telegram Logo"></a>
                <a href="https://discord.gg/furio" target="_blank"><img src="../../images/logo-discord.svg" width="32" height="32" alt="Discord Logo"></a>
                <a href="https://furiocrypto.medium.com" target="_blank"><img src="../../images/logo-medium.svg" width="32" height="32" alt="Medium Logo"></a>
                <a href="https://www.youtube.com/c/FurioCrypto" target="_blank"><img src="../../images/logo-youtube.svg" width="32" height="32" alt="Youtube Logo"></a>
                <a href="https://www.facebook.com/furiocrypto" target="_blank"><img src="../../images/logo-facebook.svg" width="32" height="32" alt="Facebook Logo"></a>
                <a href="https://www.instagram.com/furiocrypto" target="_blank"><img src="../../images/logo-instagram.svg" width="32" height="32" alt="Instagram Logo"></a>
            </div>
        </div>
        <div class="d-flex flex-column footer-custom-spacing custom-height">
            <h5 :style="{paddingLeft: '10px'}">Useful Links</h5>
            <div class="d-flex flex-row h-100">
                <div class="d-flex flex-column justify-content-between">
                    <a href="https://www.furio.io/how-it-works/" target="_blank" class="footer-text">How It Works</a>
                    <a href="https://www.furio.io/furvault" target="_blank" class="footer-text">Furvault</a>
                    <a href="https://www.furio.io/furswap" target="_blank" class="footer-text">Furswap</a>
                    <a href="https://www.furio.io/referral-system" target="_blank" class="footer-text">Referral System</a>
                    <a href="https://www.furio.io/variable-rewards" target="_blank" class="footer-text">Variable Rewards</a>
                    <a href="https://app.furio.io/promo" target="_blank" class="footer-text">Check for Promos</a>
                </div>
                <div class="d-flex flex-column justify-content-between">
                    <a href="https://www.furio.io/taxes" target="_blank" class="footer-text">Taxes</a>
                    <a href="https://www.furio.io/whale-tax" target="_blank" class="footer-text">Whale Tax</a>
                    <a href="https://www.furio.io/anti-dump-mechanics" target="_blank" class="footer-text">Anti Dump Mechanics</a>
                    <a href="https://www.furio.io/fast-programme/" target="_blank" class="footer-text">FAST Programme</a>
                    <a href="https://www.furio.io/furpool/" target="_blank" class="footer-text">Furpool</a>
                    <a href="https://app.furio.io/addressbook" target="_blank" class="footer-text">Address Book</a>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column justify-content-between footer-custom-spacing custom-height">
            <h5>Contact Us</h5>
            <div class="d-flex flex-column justify">
                <a href="mailto:info@furio.io" class="footer-text" :style="{padding: '0 !important', margin: '70px 0 70px 0 !important'}"><i class="bi bi-envelope-fill"></i> info@furio.io</a>
                <a href="https://solidity.finance/audits/Furio/" target="_blank"><img src="../../images/solidity-banner.svg" alt="Solidity Banner" width="170px"/></a>
            </div>
        </div>
    </div>
    <LoginModal/>
</template>

<script>
import { computed, ref } from "vue";
import { useStore } from "vuex";
import axios from 'axios';
import useAlerts from "../composables/useAlerts";
import useWallet from "../composables/useWallet";
import LoginModal from "./LoginModal.vue";
export default {
    data() {
        return {
            coinprice: null
        };
    },
    methods: {
        getFurioPrice: async function () {
            let val;
            await axios.get("https://api.coingecko.com/api/v3/simple/price?ids=furio&vs_currencies=usd&include_market_cap=false&include_24hr_vol=false&include_24hr_change=false&include_last_updated_at=false").then(function (res) {
                console.log("furio price", res.data.furio.usd);
                val = res.data.furio.usd;
            });
            this.coinprice = val;
        }
    },
    mounted() {
        this.getFurioPrice();
    },
    setup() {
        const store = useStore();
        const alerts = useAlerts();
        const wallet = useWallet();
        const refLink = computed(() => {
            if (!store.state.wallet.loggedIn) {
                return null;
            }
            return location.protocol + "//" + location.host + "?ref=" + store.state.wallet.address;
        });
        const moveToSwap = () => {
            //window.location = "https://app.furio.io/swap";
            window.open("https://app.furio.io/swap", "_blank");
        };
        const copyRefLink = () => {
            navigator.clipboard.writeText(refLink.value);
            alerts.info("Referral link copied");
        };
        return {
            store,
            wallet,
            refLink,
            copyRefLink,
            moveToSwap,
        };
    },
    components: { LoginModal }
}
</script>
