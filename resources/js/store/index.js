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
            presaleNft: {
                updated: false,
                supply: 0,
                value: 0,
                price: 0,
                max: 0,
                ownedValue: 0,
                balance: 0,
                presaleOneStart: 0,
                presaleTwoStart: 0,
                presaleThreeStart: 0,
                claimStart: 0,
                nextRound: null,
            },
            usdc: {

            },
            eth:-1,
            referrer: null,
            loading: true,
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
        presaleNft(state, value) {
            state.presaleNft = value;
        },
        balances(state, value) {
            state.balances = value;
        },
        eth(state, value) {
            state.eth = value;
        },
        referrer(state, value) {
            state.referrer = value;
        },
        loading(state, value) {
            state.loading = value;
        }
    }
});
