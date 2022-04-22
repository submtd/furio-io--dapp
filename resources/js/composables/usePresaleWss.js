import Web3 from "web3";
import { useStore } from "vuex";
import useAlerts from "./useAlerts";
export default () => {
    const alerts = useAlerts();
    const store = useStore();
    const web3wss = new Web3(store.state.settings.wssUrl);
    var options = {
        address: store.state.settings.presaleNftAddress,
        topics: [
            web3wss.utils.keccak256("TokensPurchased"),
            web3wss.utils.keccak256("TokenClaimed"),
        ],
        reconnect: {
            auto: true,
            delay: 5000,
            maxAttempts: 5,
            onTimeout: false,
        },
    }
    var subscription = web3wss.eth.subscribe("logs", options, function(error, result) {
        if(!error) console.log("Got result");
        else console.error(error);
    }).on("data", function(log) {
        alerts.info("Got data: " + log);
        console.log("Got data", log);
    }).on("changed", function(log) {
        alerts.info("Changed: " + log);
        console.log("changed", log);
    });
}
