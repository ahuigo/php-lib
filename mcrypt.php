<?php
/**
 * @author hilojack<a132811@gmail.com>
 * @description mcrypt class for php
 * @link http://www.php.net/manual/en/mcrypt.ciphers.php
 * @chipers see mcrypt_list_algorithms()
 */
class mcrypt{

    /**
     * if blocksize mod 8 = 0, it is pkcs5
     * mcrypt 默认的填充值为 null （'\0'），java或.NET 默认填充方式为 PKCS7
     */
    static function pkcs7_pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    static function pkcs7_unpad($text) {
        $pad = ord($text{strlen($text)-1});
        return substr($text, 0, -1 * $pad);
    }

    static function encrypt($input, $key, $algo = 'tripledes') {
        $size = mcrypt_get_block_size($algo, 'ecb');
        $input = self::pkcs7_pad($input, $size);
        $td = mcrypt_module_open($algo, '', 'ecb', '');
        $key = str_pad($key,mcrypt_enc_get_key_size($td));//3des:24 des:8
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }
    static function decrypt($crypt,$key, $algo = MCRYPT_TRIPLEDES) {
        $crypt = base64_decode($crypt);
        $td = mcrypt_module_open ($algo, '', 'ecb', '');
        $key = str_pad($key,mcrypt_enc_get_key_size($td));//3des:24 des:8
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);//不会影响ecb 但会影响cbc
        mcrypt_generic_init($td, $key, $iv);
        $decrypted_data = mdecrypt_generic ($td, $crypt);
        mcrypt_generic_deinit ($td);
        mcrypt_module_close ($td);
        $decrypted_data = self::pkcs7_unpad($decrypted_data);
        $decrypted_data = $decrypted_data;
        return $decrypted_data;
    }
}


