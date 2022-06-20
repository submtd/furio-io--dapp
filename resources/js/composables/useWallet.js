import { useStore } from "vuex";
import WalletConnectProvider from "@walletconnect/web3-provider";
import router from "../router";
import useAlerts from "./useAlerts";
import useSettings from "./useSettings";
import useCookies from "./useCookies";
import axios from "axios";

export default () => {
    const store = useStore(); // Global storage.
    const alerts = useAlerts(); // Alerts.
    const cookies = useCookies(); // Cookies.
    const settings = useSettings(); // Settings.
    let connected = false; // Connected?
    let wallet = null; // Current wallet.

    if(typeof window.ethereum != "undefined") {
        // Watch for accountsChanged event.
        window.ethereum.on('accountsChanged', async function () {
            if(connected) {
                await loadWallet();
                return;
            }
            await connect();
        });
        // Watch for networkChanged event.
        window.ethereum.on('networkChanged', async function () {
            if(connected) {
                await checkNetwork();
                return;
            }
            await connect();
        });
    }

    // Get connected state.
    const isConnected = () => {
        return connected;
    }

    // Get current wallet.
    const getWallet = () => {
        return wallet;
    }

    // Connect with metamask.
    const metamask = () => {
        // If mobile or doesn't have mm installed, redirect.
        if (typeof window.ethereum == "undefined") {
            //window.location.href = "https://metamask.app.link/dapp/" + location.hostname;
            const a = document.createElement("a");
            a.href = "https://metamask.app.link/dapp/" + location.hostname;
            a.target = "_self";
            document.body.appendChild(a);
            a.click();
            a.remove();
            return;
        }
        cookies.set("provider", "metamask");
        web3.setProvider(window.ethereum);
        return connect();
    }

    // Connect with walletconnect.
    const walletconnect = () => {
        const provider = new WalletConnectProvider({
            rpc: {
                [parseInt(store.state.settings.network_id)]: store.state.settings.rpc_url,
            },
        });
        cookies.set("provider", "walletconnect");
        web3.setProvider(provider);
        return connect();
    }

    // Connect to correct network.
    const checkNetwork = async () => {
        // Switch to correct network.
        if(typeof store.state.settings.network_id == "undefined") {
            return;
        }
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
            }
        }
    }

    // Connect to wallet.
    const connect = async () => {
        settings.update();
        if(connected) {
            // Already connected.
            return;
        }
        if(!web3.currentProvider) {
            if(cookies.get("provider") == "metamask") {
                return metamask();
            }
            if(cookies.get("provider") == "walletconnect") {
                return walletconnect();
            }
            // Need to pick a provider
            router.push("/connect");
            return;
        }
        // Enable provider.
        try {
            await web3.currentProvider.enable();
            connected = true;
            // Switch to correct network.
            await checkNetwork();
            await loadWallet();
        } catch (error) {
            alerts.danger(error.message);
            return disconnect();
        }
        alerts.clear();
        settings.update();
    }

    // Disconnect.
    const disconnect = async () => {
        connected = false;
        wallet = null;
        const storedWallet = {
            address: null,
            shortAddress: null,
            nonce: null,
            loggedIn: false,
            name: null,
        };
        store.commit("wallet", storedWallet);
        cookies.remove("provider");
        cookies.remove("wallet");
        try {
            web3.currentProvider.disconnect();
        } catch(error) {}
        try {
            web3.currentProvider.close();
        } catch(error) {}
        await axios.get("/api/v1/logout");
        router.push("/connect");
    }

    // Load wallet.
    const loadWallet = async () => {
        if(!connected) {
            await connect();
            return;
        }
        // Get address.
        let address = await web3.eth.getAccounts();
        if(wallet && address[0] == wallet.attributes.address) {
            return;
        }
        wallet = await lookupAddress(address[0]);
        const storedWallet = {
            address: wallet.attributes.address,
            shortAddress: wallet.attributes.shortAddress,
            nonce: wallet.attributes.nonce,
            loggedIn: true,
            name: wallet.attributes.name,
        };
        store.commit("wallet", storedWallet);
        cookies.set("wallet", address[0]);
        dispatchEvent(new Event("refresh"));
    }

    // Lookup address.
    const lookupAddress = async (address) => {
        try {
            const response = await axios.get("/api/v1/address/" + address);
            response.data.data.attributes.shortAddress = response.data.data.attributes.address.substr(0, 4) + "..." + response.data.data.attributes.address.substr(-4);
            return response.data.data;
        } catch (error) {
            alerts.danger(error.message);
            return disconnect();
        }
    }

    connect();

    return {
        isConnected,
        getWallet,
        metamask,
        walletconnect,
        connect,
        disconnect,
        lookupAddress,
    }
}
