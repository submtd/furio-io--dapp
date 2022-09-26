import { useStore } from "vuex";
import useAlerts from "./useAlerts";

export default () => {
    const store = useStore();
    const alerts = useAlerts();

    const getAddress = async (contract) => {
        try {
            const addressBook = new web3.eth.Contract(JSON.parse(store.state.settings.addressbook_abi), store.state.settings.addressbook_address);
            const address = await addressBook.methods.get(contract).call();
            return address;
        } catch (error) {
            alerts.danger(error.message);
        }
    }

    return {
        getAddress,
    }
}
