import { useStore } from "vuex";
import useAlerts from "./useAlerts";
import useDisplayCurrency from "./useDisplayCurrency";

export default () => {
    const store = useStore();
    const alerts = useAlerts();
    const displayCurrency = useDisplayCurrency();
    let tokenBalance = 0
    let paymentBalance = 0;

    const refresh = async () => {
        try {
            const token = new web3.eth.Contract(JSON.parse(store.state.settings.token_abi), store.state.settings.token_address);
            const payment = new web3.eth.Contract(JSON.parse(store.state.settings.payment_abi), store.state.settings.payment_address);
            tokenBalance = displayCurrency.format(await token.methods.balanceOf(store.state.wallet.address).call())
            paymentBalance = displayCurrency.format(await payment.methods.balanceOf(store.state.wallet.address).call());
        } catch (error) {
            alerts.danger(error.message);
        }
    }

    return {
        tokenBalance,
        paymentBalance,
        refresh,
    }
}
