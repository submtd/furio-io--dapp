<template>
    <h1 v-show="!showUpdateNameForm">{{ name }}</h1>
    <div v-show="showUpdateNameForm">
        <div class="form-group">
            <label for="name">Name</label>
            <input v-model="updatedName" class="form-control" id="name"/>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <button @click="toggleNameForm" class="btn btn-lg btn-secondary btn-block mb-2">Cancel</button>
            </div>
            <div class="col-sm-3">
                <button @click="updateName" class="btn btn-lg btn-info btn-block mb-2">Update</button>
            </div>
        </div>
    </div>
    <div v-show="isSelf && !showUpdateNameForm"><button @click="toggleNameForm" class="btn btn-link"><small>update team name</small></button></div>
    <p v-show="!isSelf" class="mb-t">View team stats here.</p>
    <p v-show="isSelf" class="mb-5">Buy your downline NFT's and manage your teams/airdrops here.</p>
    <div class="row flex-row-reverse gx-5">
        <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
            <div v-show="loading" class="text-center">
                <div class="spinner-border m-5" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div v-show="!loading">
                <div v-show="isSelf" class="row mb-3">
                    <div class="col-md-6">
                        <h5>Buy Downline NFTs</h5>
                        <div class="form-group">
                            <label for="buy-quantity">Quantity</label>
                            <input v-model="buyQuantity" :max="available" min="0" type="number" class="form-control" id="buy-quantity"/>
                        </div>
                        <button @click="buy" class="btn btn-sm btn-info btn-block mb-2">Buy ({{ buyQuantity * 5 }} $FUR)</button>
                    </div>
                    <div class="col-md-6">
                        <h5>Sell Downline NFTs</h5>
                        <div class="form-group">
                            <label for="sell-quantity">Quantity</label>
                            <input v-model="sellQuantity" :max="owned" min="0" type="number" class="form-control" id="sell-quantity"/>
                        </div>
                        <button @click="sell" class="btn btn-sm btn-info btn-block mb-2">Sell ({{ sellQuantity * 4 }} $FUR)</button>
                    </div>
                </div>
                <div v-show="!isSelf">
                    <h3>Send Airdrop to Team Leader</h3>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input v-model="individualAirdropAmount" class="form-control" id="amount"/>
                        <small id="amount-help" class="form-text text-muted">Airdrops are sent from your wallet balance into the recipient's vault balance.</small>
                    </div>
                    <button @click="sendIndividualAirdrop" class="btn btn-lg btn-info btn-block mb-2">Send Airdrop</button>
                </div>
                <div class="mb-3">Referrer: <button @click="participantLink(referrer)" class="btn btn-link"><strong>{{ referrer }}</strong></button>
                    <div v-show="showUpdateReferrerForm" class="mb-5">
                        <div class="alert alert-danger">
                            Referrer can only be updated <strong>one</strong> time. Please make sure the information you enter here is correct.
                        </div>
                        <div class="form-group">
                            <label for="referrer">Referrer</label>
                            <input v-model="newReferrer" class="form-control" id="referrer"/>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <button @click="toggleUpdateReferrerForm" class="btn btn-lg btn-secondary btn-block mb-2">Cancel</button>
                            </div>
                            <div class="col-sm-3">
                                <button @click="updateReferrer" class="btn btn-lg btn-info btn-block mb-2">Update</button>
                            </div>
                        </div>
                    </div>
                    <div v-show="isSelf && canUpdateReferrer && !showUpdateReferrerForm" class="text-right"><button @click="toggleUpdateReferrerForm" class="btn btn-link"><small>Update Referrer</small></button></div>
                </div>
                <div v-show="isSelf && walletBalance > 0" class="mb-5">
                    <h3>Team Airdrop</h3>
                    <p>Team airdrops allow you to send a bonus to all qualifying team members. You can set a minimum and a maximum vault balance to determine who receives the airdrop.</p>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input v-model="airdropAmount" class="form-control" id="amount"/>
                        <small id="amount-help" class="form-text text-muted">Airdrops are sent from your wallet balance into the recipient's vault balance. This amount will be split evenly between all qualifying team members.</small>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="min">Minimum Vault Balance</label>
                                <input v-model="minBalance" class="form-control" id="min"/>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="max">Maximum Vault Balance</label>
                                <input v-model="maxBalance" class="form-control" id="max"/>
                            </div>
                        </div>
                    </div>
                    <button @click="sendAirdrop" class="btn btn-lg btn-info btn-block mb-2">Send Airdrop</button>
                </div>
                <h4>Referrals</h4>
                <ul class="nav flex-column">
                    <li v-for="referred in referrals" class="nav-item">
                        <button @click="participantLink(referred)" class="btn btn-link">{{ referred }}</button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="../../images/nft.svg" class="mx-auto d-block mb-3" alt="NFT" width="75" height="75"/>
                            <p class="card-title">NFTs Owned</p>
                            <p class="card-text"><strong>{{ owned }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="../../images/team-wallet.svg" class="mx-auto d-block mb-3" alt="Team Wallet" width="75" height="75"/>
                            <p class="card-title">Team Wallet</p>
                            <p class="card-text"><strong>{{ teamWallet }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="../../images/referral.svg" class="mx-auto d-block mb-3" alt="Referrals" width="75" height="75"/>
                            <p class="card-title">Direct Referrals</p>
                            <p class="card-text"><strong>{{ directReferrals }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="../../images/fur.svg" class="mx-auto d-block mb-3" alt="FUR" width="75" height="75"/>
                            <p class="card-title">Rewarded</p>
                            <p class="card-text"><strong>{{ rewarded }} $FUR</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th scope="row">Participant Status</th>
                                        <td>{{ participantStatusDisplay }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Reward Rate</th>
                                        <td>{{ rewardRate }}%</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Vault Balance</th>
                                        <td>{{ displayCurrency.format(getProperty("balance")) }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Deposited</th>
                                        <td>{{ displayCurrency.format(getProperty("deposited")) }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Compounded</th>
                                        <td>{{ displayCurrency.format(getProperty("compounded")) }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Claimed</th>
                                        <td>{{ displayCurrency.format(getProperty("claimed")) }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Referral Rewards</th>
                                        <td>{{ displayCurrency.format(getProperty("awarded")) }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Taxes Paid</th>
                                        <td>{{ displayCurrency.format(getProperty("taxed")) }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Airdrops Sent</th>
                                        <td>{{ displayCurrency.format(getProperty("airdropSent")) }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Airdrops Received</th>
                                        <td>{{ displayCurrency.format(getProperty("airdropReceived")) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-light text-dark rounded p-5 mb-4">
        <div class="row">
            <h5>Lookup Team</h5>
            <div class="col-md-10">
                <div class="form-group">
                    <label for="lookup" class="sr-only">Team Address</label>
                    <input v-model="lookupTeam" class="form-control" id="lookup" placeholder="Lookup Team">
                </div>
            </div>
            <div class="col-md-2">
                <button @click="participantLink(lookupTeam)" class="btn btn-sm btn-info">Lookup Team</button>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, watch } from "vue";
import router from "../router";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import useBalances from "../composables/useBalances";
import useAlerts from "../composables/useAlerts";
import useDisplayCurrency from '../composables/useDisplayCurrency';
import useWallet from "../composables/useWallet";

export default {
    setup () {
        const store = useStore();
        const alerts = useAlerts();
        const route = useRoute();
        const balances = useBalances();
        const displayCurrency = useDisplayCurrency();
        const wallet = useWallet();
        const address = ref(null);
        const loading = ref(false);
        const maxSupply = ref(0);
        const totalSupply = ref(0);
        const owned = ref(0);
        const buyQuantity = ref(0);
        const sellQuantity = ref(0);
        const participant = ref(null);
        const referrals = ref([]);
        const airdropAmount = ref(0);
        const minBalance = ref(0);
        const maxBalance = ref(100000);
        const walletBalance = ref(0);
        const teamaddress = ref(null);
        const shortteamaddress = ref(null);
        const showUpdateNameForm = ref(false);
        const updatedName = ref(null);
        const rewardRate = ref(0);
        const participantStatus = ref(1);
        const lookupTeam = ref(null);
        const individualAirdropAmount = ref(0);
        const showUpdateReferrerForm = ref(false);
        const newReferrer = ref(null);

        const name = computed(() => {
            if(!address.value) {
                return null;
            }
            return address.value.attributes.name ?? address.value.attributes.shortAddress;
        });

        const available = computed(() => {
            const remaining = maxSupply.value - totalSupply.value;
            const ableToBuy = 15 - owned.value;
            if(ableToBuy > remaining) {
                return remaining;
            }
            return ableToBuy;
        });

        const directReferrals = computed(() => {
            if(!participant.value) {
                return 0;
            }
            return participant.value.directReferrals;
        });

        const rewarded = computed(() => {
            if(!participant.value) {
                return 0;
            }
            return displayCurrency.format(participant.value.awarded);
        });

        const teamWallet = computed(() => {
            let tw = "No";
            if(!participant.value) {
                return tw;
            }
            if(participant.value.teamWallet) {
                tw = "Yes";
            }
            return tw;
        });

        const referrer = computed(() => {
            if(!participant.value) {
                return null;
            }
            return participant.value.referrer;
        });

        const isSelf = computed(() => {
            if(!address.value) {
                return false;
            }
            if(typeof store.state.wallet == "undefined") {
                return false;
            }
            return address.value.attributes.address == store.state.wallet.address;
        });

        const participantStatusDisplay = computed(() => {
            switch(participantStatus.value) {
                case "1":
                    return 'Negative';
                case "2":
                    return 'Neutral';
                case "3":
                    return 'Positive';
            }
        });

        const canUpdateReferrer = computed(() => {
            if(!participant.value) {
                return false;
            }
            if(participant.value.referrer == store.state.settings.safe_address && participant.value.directReferrals == 0) {
                return true;
            }
            return false;
        });

        addEventListener("refresh", async () => {
            if(!address.value) {
                //await update();
            }
        });

        const addr = computed(async () => {
            return route.params.teamaddress ?? store.state.wallet.address;
        });

        watch(addr, async () => {
            await update();
        });

        onMounted(async () => {
            await update();
        });

        const participantLink = (address) => {
            router.push("/team/" + address);
        }

        const downlineContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.downline_abi), store.state.settings.downline_address);
        }

        const tokenContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.token_abi), store.state.settings.token_address);
        }

        const vaultContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.vault_abi), store.state.settings.vault_address);
        }

        const update = async () => {
            loading.value = true;
            try {
                address.value = await wallet.lookupAddress(route.params.teamaddress ?? store.state.wallet.address);
                const contract = downlineContract();
                const vault = vaultContract();
                const token = tokenContract();
                totalSupply.value = await contract.methods.totalSupply().call();
                maxSupply.value = await contract.methods.maxSupply().call();
                owned.value = await contract.methods.balanceOf(address.value.attributes.address).call();
                participant.value = await vault.methods.getParticipant(address.value.attributes.address).call();
                referrals.value = await vault.methods.getReferrals(address.value.attributes.address).call();
                walletBalance.value = await token.methods.balanceOf(store.state.wallet.address).call();
                buyQuantity.value = 15 - owned.value;
                sellQuantity.value = owned.value;
                rewardRate.value = await vault.methods.rewardRate(address.value.attributes.address).call() / 100;
                participantStatus.value = await vault.methods.participantStatus(address.value.attributes.address).call();
            } catch (error) {
                alerts.danger(error.message);
            }
            balances.refresh();
            loading.value = false;
        }

        const toggleNameForm = () => {
            showUpdateNameForm.value = !showUpdateNameForm.value;
        }

        const updateName = async () => {
            const signature = await web3.eth.personal.sign(address.value.attributes.nonce, address.value.attributes.address, "");
            await axios.post("/api/v1/name", {
                address: address.value.attributes.address,
                nonce: address.value.attributes.nonce,
                signature: signature,
                name: updatedName.value,
            }).then(response => {
                alerts.info("Team name updated");
            }).catch(error => {
                if(error.response.status == 422) {
                    alerts.danger(error.response.data.message);
                    return;
                }
                alerts.danger(error.message);
            });
            toggleNameForm();
            await update();
        }

        const buy = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const downline = downlineContract();
                const token = tokenContract();
                const gasPriceMultiplier = 1.2;
                const gasMultiplier = 1.2;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const amount = BigInt(buyQuantity.value * 5 * 1000000000000000000);
                const allowance = await token.methods.allowance(store.state.wallet.address, store.state.settings.downline_address).call();
                if(allowance < amount) {
                    const approveGas = Math.round(await token.methods.approve(store.state.settings.downline_address, amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                    await token.methods.approve(store.state.settings.downline_address, amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                }
                const gas = Math.round(await downline.methods.buy(buyQuantity.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await downline.methods.buy(buyQuantity.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await update();
            loading.value = false;
        }

        const sell = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const downline = downlineContract();
                const gasPriceMultiplier = 1.2;
                const gasMultiplier = 1.2;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await downline.methods.sell(sellQuantity.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await downline.methods.sell(sellQuantity.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await update();
            loading.value = false;
        }

        const sendIndividualAirdrop = async () => {
            const sendAmount = BigInt(individualAirdropAmount.value * 1000000000000000000);
            if(sendAmount > walletBalance.value) {
                alerts.danger("Insufficient funds");
                return;
            }
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const token = tokenContract();
                const vault = vaultContract();
                const gasPriceMultiplier = 1.2;
                const gasMultiplier = 1.2;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const allowance = await token.methods.allowance(store.state.wallet.address, store.state.settings.vault_address).call();
                if(allowance < sendAmount) {
                    const approveGas = Math.round(await token.methods.approve(store.state.settings.vault_address, sendAmount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                    await token.methods.approve(store.state.settings.vault_address, sendAmount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                }
                const gas = Math.round(await vault.methods.airdrop(address.value.attributes.address, sendAmount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await vault.methods.airdrop(address.value.attributes.address, sendAmount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await update();
            loading.value = false;
        }

        const sendAirdrop = async () => {
            const sendAmount = BigInt(airdropAmount.value * 1000000000000000000);
            const min = BigInt(minBalance.value * 1000000000000000000);
            const max = BigInt(maxBalance.value * 1000000000000000000);
            if(sendAmount > walletBalance.value) {
                alerts.danger("Insufficient funds");
                return;
            }
            if(sendAmount < 0) {
                alerts.danger("Invalid amount");
                return;
            }
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const vault = vaultContract();
                const token = tokenContract();
                const gasPriceMultiplier = 1.2;
                const gasMultiplier = 1.2;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const allowance = await token.methods.allowance(store.state.wallet.address, store.state.settings.vault_address).call();
                if(allowance < sendAmount) {
                    const approveGas = Math.round(await token.methods.approve(store.state.settings.vault_address, sendAmount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                    await token.methods.approve(store.state.settings.vault_address, sendAmount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                }
                const gas = Math.round(await vault.methods.airdropTeam(sendAmount, min, max).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await vault.methods.airdropTeam(sendAmount, min, max).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await update();
            loading.value = false;
        }

        const getProperty = (property) => {
            if(!participant.value) {
                return null;
            }
            if(!participant.value[property]) {
                return null;
            }
            return participant.value[property];
        }

        const toggleUpdateReferrerForm = () => {
            showUpdateReferrerForm.value = !showUpdateReferrerForm.value;
        }

        const updateReferrer = async () => {
            try {
                const vault = vaultContract();
                const gasPriceMultiplier = 1.2;
                const gasMultiplier = 1.2;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await vault.methods.updateReferrer(newReferrer.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await vault.methods.updateReferrer(newReferrer.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            } catch (error) {
                alerts.danger(error.message);
            }
            await update();
            loading.value = false;
        }

        return {
            store,
            name,
            address,
            loading,
            totalSupply,
            maxSupply,
            owned,
            buyQuantity,
            sellQuantity,
            teamWallet,
            available,
            buy,
            sell,
            referrer,
            referrals,
            directReferrals,
            rewarded,
            participantLink,
            airdropAmount,
            minBalance,
            maxBalance,
            walletBalance,
            sendAirdrop,
            teamaddress,
            shortteamaddress,
            isSelf,
            toggleNameForm,
            showUpdateNameForm,
            updateName,
            updatedName,
            participantStatusDisplay,
            rewardRate,
            displayCurrency,
            getProperty,
            addr,
            lookupTeam,
            individualAirdropAmount,
            sendIndividualAirdrop,
            canUpdateReferrer,
            toggleUpdateReferrerForm,
            showUpdateReferrerForm,
            updateReferrer,
            newReferrer,
        }
    }

}
</script>
