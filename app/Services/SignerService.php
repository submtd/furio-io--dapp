<?php

namespace App\Services;

use Web3\Contracts\Ethabi;
use Web3\Contracts\Types\Address;
use Web3\Contracts\Types\Boolean;
use Web3\Contracts\Types\Bytes;
use Web3\Contracts\Types\DynamicBytes;
use Web3\Contracts\Types\Integer;
use Web3\Contracts\Types\Str;
use Web3\Contracts\Types\Uinteger;
use Web3p\EthereumUtil\Util;

class SignerService
{
    /**
     * Sign.
     *
     * @param string address
     * @param string salt
     * @param int expiration
     * @return string
     */
    public static function sign(string $address, string $salt, int $expiration): string
    {
        $abi = new Ethabi([
            'address' => new Address,
            'bool' => new Boolean,
            'bytes' => new Bytes,
            'dynamicBytes' => new DynamicBytes,
            'int' => new Integer,
            'string' => new Str,
            'uint' => new Uinteger,
        ]);
        $message = '0x'.$abi->encodeParameters(['address', 'string', 'uint256'], [
            $address,
            $salt,
            $expiration,
        ]);
        $util = new Util();
        $hash = '0x'.hash('sha256', hex2bin($util->stripZero($message)));
        $signature = $util->ecsign(env('SIGNER'), $hash);
        $r = str_pad($signature->r->toString(16), 64, '0', STR_PAD_LEFT);
        $s = str_pad($signature->s->toString(16), 64, '0', STR_PAD_LEFT);
        $v = dechex($signature->recoveryParam - 8);

        return "0x$r$s$v";
    }
}
