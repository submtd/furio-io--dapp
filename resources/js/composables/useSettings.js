import axios from "axios";
import { useStore } from "vuex";
//import useAddressBook from "./useAddressBook";
export default () => {
    //const addressBook = useAddressBook();
    const store = useStore();

    const update = async () => {
        const response = await axios.get("/api/v1/settings");
        const settings = store.state.settings;
        for(const property in response.data) {
            settings[property] = response.data[property];
        }
        // get time offset
        settings.time_offset = settings.server_time - (Date.now() / 1000);
        // get addresses
        //if(store.state.wallet.loggedIn && typeof store.state.settings.vault_address == "undefined") {
            //settings.claim_address = await addressBook.getAddress("claim");
            //settings.downline_address = await addressBook.getAddress("downline");
            //settings.presale_address = await addressBook.getAddress("presale");
            //settings.swap_address = await addressBook.getAddress("swap");
            //settings.token_address = await addressBook.getAddress("token");
            //settings.payment_address = await addressBook.getAddress("payment");
            //settings.vault_address = await addressBook.getAddress("vault");
            //settings.safe_address = await addressBook.getAddress("safe");
        //}
        // commit settings
        store.commit("settings", settings);
        // update token info
        //if(Date.now() - settings.last_token_update > 300000 && settings.token_address && settings.vault_address) {
            //const token = new web3.eth.Contract(JSON.parse(settings.token_abi), settings.token_address);
            //const totalSupply = await token.methods.totalSupply().call();
            //const vaultSupply = await token.methods.balanceOf(settings.vault_address).call();
            //const circulatingSupply = BigInt(totalSupply - vaultSupply).toString();
            //await axios.post("/api/v1/updatetoken", {
                //total_supply: totalSupply,
                //circulating_supply: circulatingSupply,
            //});
        //}
        dispatchEvent(new Event("refresh"));
    }

    return {
        update,
    }
}
