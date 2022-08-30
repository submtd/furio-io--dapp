<template>
        <nav class="navbar navbar-dark navbar-expand-xl pt-4 px-5">
            <div class="container p-0">
                <router-link :to="{ name: 'Home' }" class="navbar-brand">
                    <img class="d-none d-md-block" src="../../images/furio-logo.svg" alt="Furio Logo" height="60"/>
                    <img class="d-md-none" src="../../images/furio-icon.svg" alt="Furio Icon" height="60"/>
                </router-link>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse d-flex justify-content-end" id="navbar">
                    <ul class="navbar-nav ml-auto mr-5">
                        <li class="nav-item ml-4">
                            <router-link :to="{ name: 'Home' }" class="nav-link nav-udl" active-class="active">Furswap<hr/></router-link>
                        </li>
                        <li class="nav-item ml-4">
                            <router-link :to="{ name: 'Vault' }" class="nav-link nav-udl" active-class="active">Furvault<hr/></router-link>
                        </li>
                        <li class="nav-item ml-4">
                            <router-link :to="{ name: 'LPStaking' }" class="nav-link nav-udl" active-class="active">Furpool<hr/></router-link>
                        </li>
                        <li class="nav-item ml-4">
                            <router-link :to="{ name: 'Team', params: { teamaddress: teamaddress }}" class="nav-link nav-udl" active-class="active">Team<hr/></router-link>
                        </li>
                        <li class="nav-item ml-4">
                            <router-link :to="{ name: 'Downline' }" class="nav-link nav-udl" active-class="active">Downline NFTs<hr/></router-link>
                        </li>
                    </ul>
                    <div class="d-flex">
                        <ul class="navbar-nav flex-column text-right">
                            <!-- <li class="nav-item">
                                <button @click="profileLink" class="btn btn-link nav-link">{{ name }}</button>
                            </li> -->
                            <li class="nav-item">
                                <div v-show="!store.state.wallet.loggedIn">
                                    <button type="button" class="btn btn-primary btn-conf"  data-toggle="modal" data-target="#loginmodal"><i class="bi bi-lock pr-2"></i>Connect Wallet</button>
                                </div>
                                <div v-show="store.state.wallet.loggedIn">
                                    <button type="button" class="btn btn-primary btn-conf" data-toggle="modal" data-target="#logoutmodal"><i class="bi bi-unlock pr-2"></i>{{address}}</button>
                                </div>
                                <LoginModal/>
                                <LogoutModal/>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
</template>

<script>
import { computed } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import router from "../router";
import useWallet from "../composables/useWallet";
import Web3 from "web3";
import LoginModal from '../components/LoginModal.vue';
import LogoutModal from "../components/LogoutModal.vue";

export default {
    components: {
        LoginModal,
        LogoutModal
    },
    setup () {
        const store = useStore();
        const wallet = useWallet();

        const name = computed(() => {
            return store.state.wallet.name ?? store.state.wallet.shortAddress;
        });

        // let addr = store.state.wallet.address;
        // const addrString = addr.slice(0,5) + "....." + addr.slice(addr.length-6, addr.length-1);
        const address = computed(() => {
            let addr = store.state.wallet.address;
            if(addr == null) return "0x0000000";
            const addrString = addr.slice(0,5) + "....." + addr.slice(addr.length-4, addr.length);
            return addrString;
        });

        const teamaddress = computed(() => {
            return store.state.wallet.address ?? "0x0000000000000000000000000000000000000000";
        });

        if(!Web3.currentProvider) {
            wallet.connect();
        }

        //if(!store.state.wallet.loggedIn && useRoute().name != 'Connect') {
            //router.push("/connect");
        //}

        const profileLink = () => {
            router.push("/team/" + store.state.wallet.address);
        }

        return {
            store,
            wallet,
            name,
            teamaddress,
            address,
            profileLink,
        }
    }
}
</script>
