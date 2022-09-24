import { useStore } from "vuex";
import WalletConnectProvider from "@walletconnect/web3-provider";
import router from "../router";
import useAlerts from "./useAlerts";
import useCookies from "./useCookies";
import axios from "axios";
import useSettings from "./useSettings";

export default () => {
    const store = useStore(); // Global storage.
    const settings = useSettings();
    const alerts = useAlerts(); // Alerts.
    const cookies = useCookies(); // Cookies.
    let connected = false; // Connected?
    let wallet = null; // Current wallet.

    console.log("Blockchain Network Version: ",window.ethereum.networkVersion);

    if(typeof window.ethereum != "undefined") {
        // Watch for accountsChanged event.
        window.ethereum.on('accountsChanged', async function () {
            if(connected) {
                await loadWallet();
                return;
            }
            //await connect();
        });
        // Watch for networkChanged event.
        window.ethereum.on('chainChanged', async function () {
            if(connected) {
                await checkNetwork();
                return;
            }
            //await connect();
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
        console.log("metamask");
        // If mobile or doesn't have mm installed, redirect.
        if (typeof window.ethereum == "undefined") {
            window.location.href = "https://metamask.app.link/dapp/" + location.hostname;
            return;
        }
        cookies.set("provider", "metamask");
        web3.setProvider(window.ethereum);
        console.log("Wallet Address:", store.state.wallet.address);
        return connect();
    }

    // Connect with walletconnect.
    const walletconnect = () => {
        console.log("wallet connect");
        const provider = new WalletConnectProvider({
            rpc: {
                [parseInt(store.state.settings.network_id)]: store.state.settings.rpc_url,
            },
            chainId: 56,
            network: 'binance',
            qrcode: true,
            qrcodeModalOptions: {
                mobileLinks: [
                    "metamask",
                    "trust"
                ]
            }
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
        console.log("Settings of Network ID and Name: ", store.state.settings.network_id);
        if(parseInt(await web3.eth.net.getId()) != parseInt(store.state.settings.network_id)) {
            try {
                await web3.currentProvider.request({
                    method: "wallet_switchEthereumChain",
                    params: [{
                        chainId: web3.utils.toHex(store.state.settings.network_id),
                    }],
                });
            } catch(error) {
                console.error(error);
                alerts.danger("Incorrect network. Please connect to " + store.state.settings.network_name);
            }
        }
    }

    // Connect to wallet.
    const connect = async () => {
        //settings.update();
        console.log("connect");
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
            router.push("/");
            return;
        }
        // Enable provider.
        try {
            await web3.currentProvider.enable();
            connected = true;
            // Switch to correct network.
            await checkNetwork();
            await loadWallet();
            alerts.clear();
            router.push("/");
        } catch (error) {
            console.error(error);
            console.log("error");
            //alerts.danger(error.message);
            return disconnect();
        }
        settings.update();
    }

    // Disconnect.
    const disconnect = async () => {
        connected = true;
        wallet = null;
        const storedWallet = {
            address: null,
            shortAddress: null,
            nonce: null,
            loggedIn: false,
            name: null,
        };
        console.log("called2")
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
        location.href = "/";
        //router.push("/connect");
        console.log("disconnect funtion is called!")
    }

    // Load wallet.
    const loadWallet = async () => {
        if(!connected) {
            await connect();
            return;
        }
        // Get address.
        let address = await web3.eth.getAccounts();
        console.log("address: ", address);
        //let address = await web3.eth.requestAccounts();
        if(wallet && address[0] == wallet.attributes.address) {
            return;
        }
        wallet = await lookupAddress(address[0]);
        console.log("wallet: ", wallet);
        const storedWallet = {
            address: wallet.attributes.address,
            shortAddress: wallet.attributes.shortAddress,
            nonce: wallet.attributes.nonce,
            loggedIn: true,
            name: wallet.attributes.name,
        };
        console.log("called1")
        store.commit("wallet", storedWallet);
        cookies.set("wallet", address[0]);
        dispatchEvent(new Event("refresh"));
        console.log("loadWallet");
    }

    // Lookup address.
    const lookupAddress = async (address) => {
        try {
            const response = await axios.get("/api/v1/address/" + address);
            response.data.data.attributes.shortAddress = response.data.data.attributes.address.substr(0, 4) + "..." + response.data.data.attributes.address.substr(-4);
            return response.data.data;
        } catch (error) {
            console.log(error);
            //alerts.danger(error.message);
            return disconnect();
        }
    }
    const addFurioToken = async() => {
        const tokenAddress = '0x48378891d6E459ca9a56B88b406E8F4eAB2e39bF';
        const tokenSymbol = '$FUR';
        const tokenDecimals = 18;

        if(!web3.currentProvider) {
            alert("Please connect wallet!");
            return ;
        }

        try {
            const isAdded = await web3.currentProvider.request({
                method: 'wallet_watchAsset',
                params: {
                type: 'ERC20',
                options: {
                    address: tokenAddress,
                    symbol: tokenSymbol,
                    decimals: tokenDecimals,
                    // image: tokenImage, // if you have the image, it goes here
                    },
                },
            });
        } catch (error) {
            console.log("Can't add the Furio token!")
        }
    }

    return {
        isConnected,
        getWallet,
        metamask,
        walletconnect,
        connect,
        disconnect,
        lookupAddress,
        addFurioToken
    }
}
