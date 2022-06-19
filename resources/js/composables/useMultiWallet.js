import { useStore } from "vuex";
import Cookies from "js-cookies";
import WalletConnectProvider from "@walletconnect/web3-provider";
import router from "../router";
import useAlerts from "./useAlerts";

export default () => {
    const store = useStore(); // Global storage.
    const alerts = useAlerts(); // Alerts.
    const wallets = []; // Array of all wallets.
    const wallet = null; // Current wallet.

    // Get all wallets.
    const getWallets = () => {
        return wallets;
    }

    // Get current wallet.
    const getWallet = () => {
        return wallet;
    }

    // Connect with metamask.
    const metamask = () => {
        // If mobile or doesn't have mm installed, redirect.
        if (typeof window.ethereum == "undefined") {
            window.location.href = "https://metamask.app.link/dapp/" + location.hostname;
            return;
        }
        Cookies.setItem("provider", "metamask");
        web3.setProvider(window.ethereum);
    }

    // Connect with walletconnect.
    const walletconnect = () => {
        const provider = new WalletConnectProvider({
            rpc: {
                [parseInt(store.state.settings.network_id)]: store.state.settings.rpc_url,
            },
        });
        Cookies.setItem("provider", "walletconnect");
        web3.setProvider(provider);
    }

    // Connect to wallet.
    const connect = async () => {
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
        // Enable provider.
        alerts.info("Connecting to wallet");
        try {
            await web3.currentProvider.enable();
            // Switch to correct network.
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
            // Get all addresses.
            let addresses = await web3.eth.getAccounts();
            addresses.each(async (address) => {
                wallets[address] = lookupAddress(address);
            });
            // Connect to last connected address or first in the list.
            let address = Cookies.getItem("wallet");
            if(!address) address = addresses[0];
            wallet = wallets[address];
            Cookies.setItem("wallet", address);
        } catch (error) {
            alerts.danger(error.message);
            return disconnect();
        }
        alerts.clear();
    }

    // Disconnect.
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
        router.push("/connect");
    }

    // Lookup address.
    const lookupAddress = async (address) => {
        await axios.get("/api/v1/address/" + address
        ).then(response => {
            return response.data;
        }).catch(error => {
            alerts.danger(error.message);
            return;
        });
    }
}
