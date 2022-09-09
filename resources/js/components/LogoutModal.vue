<template>
    <div class="modal fade" id="logoutmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered logoutmodal-center">
            <div class="modal-content logoutmodal-c" :style="{width: '430px', height: '280px'}">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Your Wallet</h5>
                    <hr/>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body d-flex flex-column justify-content-between px-4">
                    <div>
                        <div class="d-flex flex-row justify-content-between">
                                <p class="d-flex flex-column justify-content-center" :style="{margin: 0, color: 'green'}">Connected</p>
                        </div>
                        <p class="ctext-addr">{{store.state.wallet.address}}</p>
                        <div class="d-flex">
                                <a @click="clipCopyAddr" class="ctext-logout font-weight-bold">Copy Address <i class="bi bi-subtract ctext-addr-scan"></i></a>
                                <a @click="goToExp" class="ctext-logout ml-3">View On BscScan <i class="bi bi-box-arrow-in-up-right ctext-addr-scan"></i></a>
                        </div>
                   </div>
                   <div>
                        <p :style="{margin: 0, fontWeight: '700', color: '#000000', fontSize: '14px'}">Referral Link:</p>
                        <p :style="{margin: 0, fontSize: '11px', color: '#000000', fontFamily: 'sans-serif', fontWeight: '450'}">{{refLink}}</p>
                        <a @click="clipCopyRefer" class="ctext-logout font-weight-bold">Copy Address <i class="bi bi-subtract ctext-addr-scan"></i></a>
                    </div>
                   <button class='btn btn-logout' @click="wallet.disconnect" data-dismiss="modal">Logout</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import useWallet from '../composables/useWallet';
    import {useStore} from 'vuex';
    import {computed} from 'vue';

    export default {
        
        setup() {
            const store = useStore();
            const wallet = useWallet();

            const refLink = computed(() => {
                if(!store.state.wallet.loggedIn) {
                    return null;
                }
                return location.protocol + "//" + location.host + "?ref=" + store.state.wallet.address;
            });

            const clipCopyAddr = () => {    
                const addr = store.state.wallet.address;
                navigator.clipboard.writeText(addr);
                alert("Copied");
            }

            const clipCopyRefer = () => {    
                let link = location.protocol + "//" + location.host + "?ref=" + store.state.wallet.address;
                navigator.clipboard.writeText(link);
                alert("Copied");
            }

            const goToExp = () => {
                const addr = store.state.wallet.address;
                const url = "https://bscscan.com/address/" + addr;
                window.open(url);
            }

            return {
                store,
                wallet,
                refLink,
                clipCopyAddr,
                clipCopyRefer,
                goToExp
            }


        }
    }
</script>