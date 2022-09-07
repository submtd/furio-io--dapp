<template>
    <div class="footer border-top border-secondary d-flex flex-row justify-content-around flex-wrap footer-spacing">
        <div class="d-flex flex-column justify-content-around footer-panel footer-custom-spacing p-4">
            <div class="d-flex flex-row justify-content-between align-items-center">
                <div class="d-flex flex-row">
                    <img src="../../images/furio-icon.svg" alt="Furio Logo" class="img-furio-size">
                    <div class="d-flex flex-column pl-3">
                        <p :style="{margin: '0 !important', fontSize: '16px'}">$FUR</p>
                        <p :style="{margin: '0 !important', fontWeight: '550', fontSize: '20px', textAlign: 'center'}">{{coinprice}}</p>
                    </div>
                </div>
                
                <button class="btn btn-add-fur"><img src="../../images/main-metamask.svg" alt="Add $FUR to Metamask" class="img-metamask-size"></button>
                <button class="btn btn-buy-fur" @click="moveToSwap">Buy $FUR</button>
            </div>

            <div class="d-flex flex-row">
                <a class="nav-link" href="https://twitter.com/furiocrypto"><img class="img-fluid" src="../../images/logo-twitter.svg" width="32" height="32" alt="Twitter Logo"></a>
                <a class="nav-link" href="https://t.me/furiocryptochat"><img class="img-fluid" src="../../images/logo-telegram.svg" width="32" height="32" alt="Telegram Logo"></a>
                <a class="nav-link" href="https://discord.gg/furio"><img class="img-fluid" src="../../images/logo-discord.svg" width="32" height="32" alt="Discord Logo"></a>
                <a class="nav-link" href="https://furiocrypto.medium.com"><img class="img-fluid" src="../../images/logo-medium.svg" width="32" height="32" alt="Medium Logo"></a>
                <a class="nav-link" href="https://www.youtube.com/c/FurioCrypto"><img src="../../images/logo-youtube.svg" width="32" height="32" alt="Youtube Logo"></a>
                <a class="nav-link" href="https://www.facebook.com/furiocrypto"><img src="../../images/logo-facebook.svg" width="32" height="32" alt="Facebook Logo"></a>
                <a class="nav-link" href="https://www.instagram.com/furiocrypto"><img class="img-fluid" src="../../images/logo-instagram.svg" width="32" height="32" alt="Instagram Logo"></a>
            </div>
        </div>
        <div class="d-flex flex-column footer-custom-spacing">
            <h5 :style="{paddingLeft: '16px'}">Useful Links</h5>
            <div class="d-flex flex-row">
                <div>
                    <a class="nav-link" href="https://www.furio.io/how-it-works/">How It Works</a>
                    <a class="nav-link" href="https://www.furio.io/furvault">Furvault</a>
                    <a class="nav-link" href="https://www.furio.io/furswap">Furswap</a>
                    <a class="nav-link" href="https://www.furio.io/referral-system">Referral System</a>
                    <a class="nav-link" href="https://www.furio.io/variable-rewards">Variable Rewards</a>
                    <a class="nav-link" href="https://app.furio.io/promo">Check for Promos</a>
                </div>
                <div>
                    <a class="nav-link" href="https://www.furio.io/taxes">Taxes</a>
                    <a class="nav-link" href="https://www.furio.io/whale-tax">Whale Tax</a>
                    <a class="nav-link" href="https://www.furio.io/anti-dump-mechanics">Anti Dump Mechanics</a>
                    <a class="nav-link" href="https://www.furio.io/fast-programme/">FAST Programme</a>
                    <a class="nav-link" href="https://www.furio.io/furpool/">Furpool</a>
                    <a class="nav-link" href="https://app.furio.io/addressbook">Address Book</a>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column justify-content-between footer-custom-spacing">
            <h5>Contact Us</h5>
            <a class="nav-link" href="mailto:info@furio.io" :style="{padding: '0 !important', margin: '70px 0 70px 0 !important'}">info@furio.io</a>
            <a href="https://solidity.finance/audits/Furio/" target="_blank"><img src="../../images/solidity-banner.svg" alt="Solidity Banner"/></a>
        </div>
    </div>
</template>

<script>
import { computed, ref } from "vue";
import { useStore } from "vuex";
import axios from 'axios';
import useAlerts from "../composables/useAlerts";
export default {
    data() {
        return {
            coinprice: null
        }
    },
    methods: {
        getFurioPrice: async function() {
            let val;
            await axios.get('https://api.coingecko.com/api/v3/simple/price?ids=furio&vs_currencies=usd&include_market_cap=false&include_24hr_vol=false&include_24hr_change=false&include_last_updated_at=false').then(function(res){
                console.log("furio price", res.data.furio.usd);    
                val = res.data.furio.usd;
            });
            this.coinprice = val;
        }
    },
    mounted() {
        this.getFurioPrice();
    },
    setup () {
        
        const store = useStore();
        const alerts = useAlerts();

        const refLink = computed(() => {
            if(!store.state.wallet.loggedIn) {
                return null;
            }
            return location.protocol + "//" + location.host + "?ref=" + store.state.wallet.address;
        });

        const moveToSwap = () => {
            window.location = "https://app.furio.io/swap";
            
        }

        const copyRefLink = () => {
            navigator.clipboard.writeText(refLink.value);
            alerts.info("Referral link copied");
        }

        return  {
            store,
            refLink,
            copyRefLink,
            moveToSwap,
        }
    }
}
</script>
