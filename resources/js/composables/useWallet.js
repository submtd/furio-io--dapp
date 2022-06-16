import { useStore } from "vuex";
import useAlerts from "./useAlerts";
import useSettings from "./useSettings";
import router from "../router";
import WalletConnectProvider from "@walletconnect/web3-provider";
import Cookies from "js-cookies";
export default () => {
    const store = useStore();
    const alerts = useAlerts();
    const settings = useSettings();

    const metamask = () => {
        if (typeof window.ethereum == 'undefined') {
            window.location.href = 'https://metamask.app.link/dapp/' + location.hostname;
            return false;
        }
        Cookies.setItem('provider', 'metamask');
        web3.setProvider(window.ethereum);
        connect();
    }

    const walletconnect = () => {
        const provider = new WalletConnectProvider({
            rpc: {
                [parseInt(store.state.settings.network_id)]: store.state.settings.rpc_url,
            }
        });
        Cookies.setItem('provider', 'walletconnect');
        web3.setProvider(provider);
        connect();
    }

    const connect = async () => {
        if(store.state.wallet.loggedIn) {
            return;
        }
        if(!web3.currentProvider) {
            if(Cookies.getItem('provider') == "metamask") {
                return metamask();
            }
            if(Cookies.getItem('provider') == "walletconnect") {
                return walletconnect();
            }
            router.push("/connect");
        }
        await settings.update();
        try {
            if(!web3.currentProvider) {
                return;
            }
            await web3.currentProvider.enable();
            if(parseInt(await web3.eth.net.getId()) != parseInt(store.state.settings.network_id)) {
                alerts.danger("Incorrect network. Please connect to " + store.state.settings.network_name);
                return disconnect();
            }
            const accounts = await web3.eth.getAccounts();
            const wallet = null;
            const wallets = [];
            accounts.forEach(async (account) => {
                wallet.address = account;
                wallet.shortAddress = account.substr(0, 4) + "..." + account.substr(-4);
                await axios.post("/api/v1/address", {
                    address: account,
                }).then(response => {
                    wallet.nonce = response.data.nonce;
                    wallet.name = response.data.name;
                }).catch(error => {
                    alerts.danger(error.message);
                    return disconnect();
                });
                if(!wallet) {
                    wallet.loggedIn = true;
                    store.commit("wallet", wallet);
                    Cookies.setItem("wallet", wallet);
                }
                wallets.push(w);
            });
            store.commit("wallets", wallets);
            Cookies.setItem("wallets", wallets);
            alerts.clear();
            await settings.update();
            if(router.currentRoute.value.path == "/connect") {
                router.push("/");
            }
        } catch (error) {
            alerts.danger(error.message);
        }
    }

    const disconnect = async () => {
        Cookies.removeItem("provider");
        Cookies.removeItem("wallet");
        Cookies.removeItem("wallets");
        try {
            web3.currentProvider.disconnect();
        } catch(error) {}
        try {
            web3.currentProvider.close();
        } catch(error) {}
        await axios.get("/api/v1/logout");
        const wallet = {
            address: null,
            shortAddress: null,
            nonce: null,
            loggedIn: false,
            name: null,
        };
        store.commit("wallet", wallet);
        router.push("/connect");
    }

    return {
        metamask,
        walletconnect,
        connect,
        disconnect,
    }
}
