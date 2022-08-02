<?php

return [
    'network_id' => env('NETWORK_ID', 4),
    'network_name' => env('NETWORK_NAME', 'Rinkeby Testnet'),
    'rpc_url' => env('RPC_URL', 'https://rinkeby.infura.io/v3/b8e7a65f07574f89a1424b075a31f605'),
    'block_explorer_url' => env('BLOCK_EXPLORER_URL', 'https://rinkeby.etherscan.io'),
    'addressbook_address' => env('ADDRESSBOOK_ADDRESS', '0xE26B21737a35B114Ec23c41ac949360b90A2b4Ea'),
    'autocompound_address' => env('AUTOCOMPOUND_ADDRESS', '0xd3C01a66A9deDF47B9b61BB1b30Ca9729ABD620c'),
    'claim_address' => env('CLAIM_ADDRESS', '0xBE520aad8EACd773FE96cA3deCF31A269Bdaa0C1'),
    'downline_address' => env('DOWNLINE_ADDRESS', '0xb9DB83cE7AbEe3016333Be80074951C3DCeB1831'),
    'payment_address' => env('PAYMENT_ADDRESS', '0xeF0939b3c2c64c03Dc11AAc9D7Ec7dB99d82654B'),
    'presale_address' => env('PRESALE_ADDRESS', '0x0CD72acd32067FBf20Ac80450Aac3ec79Bc66dDe'),
    'safe_address' => env('SAFE_ADDRESS', '0xbd7348a8302d73782be4B4C3E959ECbCAD26FE2D'),
    'swap_address' => env('SWAP_ADDRESS', '0x3f35cf2D0b6450294baa309Eb510e1B7E004e5fa'),
    'token_address' => env('TOKEN_ADDRESS', '0x6be4206809aB796059d30013f70c0e73326151F0'),
    'vault_address' => env('VAULT_ADDRESS', '0xeE01c3AabB75ACd37E65BCD43e600c83CC56536D'),
    'vote_address' => env('VOTE_ADDRESS', '0x5f8Fa12dbe4461c70B402aB9A733DdBc50A66019'),
    'furbpresale_address' => env('FURBPRESALE_ADDRESS'),
    'furbtoken_address' => env('FURBTOKEN_ADDRESS'),
    'furbstake_address' => env('FURBSTAKE_ADDRESS'),
    'payment_decimals' => 18,
];
