import { ref } from "vue";
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

    const buy = async () => {

    }

    return {
        locked,
        getPaymentContract,
        getContract,
        getContractData,
    }
}
