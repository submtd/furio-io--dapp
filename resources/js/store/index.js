import { createStore } from "vuex";

export default createStore({
    state () {
        return {
            alert: null,
            loggedIn: false,
            settings: {},
            wallet: {
                address: null,
                shortAddress: null,
                nonce: null,
                loggedIn: false,
                name: null,
                email: null,
                emailVerificationCode: null,
                emailVerifiedAt: null,
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
    }
});
