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
        //await axios.get("https://api.pancakeswap.info/api/v2/tokens/" + settings.token_address).then(function (res) {
            //settings.furio = res.data.price;
        //});
        //await axios.get("https://api.coingecko.com/api/v3/simple/price?ids=usd-coin&vs_currencies=usd&include_market_cap=false&include_24hr_vol=false&include_24hr_change=false&include_last_updated_at=false").then(function (res) {
            //settings.usdc = res.data['usd-coin']['usd'];
        //});
        //const swap = new web3.eth.Contract(JSON.parse(settings.swap_abi), settings.swap_address);
        //settings.furio = displayCurrency.format(await swap.methods.sellOutput("1000000000000000000").call());
        //settings.furio = 5.50;
        settings.usdc = 1;

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
