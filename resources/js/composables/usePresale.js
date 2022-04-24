import { useStore } from "vuex";
import useAlerts from "./useAlerts";
export default () => {
    const alerts = useAlerts();
    const store = useStore();

    const getPaymentContract = () => {
        return new web3.eth.Contract(JSON.parse(store.state.settings.payment_abi), store.state.settings.payment_address);
    }

    const getContract = () => {
        return new web3.eth.Contract(JSON.parse(store.state.settings.presale_abi), store.state.settings.presale_address);
    }

    const getAvailable = async (max, price, value) => {
        try {
            alerts.info("Getting available NFTs from contract");
            const contract = getContract();
            const available = await contract.methods.available(store.state.wallet.address, max, price, value).call();
            alerts.clear();
            return available;
        } catch (error) {
            alerts.danger(error.message);
        }
    }

    const buy = async () => {
    }

    return {
        getPaymentContract,
        getContract,
        getAvailable,
        buy,
    }
}
