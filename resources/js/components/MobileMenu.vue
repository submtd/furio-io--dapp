<template>
    <div>
        <div id="mobilemenu"
            :style="{ width: '250px', height: '100%', position: 'absolute', background: '#181c2c', transition: 'left 500ms', zIndex: 20, top: 0, left: visible ? '0px' : '-250px' }">
            
            <div class="d-flex flex-column flex-wrap align-items-left justify-content-between cheight">
                <a href="javascript:void(0)" class="closebtn" @click="closeMenu">&times;</a>
                <div v-show="!store.state.wallet.loggedIn">
                    <button type="button" @click="closeMenu" class="btn btn-conf-mobile"
                        data-toggle="modal" data-target="#loginmodal">Connect
                        Wallet</button>
                </div>
                <div v-show="store.state.wallet.loggedIn">
                    <button type="button" @click="closeMenu" class="btn btn-conf-mobile"
                        data-toggle="modal" data-target="#logoutmodal">{{
                                address
                        }}</button>
                </div>
                <router-link :to="{ name: 'Home' }" class="nav-link nav-udl nav-udl-mobile" active-class="active"
                    @click="closeMenu">Furswap
                    <hr />
                </router-link>
                <router-link :to="{ name: 'Vault' }" class="nav-link nav-udl nav-udl-mobile" active-class="active"
                    @click="closeMenu">Furvault
                    <hr />
                </router-link>
                <router-link :to="{ name: 'LPStaking' }" class="nav-link nav-udl nav-udl-mobile" active-class="active"
                    @click="closeMenu">Furpool
                    <hr />
                </router-link>
                <router-link :to="{ name: 'Team', params: { teamaddress: teamaddress } }"
                    class="nav-link nav-udl nav-udl-mobile" active-class="active" @click="closeMenu">Team
                    <hr />
                </router-link>
                <router-link :to="{ name: 'Downline' }" class="nav-link nav-udl nav-udl-mobile" active-class="active"
                    @click="closeMenu">Downline NFTs
                    <hr />
                </router-link>
                <a href="https://furio.io" target="_blank" class="nav-udl-mobile" :style="{color: '#FFFFFF', paddingLeft: '35px'}">Website</a>
                
                <div class="d-flex flex-row justify-content-start pl-5">
                    <img src="../../images/fur.svg"  alt="FUR" width="18" height="18"/>
                    <span class="bal-font-mobile pl-2 pr-4">$FUR Balance: {{ store.state.balances.token ?? 0}}</span>
                </div> 
            
                <div class="d-flex flex-row justify-content-start pl-5">
                    <img src="../../images/usdc.svg" alt="USDC" width="18" height="18"/>
                    <span class="bal-font-mobile pl-2 pr-4">USDC Balance: {{ store.state.balances.payment ?? 0 }}</span>
                </div>
            
                <div class="d-flex flex-row justify-content-start pl-5">
                    <img src="../../images/fur.svg"  alt="FUR" width="18" height="18"/>
                    <span class="bal-font-mobile pl-2 pr-4">Vault Balance: {{ store.state.balances.vault ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="background-overlay"
            :style="{ width: '100vw', height: '100vh', background: '#00000085', position: 'fixed', top: 0, left: 0, zIndex: 10, display: visible ? 'block' : 'none' }"
            @click="closeMenu"></div>
        <LoginModal />
        <LogoutModal />
    </div>
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

export default {
    components: {
        LoginModal,
        LogoutModal,
    },
    props: ['visible', 'onClose'],
    data() {
        return {
            menuVisible: false,
        }
    },
    watch: {
        visible(newVisible) {
            if (newVisible) {
                this.menuVisible = newVisible;
            } else {
                setTimeout(() => {
                    this.menuVisible = newVisible;
                }, 500);
            }
        }
    },
    methods: {
        closeMenu() {
            this.$props.onClose();
        }
    },
    setup() {
        const store = useStore();
        const wallet = useWallet();



        const openSidebar = () => {
            document.getElementById('mobilemenu').style.width = '250px';
            document.getElementById('mobilemenu').style.height = '100%';
            document.getElementById('mobilemenu').style.zIndex = '1';
            document.getElementById('mobilemenu').style.display = 'block'
        }

        const closeSidebar = () => {
            document.getElementById('mobilemenu').style.display = 'none'
        }

        const name = computed(() => {
            return store.state.wallet.name ?? store.state.wallet.shortAddress;
        });

        // let addr = store.state.wallet.address;
        // const addrString = addr.slice(0,5) + "....." + addr.slice(addr.length-6, addr.length-1);
        const address = computed(() => {
            let addr = store.state.wallet.address;
            if (addr == null) return "0x0000000";
            const addrString = addr.slice(0, 5) + "....." + addr.slice(addr.length - 4, addr.length);
            return addrString;
        });

        const teamaddress = computed(() => {
            return store.state.wallet.address ?? "0x0000000000000000000000000000000000000000";
        });

        if (!Web3.currentProvider) {
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
            openSidebar,
            closeSidebar,
        }
    }
}

</script>