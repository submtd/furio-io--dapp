<template>
    <nav class="navbar navbar-dark navbar-expand-xl pt-4 px-5 d-none d-md-block">
        <div class="container p-0 d-flex flex-row justify-content-between">

            <!-- <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button> -->
            <div class="d-flex flex-row align-items-center" id="navbar">
                <router-link :to="{ name: 'Home' }" class="navbar-brand mr-5">
                    <img class="d-none d-md-block" src="../../images/furio-logo.svg" alt="Furio Logo" height="60" />
                    <!-- <img class="d-md-none" src="../../images/furio-logo.svg" alt="Furio Icon" height="60"/> -->
                </router-link>
            </div>
            <button class="navbar-toggler d-none d-sm-none d-md-block d-lg-none" type="button" @click="openSidebar"
                aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation"
                :style="{ width: '40px', height: '40px', padding: 0, border: 'none !important' }">
                <span :style="{ width: '40px', height: '40px', color: '#e77b3b !important' }">&#9776;</span>
            </button>
            <div
                class="flex-row align-items-center justify-content-between d-none d-xs-none d-sm-none d-md-none d-lg-flex">
                <ul class="navbar-nav d-flex flex-row align-items-center mr-4">
                    <li class="nav-item px-2 mr-1">
                        <router-link :to="{ name: 'Home' }" class="nav-link nav-udl" active-class="active">Furswap
                            <hr />
                        </router-link>
                    </li>
                    <li class="nav-item px-1 mr-1">
                        <router-link :to="{ name: 'Vault' }" class="nav-link nav-udl" active-class="active">Furvault
                            <hr />
                        </router-link>
                    </li>
                    <li class="nav-item px-1 mr-1">
                        <router-link :to="{ name: 'LPStaking' }" class="nav-link nav-udl" active-class="active">Furpool
                            <hr />
                        </router-link>
                    </li>
                    <li class="nav-item px-1 mr-1">
                        <router-link :to="{ name: 'Team', params: { teamaddress: teamaddress } }"
                            class="nav-link nav-udl" active-class="active">Team
                            <hr />
                        </router-link>
                    </li>
                    <li class="nav-item px-1 mr-1">
                        <router-link :to="{ name: 'Downline' }" class="nav-link nav-udl" active-class="active">Downline
                            NFTs
                            <hr />
                        </router-link>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <!-- <li class="nav-item">
                        <button @click="profileLink" class="btn btn-link nav-link">{{ name }}</button>
                    </li> -->
                    <li class="nav-item">
                        <div v-show="!store.state.wallet.loggedIn">
                            <button type="button" class="btn btn-primary btn-conf" data-toggle="modal"
                                data-target="#loginmodal"><i class="bi bi-lock pr-2"></i>Connect Wallet</button>
                        </div>
                        <div v-show="store.state.wallet.loggedIn">
                            <button type="button" class="btn btn-primary btn-conf" data-toggle="modal"
                                data-target="#logoutmodal"><i class="bi bi-unlock pr-2"></i>{{ address }}</button>
                        </div>

                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <nav class="navbar navbar-dark bg-dark d-md-none bg-custom-mobile" :style="{ height: '62px' }">
        <!-- <img class="d-md-none" src="../../images/furio-logo.svg" alt="Furio Logo" height="40"/> -->
        <button class="navbar-toggler" type="button" @click="openSidebar" aria-controls="navbarToggleExternalContent"
            aria-expanded="false" aria-label="Toggle navigation"
            :style="{ position: 'absolute', width: '40px', height: '40px', padding: 0, border: 'none !important' }">
            <span :style="{ width: '40px', height: '40px', color: '#e77b3b !important' }">&#9776;</span>
        </button>
        <div class="d-flex flex-row justify-content-center w-100">
            <img class="d-md-none" src="../../images/furio-logo.svg" alt="Furio Logo" height="40" />
        </div>
    </nav>
    <LoginModal />
    <LogoutModal />
    <MobileMenu :visible="menuVisible" :onClose="closeSidebar" />
</template>

<script>
import { computed } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import router from "../router";
import useWallet from "../composables/useWallet";
import Web3 from "web3";
import LoginModal from './LoginModal.vue';
import LogoutModal from "./LogoutModal.vue";
import MobileMenu from './MobileMenu.vue';

export default {
    components: {
        LoginModal,
        LogoutModal,
        MobileMenu
    },
    data() {
        return {
            menuVisible: false,
        }
    },
    methods: {
        openSidebar() {
            this.menuVisible = true
            document.getElementById('app').style.position = 'fixed';
            document.getElementById('app').style.overflow = 'hidden';
        },
        closeSidebar() {
            this.menuVisible = false
            document.getElementById('app').style.position = 'relative';
            document.getElementById('app').style.overflow = 'scroll';
        }
    },
    setup() {
        const store = useStore();
        const wallet = useWallet();


        if(!Web3.currentProvider) {
            wallet.connect();
        }
        
        // const openSidebar =() => {
        //     document.getElementById('mobilemenu').style.width = '250px';
        //     document.getElementById('mobilemenu').style.height = '100vh';
        //     document.getElementById('mobilemenu').style.zIndex = '1';
        //     document.getElementById('mobilemenu').style.display = 'block'
        // }

        // const closeSidebar =() => {
        //     document.getElementById('mobilemenu').style.width = '0';
        //     document.getElementById('mobilemenu').style.height = '0';
        //     document.getElementById('mobilemenu').style.display = 'none'
        // }

        const name = computed(() => {
            return store.state.wallet.name ?? store.state.wallet.shortAddress;
        });

        //let addr = store.state.wallet.address;
        //const addrString = addr.slice(0,5) + "....." + addr.slice(addr.length-6, addr.length-1);
        const address = computed(() => {
            if(store.state.wallet.name != null) {
                return store.state.wallet.name;
            } else if(store.state.wallet.shortAddress != null) {
                return store.state.wallet.shortAddress;
            }
            return "0x00000000";
        });

        const teamaddress = computed(() => {
            return store.state.wallet.address ?? "0x0000000000000000000000000000000000000000";
        });



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
