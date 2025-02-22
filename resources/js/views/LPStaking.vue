<template>
    <div>
        <h1>Furpool</h1>
        <div class="mb-5">
            <p>
                Furpool offers incentives to add liquidity to the $FUR/USDC liquidity pool.<br/>
                Through the taxation model, attractive returns on USDC investments are possible.<br/>
                Stake and withdraw with USDC, the LP tokens are created and sold automatically.
            </p>
        </div>
        <div class="row flex-row-reverse gx-5">
            <div class="col-lg-7 bg-light text-dark rounded p-5 mb-4">
                <div v-show="!loading">
                    <div class="row">
                        <div class="form-group col">
                            <label for="quantity">Amount</label>
                            <input v-model="quantity" type="number" class="form-control" id="quantity"/>
                        </div>
                        <div class="form-group col">
                            <label for="duration">Duration</label>
                            <select v-model="duration" class="form-control" id="duration">
                                <option v-show="lock_day.toFixed(0) == 0" value="0">No limit</option>
                                <option v-show="lock_day.toFixed(0) < 30" value="1">30 days</option>
                                <option v-show="lock_day.toFixed(0) < 60" value="2">60 days</option>
                                <option v-show="lock_day.toFixed(0) < 90" value="3">90 days</option>
                            </select>
                        </div>
                        <div v-show="staked > 0" class="form-group col">
                            <label for="upgrade">Upgrade</label>
                            <button id="upgrade" @click="upgrade" class="btn btn-success btn-xs">Upgrade Period</button>
                        </div>
                    </div>
                    <div v-show="store.state.wallet.loggedIn">
                        <button @click="stake" class="btn btn-lg btn-info btn-block mb-2">Stake</button>
                    </div>
                    <div v-show="!store.state.wallet.loggedIn">
                        <button class="btn btn-lg btn-info btn-block" data-toggle="modal" data-target="#loginmodal">Connect Wallet</button>
                    </div>
                    <div class="row mt-3">
                        <div v-show="available > 0" class="col">
                            <button @click="claim" class="btn btn-lg btn-info btn-block">Claim</button>
                        </div>
                        <div v-show="available > 0" class="col">
                            <button @click="compound" class="btn btn-lg btn-success btn-block">Compound</button>
                        </div>
                        <div v-show="lock_day.toFixed(0) == 0 && staked > 0" class="col">
                            <button @click="unstake" class="btn btn-lg btn-secondary btn-block">Unstake</button>
                        </div>
                    </div>
                    <p v-show="store.state.wallet.loggedIn" class="card-text mt-4"><strong>Time Left to Unstake: {{lock_day.toFixed(2)}} days</strong></p>
                    <div class="row mt-4">
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <img src="../../images/lp-furusd.svg" class="mx-auto d-block mb-3" alt="Available" height="75"/>
                                    <p class="card-title">LP Price</p>
                                    <p class="card-text"><strong>${{ lp_price.toFixed(2) }}</strong></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <img src="../../images/lp-per.svg" class="mx-auto d-block mb-3" alt="Available" width="75" height="75"/>
                                    <p class="card-title">All Time APR: <strong>{{ store.state.settings.pool_alltime_apr }}%</strong></p>
                                    <p class="card-text">14 Day: <strong>{{ store.state.settings.pool_fourteenday_apr }}%</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-show="loading" class="text-center">
                    <div class="spinner-border m-5" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="../../images/referral.svg" class="mx-auto d-block mb-3" alt="Stakers" width="75" height="75"/>
                                <p class="card-title">Total Stakers</p>
                                <p class="card-text"><strong>{{ totalStakers }}</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="../../images/usdc.svg" class="mx-auto d-block mb-3" alt="Staked" width="75" height="75"/>
                                <p class="card-title">Total Staked</p>
                                <p class="card-text"><strong>{{ displayCurrency.format(totalStaked) }} USDC</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="../../images/usdc.svg" class="mx-auto d-block mb-3" alt="Stake" width="75" height="75"/>
                                <p class="card-title">Your Stake</p>
                                <p class="card-text">
                                    <strong>{{ displayCurrency.format(staked) }} USDC</strong><br/>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="../../images/usdc.svg" class="mx-auto d-block mb-3" alt="Available" width="75" height="75"/>
                                <p class="card-title">Available</p>
                                <p class="card-text"><strong>{{ displayCurrency.format(available) }} USDC</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-show="store.state.wallet.admin" class="text-right">
            <router-link :to="{ name: 'PoolApr' }" class="footer-text"><small>update</small></router-link>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useStore } from 'vuex';
import useAlerts from '../composables/useAlerts';
import useBalances from '../composables/useBalances';
import useDisplayCurrency from '../composables/useDisplayCurrency';
import Web3 from 'web3';
import useSettings from '../composables/useSettings';

