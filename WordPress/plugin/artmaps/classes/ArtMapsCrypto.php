<?php
if(!class_exists("ArtMapsCryptoException")){
class ArtMapsCryptoException
extends Exception {
    public function __construct($message = "", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}}

if(!class_exists("ArtMapsCrypto")) {
class ArtMapsCrypto {

    public function signData($data, $key, ArtMapsUser $user) {
        if(!is_array($data))
            throw new ArtMapsCryptoException("Data to sign must be an array");
        $data["username"] = $user->getLogin();
        $data["userLevel"] = implode(",", $user->getRoles());
        $data["timestamp"] = intval(time() * 1000);
        ksort($data);
        $k = openssl_pkey_get_private($key);
        if($k === false)
            throw new ArtMapsCryptoException(openssl_error_string());
        if(openssl_sign(implode($data), $signature, $k, "SHA256") === false)
            throw new ArtMapsCryptoException(openssl_error_string());
        openssl_free_key($k);
        $data["signature"] = base64_encode($signature);
        if($data["signature"] === false)
            throw new ArtMapsCryptoException(
                    "There was an error base64 encoding the signature");
        return $data;
    }
}}
?>