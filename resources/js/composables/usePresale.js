import { ref } from "vue";
import { useStore } from "vuex";
import useAlerts from "./useAlerts";
export default () => {
    const alerts = useAlerts();
    const store = useStore();
    const locked = ref(false);

    const getPaymentContract = () => {
        return new web3.eth.Contract(JSON.parse(store.state.settings.usdcAbi), store.state.settings.usdcAddress);
    }

    const getContract = () => {
        return new web3.eth.Contract(JSON.parse(store.state.settings.presaleNftAbi), store.state.settings.presaleNftAddress);
    }

    const getContractData = async () => {
        locked.value = true;
        try {
            const contract = getContract();
            const presaleNft = {};
            presaleNft.updated = true;
            presaleNft.supply = await contract.methods.supply().call();
            presaleNft.value = await contract.methods.value().call();
            presaleNft.price = await contract.methods.price().call();
            presaleNft.max = await contract.methods.max(store.state.wallet.address).call();
            presaleNft.ownedValue = await contract.methods.ownedValue(store.state.wallet.address).call();
            presaleNft.balance = await contract.methods.balanceOf(store.state.wallet.address).call();
            presaleNft.presaleOneStart = await contract.methods.presaleOneStart().call();
            presaleNft.presaleTwoStart = await contract.methods.presaleTwoStart().call();
            presaleNft.presaleThreeStart = await contract.methods.presaleThreeStart().call();
            presaleNft.claimStart = await contract.methods.claimStart().call();
            presaleNft.nextRound = presaleNft.claimStart > Date.now() / 1000 ? 'Claim' : null;
            presaleNft.nextRound = presaleNft.presaleThreeStart > Date.now() / 1000 ? 'Presale Three' : presaleNft.nextRound;
            presaleNft.nextRound = presaleNft.presaleTwoStart > Date.now() / 1000 ? 'Presale Two' : presaleNft.nextRound;
            presaleNft.nextRound = presaleNft.presaleOneStart > Date.now() / 1000 ? 'Presale One' : presaleNft.nextRound;
            store.commit("presaleNft", presaleNft);
        } catch (error) {
            alerts.danger(error.message);
        }
        locked.value = false;
    }

    return {
        locked,
        getPaymentContract,
        getContract,
        getContractData,
    }
}
