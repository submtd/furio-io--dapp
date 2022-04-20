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
            const startData = await contract.methods.getStarts().call();
            presaleNft.presaleOneStart = startData[0];
            presaleNft.presaleTwoStart = startData[1];
            presaleNft.presaleThreeStart = startData[2];
            presaleNft.claimStart = startData[3];
            const presaleData = await contract.methods.getPresaleData().call();
            presaleNft.supply = presaleData[0];
            presaleNft.value = presaleData[1];
            presaleNft.price = presaleData[2];
            const ownerData = await contract.methods.getOwnerData(store.state.wallet.address).call();
            presaleNft.max = ownerData[0];
            presaleNft.ownedValue = ownerData[1];
            presaleNft.balance = ownerData[2];
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
