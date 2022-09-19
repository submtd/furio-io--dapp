import { useStore } from "vuex";
import useAlerts from "./useAlerts";
import useDisplayCurrency from "./useDisplayCurrency";

export default () => {
    const alerts = useAlerts();
    const store = useStore();
    const displayCurrency = useDisplayCurrency();

    addEventListener("refresh", async () => {
        await refresh();
    });

    const refresh = async () => {
        try {
            const balances = {};
            const tokenContract = new web3.eth.Contract(JSON.parse(store.state.settings.token_abi), store.state.settings.token_address);
            
            const paymentContract = new web3.eth.Contract(JSON.parse(store.state.settings.payment_abi), store.state.settings.payment_address);
            
            const vaultContract = new web3.eth.Contract(JSON.parse(store.state.settings.vault_abi), store.state.settings.vault_address);

            if(store.state.wallet.loggedIn) {
                const participant = await vaultContract.methods.getParticipant(store.state.wallet.address).call();
                balances.token = displayCurrency.format(await tokenContract.methods.balanceOf(store.state.wallet.address).call());
                balances.payment = displayCurrency.format(await paymentContract.methods.balanceOf(store.state.wallet.address).call());
                balances.vault = displayCurrency.format(participant.balance);
            }
            
            store.commit("balances", balances);
        } catch (error) {
            //alerts.danger(error.message);
            console.log("useBalances: ");
            console.error(error);
        }
    }

    return {
        refresh,
    }
}
