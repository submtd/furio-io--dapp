<template>
    <div class="modal fade" id="logoutmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Your Wallet</h5>
                    <hr/>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   <div class="d-flex flex-row justify-content-around">
                        <p :style="{color: 'green'}">Connected</p>
                        <button>change</button>
                   </div>
                   <p>{{store.state.wallet.address}}</p>
                   
                   <button>Log Out</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import useWallet from '../composables/useWallet';
    import {useStore} from 'vuex';
    export default {
        setup() {
            const store = useStore();
            const wallet = useWallet();
            return {
                store,
                wallet
            }
        }
    }
</script>