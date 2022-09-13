import axios from "axios";
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

    const getAvailable = async (max, price, value, total) => {
        try {
            const contract = getContract();
            const available = await contract.methods.available(store.state.wallet.address, max, price, value, total).call();
            return available;
        } catch (error) {
            console.error(error);
        }
    }

    const getSold = async (max, price, value, total) => {
        try {
            const contract = getContract();
            const sold = await contract.methods.sold(max, price, value, total).call();
            return sold;
        } catch (error) {
            console.error(error);
        }
    }

    const buy = async (signature, quantity, max, price, value, total, expiration) => {
        try {
            alerts.info("Waiting on response from wallet");
            const gasPriceMultiplier = 1.2;
            const gasMultipler = 1.2;
            const nft = getContract();
            const payment = getPaymentContract();
            const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
            const allowance = await payment.methods.allowance(store.state.wallet.address, store.state.settings.presale_address).call();
            const realPrice = BigInt(quantity * price * 10 ** store.state.settings.payment_decimals);
            if(allowance < realPrice) {
                const approveGas = Math.round(await payment.methods.approve(store.state.settings.presale_address, realPrice).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultipler);
                await payment.methods.approve(store.state.settings.presale_address, realPrice).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
            }
            const buyGas = Math.round(await nft.methods.buy(signature, quantity, max, price, value, total, expiration).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultipler);
            const result = await nft.methods.buy(signature, quantity, max, price, value, total, expiration).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: buyGas });
            alerts.info("Transaction successful! TXID: " + result.blockHash);
            await axios.get("/api/v1/updatepresale?quantity=" + quantity + "&max=" + max + "&price=" + price + "&value=" + value + "&total=" + total + "&success=1");
        } catch (error) {
            alerts.danger(error.message);
            console.error(error);
            await axios.get("/api/v1/updatepresale?quantity=" + quantity + "&max=" + max + "&price=" + price + "&value=" + value + "&total=" + total + "&success=0");
        }
    }

    return {
        getPaymentContract,
        getContract,
        getAvailable,
        getSold,
        buy,
    }
}
