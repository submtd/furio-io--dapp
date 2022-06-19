import { useStore } from "vuex";
import useAlerts from "./useAlerts";
import router from "../router";
import WalletConnectProvider from "@walletconnect/web3-provider";
import Cookies from "js-cookies";
import axios from "axios";
export default () => {
    const store = useStore();
    const alerts = useAlerts();

    const wallet = {
        loggedIn: false,
        address: null,
        shortAddress: null,
        nonce: null,
        name: null,
    }

    const accounts = [];

    const getWallet = () => {
        return wallet;
    }

    const getAccounts = () => {
        return accounts;
    }

    const lookupAddress = async (address) => {
        await axios.get("/api/v1/address/" + address
        ).then(response => {
            accounts[address] = response.data;
        }).catch(error => {
            alerts.danger(error.message);
            return;
        });
        return accounts[address];
    }

    const switchWallet = async (address) => {
        if(!accounts.includes(address)) {
            alerts.danger("Invalid account");
            return disconnect();
        }
        wallet.loggedIn = true;
        wallet.address = address;
        wallet.shortAddress = address.substr(0, 4) + "..." + address.substr(-4);
        await axios.post("/api/v1/address", {
            address: address,
        }).then(response => {
            wallet.nonce = response.data.nonce;
            wallet.name = response.data.name;
        }).catch(error => {
            alerts.danger(error.message);
            return disconnect();
        });
        Cookies.setItem("wallet", address);
        router.push("/");
    }

    const metamask = () => {
        if (typeof window.ethereum == 'undefined') {
            window.location.href = 'https://metamask.app.link/dapp/' + location.hostname;
            return;
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
        if(wallet.loggedIn) {
            // already connected
            return;
        };
        if(!web3.currentProvider) {
            if(Cookies.getItem('provider') == "metamask") {
                return metamask();
            }
            if(Cookies.getItem('provider') == "walletconnect") {
                return walletconnect();
            }
            router.push("/connect");
            return;
        }
        try {
            await web3.currentProvider.enable();
            // switch to correct network
            if(parseInt(await web3.eth.net.getId()) != parseInt(store.state.settings.network_id)) {
                try {
                    await web3.currentProvider.request({
                        method: "wallet_switchEthereumChain",
                        params: [{
                            chainId: web3.utils.toHex(store.state.settings.network_id),
                        }],
                    });
                } catch(error) {
                    alerts.danger("Incorrect network. Please connect to " + store.state.settings.network_name);
                    return disconnect();
                }
            }
            let addresses = await web3.eth.getAccounts();
            addresses.each(address => {
                lookupAddress(address);
            });
            let address = Cookies.getItem("wallet");
            if(!address) {
                address = accounts[addresses[0]];
            }
            alerts.clear();
            return switchWallet(address);
        } catch (error) {
            alerts.danger(error.message);
        }
    }

    const disconnect = async () => {
        Cookies.removeItem("provider");
        Cookies.removeItem("wallet");
        try {
            web3.currentProvider.disconnect();
        } catch(error) {}
        try {
            web3.currentProvider.close();
        } catch(error) {}
        await axios.get("/api/v1/logout");
        wallet.loggedIn = false;
        wallet.address = null;
        wallet.shortAddress = null;
        wallet.nonce = null;
        wallet.name = null;
        router.push("/connect");
    }

    return {
        getWallet,
        getAccounts,
        metamask,
        walletconnect,
        connect,
        disconnect,
    }
}
