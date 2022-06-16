import { createStore } from "vuex";

export default createStore({
    state () {
        return {
            alert: null,
            loggedIn: false,
            settings: {},
            balances: {},
            wallet: {
                address: null,
                shortAddress: null,
                nonce: null,
                loggedIn: false,
                name: null,
            },
            wallets: [],
        }
    },
    mutations: {
        alert(state, value) {
            state.alert = value;
        },
        loggedIn(state, value) {
            state.loggedIn = value;
        },
        settings(state, value) {
            state.settings = value;
        },
        wallet(state, value) {
            state.wallet = value;
        },
        wallets(state, value) {
            state.wallets = value;
        },
        presaleNft(state, value) {
            state.presaleNft = value;
        },
        balances(state, value) {
            state.balances = value;
        },
    }
});
