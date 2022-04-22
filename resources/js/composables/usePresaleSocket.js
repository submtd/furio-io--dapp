import Web3 from "web3";
export default function usePresaleSocket() {
    const web3wss = new Web3(new Web3.providers.WebsocketProvider("wss://ws-nd-460-208-742.p2pify.com/b2c6542ae5825a80c07775b63a58499e"));
    var options = {
        address: "0x983F56616C2315f3AD69a3E55e523e701E4a2103",
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
    web3wss.eth.subscribe("logs", options, function(error, result) {
        if(!error) console.log("Got result", result);
        else console.error(error);
    }).on("data", function(log) {
        console.log("Got data", log);
    }).on("changed", function(log) {
        console.log("Changed", log);
    });
}