export default {
    setup() {
        var web3 = new Web3(Web3.givenProvider || 'ws://some.local-or-remote.node:8546');
        const store = useStore();
        const settings = useSettings();
        const alerts = useAlerts();
        const balances = useBalances();
        const displayCurrency = useDisplayCurrency();
        const loading = ref(false);
        const quantity = ref(0);
        const duration = ref(0);
        const available = ref(0);
        const balance = ref(0);
        const totalStakers = ref(0);
        const totalStaked = ref(0);
        const staked = ref(0);
        const lp_price = ref(0);
        const lock_day = ref(0);
        const boostedAmount = ref(0);

        addEventListener("refresh", async () => {
            await update();
        });
        onMounted(async () => {
            await settings.update();
            await update();

        });


        const stakingContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.lpstaking_abi), store.state.settings.lpstaking_address);
        };
        const paymentContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.payment_abi), store.state.settings.payment_address);
        };
        const factoryContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.factory_abi), store.state.settings.factory_address);
        };
        const pairContract = () => {
            return new web3.eth.Contract(JSON.parse(store.state.settings.pair_abi), store.state.settings.pair_address);
        }

        const update = async () => {
            loading.value = true;
            try {
                const pair = pairContract();
                const contract = stakingContract();
                const furio_price = store.state.settings.furio;
                const usdc_price = store.state.settings.usdc;


                // const totalSupply = await lpReserve.methods.totalSupply().call();

                totalStakers.value = await contract.methods.totalStakerNum().call();
                totalStaked.value = await contract.methods.totalStakingAmountInUsdc().call();


                //Get LP Price
                const reserves = await pair.methods.getReserves().call();
                const totalSupply = await pair.methods.totalSupply().call();

                lp_price.value = (reserves[0]*furio_price + reserves[1]*usdc_price) /totalSupply;

                if(store.state.wallet.loggedIn) {
                    staked.value = await contract.methods.stakingAmountInUsdc(store.state.wallet.address).call();
                    boostedAmount.value = await contract.methods.boostedAmountInUsdc(store.state.wallet.address).call();
                    available.value = await contract.methods.availableRewardsInUsdc(store.state.wallet.address).call();
                    const lock_sec = await contract.methods.getRemainingLockedTime(store.state.wallet.address).call();
                    //const apr = available/stacked*100*365
                    console.log("locked second from contract: ", lock_sec);
                    lock_day.value = lock_sec/86400.0;
                    console.log("Locked days: ", lock_day);
                    const staker = contract.methods.stakers(store.state.wallet.address).call();
                    duration.value = staker.stakingPeriod;
                    if(typeof staker.stakingPeriod == "undefined") duration.value = 0;
                }

            }
            catch (error) {
                //alerts.danger(error.message);
            }
            balances.refresh();
            loading.value = false;
        };
        const stake = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const staking = stakingContract();
                const payment = paymentContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const amount = BigInt(quantity.value * "1000000000000000000");
                const allowance = await payment.methods.allowance(store.state.wallet.address, store.state.settings.lpstaking_address).call();
                if (allowance < amount) {
                    const approveGas = Math.round(await payment.methods.approve(store.state.settings.lpstaking_address, amount).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                    await payment.methods.approve(store.state.settings.lpstaking_address, amount).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: approveGas });
                }
                const gas = Math.round(await staking.methods.stake(store.state.settings.payment_address, amount, duration.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await staking.methods.stake(store.state.settings.payment_address, amount, duration.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
                dispatchEvent(new Event("refresh"));
                await update();
            }
            catch (error) {
                alerts.danger(error.message);
            }
            loading.value = false;
        };
        const claim = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const contract = stakingContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await contract.methods.claimRewards().estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await contract.methods.claimRewards().send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            }
            catch (error) {
                alerts.danger(error.message);
            }
            dispatchEvent(new Event("refresh"));
            await update();
            loading.value = false;
        };
        const compound = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const contract = stakingContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await contract.methods.compound().estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await contract.methods.compound().send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            }
            catch (error) {
                alerts.danger(error.message);
            }
            dispatchEvent(new Event("refresh"));
            await update();
            loading.value = false;
        };
        const unstake = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const contract = stakingContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await contract.methods.unstake().estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await contract.methods.unstake().send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            }
            catch (error) {
                alerts.danger(error.message);
            }
            dispatchEvent(new Event("refresh"));
            await update();
            loading.value = false;
        };
        const upgrade = async () => {
            alerts.warning("waiting on response from wallet");
            loading.value = true;
            try {
                const contract = stakingContract();
                const gasPriceMultiplier = 1;
                const gasMultiplier = 1.5;
                const gasPrice = Math.round(await web3.eth.getGasPrice() * gasPriceMultiplier);
                const gas = Math.round(await contract.methods.resetStakingPeriod(duration.value).estimateGas({ from: store.state.wallet.address, gasPrice: gasPrice }) * gasMultiplier);
                const result = await contract.methods.resetStakingPeriod(duration.value).send({ from: store.state.wallet.address, gasPrice: gasPrice, gas: gas });
                alerts.info("Transaction successful! TXID: " + result.blockHash);
            }
            catch (error) {
                alerts.danger(error.message);
            }
            dispatchEvent(new Event("refresh"));
            await update();
            loading.value = false;
        };
        return {
            store,
            loading,
            displayCurrency,
            quantity,
            duration,
            available,
            balance,
            totalStakers,
            totalStaked,
            staked,
            lp_price,
            lock_day,
            stake,
            claim,
            compound,
            unstake,
            upgrade,
            boostedAmount,
        };
    }
}
</script>
