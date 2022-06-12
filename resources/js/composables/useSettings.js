import axios from "axios";
import { useStore } from "vuex";
import useAddressBook from "./useAddressBook";
import useDisplayCurrency from "./useDisplayCurrency";
export default () => {
    const addressBook = useAddressBook();
    const store = useStore();
    const displayCurrency = useDisplayCurrency();

    const update = async () => {
        const response = await axios.get("/api/v1/settings");
            const settings = store.state.settings;
            for(const property in response.data) {
                settings[property] = response.data[property];
            }
            // get time offset
            settings.time_offset = settings.server_time - (Date.now() / 1000);
            // get addresses
            if(store.state.wallet.loggedIn && typeof store.state.settings.vault_address == "undefined") {
                settings.claim_address = await addressBook.getAddress("claim");
                settings.downline_address = await addressBook.getAddress("downline");
                settings.presale_address = await addressBook.getAddress("presale");
                settings.swap_address = await addressBook.getAddress("swap");
                settings.token_address = await addressBook.getAddress("token");
                settings.payment_address = await addressBook.getAddress("payment");
                settings.vault_address = await addressBook.getAddress("vault");
            }
            // commit settings
            store.commit("settings", settings);
            // get balances
            if(store.state.wallet.logedIn && typeof store.state.balances.token == "undefined") {
                const balances = {};
                const tokenContract = new web3.eth.Contract(JSON.parse(store.state.settings.token_abi), store.state.settings.token_address);
                balances.token = displayCurrency.format(await tokenContract.methods.balanceOf(store.state.wallet.address).call());
                const paymentContract = new web3.eth.Contract(JSON.parse(store.state.settings.payment_abi), store.state.settings.payment_address);
                balances.payment = displayCurrency.format(await paymentContract.methods.balanceOf(store.state.wallet.address).call());
                store.commit("balances", balances);
            }
    }

    return {
        update,
    }
}
