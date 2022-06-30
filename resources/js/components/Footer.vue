<template>
    <div class="container">
        <div v-show="store.state.wallet.loggedIn" class="bg-light text-dark rounded p-5 mb-4 text-center">
            Referral Link: <strong>{{ refLink }}</strong> <button @click="copyRefLink" class="btn btn-sm btn-info">Copy Link</button>
        </div>
    </div>
    <div class="footer border-top border-secondary pt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 pe-5">
                    <img src="../../images/furio-logo.svg" alt="Furio Logo" class="img-fluid">
                    <p class="mt-4 text-left">Decentralised Finance</p>
                </div>
                <div class="col-md-3">
                    <h5 class="border-bottom border-primary pb-3">Contact Us</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="mailto:info@furio.io"><i class="fa-solid fa-envelope"></i> info@furio.io</a></li>
                        <li class="nav-item"><a class="nav-link" href="mailto:partners@furio.io"><i class="fa-solid fa-envelope"></i> partners@furio.io</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="border-bottom border-primary pb-3">Useful Links</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="https://furio.io/how-it-works/"><i class="fa-solid fa-link"></i> How It Works</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://furio.io/referral-system"><i class="fa-solid fa-link"></i> Referral System</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://furio.io/variable-rewards"><i class="fa-solid fa-link"></i> Variable Rewards</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://furio.io/taxes"><i class="fa-solid fa-link"></i> Taxes</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://furio.io/whale-tax"><i class="fa-solid fa-link"></i> Whale Tax</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://www.furio.io/anti-dump-mechanics/"><i class="fa-solid fa-link"></i> Anti Dump Mechanics</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://www.furio.io/fast-programme/"><i class="fa-solid fa-link"></i> FAST Programme</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://www.furio.io/fast-teams/"><i class="fa-solid fa-link"></i> FAST Teams</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://www.furio.io/how-to-guides/"><i class="fa-solid fa-link"></i> How To Guides</a></li>
                        <li v-show="store.state.wallet.loggedIn" class="nav-item"><router-link :to="{ name: 'Promo' }" class="nav-link"><i class="fa-solid fa-link"></i> Check for Promos</router-link></li>
                        <li v-show="store.state.wallet.loggedIn" class="nav-item"><router-link :to="{ name: 'AddressBook' }" class="nav-link"><i class="fa-solid fa-link"></i> Address Book</router-link></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="border-bottom border-primary pb-3">Connect</h5>
                    <nav class="nav social">
                        <a class="nav-link" href="https://twitter.com/furiocrypto"><img class="img-fluid" src="../../images/twitter.svg" width="32" height="32" alt="Twitter Logo"></a>
                        <a class="nav-link" href="https://t.me/furiocrypto"><img class="img-fluid" src="../../images/telegram.svg" width="32" height="32" alt="Telegram Logo"></a>
                        <a class="nav-link" href="https://discord.gg/furio"><img class="img-fluid" src="../../images/discord.svg" width="32" height="32" alt="Discord Logo"></a>
                        <a class="nav-link" href="https://furiocrypto.medium.com/"><img class="img-fluid" src="../../images/medium.svg" width="32" height="32" alt="Medium Logo"></a>
                        <a class="nav-link" href="https://www.facebook.com/furiocrypto"><img src="../../images/facebook.svg" width="32" height="32" alt="Facebook Logo"></a>
                        <a class="nav-link" href="https://www.instagram.com/furiocrypto"><img class="img-fluid" src="../../images/instagram.svg" width="32" height="32" alt="Instagram Logo"></a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { computed } from "vue";
import { useStore } from "vuex";
import useAlerts from "../composables/useAlerts";
export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();

        const refLink = computed(() => {
            if(!store.state.wallet.loggedIn) {
                return null;
            }
            return location.protocol + "//" + location.host + "?ref=" + store.state.wallet.address;
        });

        const copyRefLink = () => {
            navigator.clipboard.writeText(refLink.value);
            alerts.info("Referral link copied");
        }

        return  {
            store,
            refLink,
            copyRefLink,
        }
    }
}
</script>
