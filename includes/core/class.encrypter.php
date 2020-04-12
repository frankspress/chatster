<?php

namespace Chatster\Core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Crypto
{
    const METHOD = 'AES256';

    public static function encrypt($string)
    {
        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = openssl_random_pseudo_bytes($nonceSize);

        $ciphertext = openssl_encrypt(
            $string,
            self::METHOD,
            CHATSTER_KEY,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return $nonce.$ciphertext;
    }

    public static function decrypt($string)
    {
        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = mb_substr($string, 0, $nonceSize, '8bit');
        $ciphertext = mb_substr($string, $nonceSize, null, '8bit');

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::METHOD,
            CHATSTER_KEY,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return $plaintext;
    }
}
// $customer_id = substr(md5(uniqid(rand(), true)), 0, 100);
//
//
//   $old = Crypto::decrypt(  base64url_decode(  $_COOKIE['cocktest'] ) );
//    var_dump( $old  ) ;
// var_dump($customer_id);
//  $cock = base64url_encode( Crypto::encrypt( $customer_id ) );
//  $result = Crypto::decrypt(  base64url_decode(  $cock ) );
// setrawcookie('cocktest',  $cock  , (time() + 8419200), "/");
//  var_dump($cock, $result, $customer_id);die;
