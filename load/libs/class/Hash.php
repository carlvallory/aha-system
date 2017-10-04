<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Hash
{
        //Hash::create('md5', $data, HASH_PASSWORD_KEY);
    
    public static function create($algorithm, $data, $salt){
        $context = hash_init($algorithm, HASH_HMAC, $salt);
        hash_update($context, $data);
        return hash_final($context);
    }
    
    public static function encode($value, $key = authToken){
        $encType = MCRYPT_RIJNDAEL_256;
        $encMode = MCRYPT_MODE_ECB;
        $return  = mcrypt_encrypt($encType, $key, $value, $encMode, mcrypt_create_iv(mcrypt_get_iv_size($encType, $encMode), MCRYPT_RAND));
        $return  = strrev($return);
        return base64_encode($return);	
    }

    public static function decode($value, $key = authToken){
        $value	 = base64_decode($value);
        $value	 = strrev($value);
        $encType = MCRYPT_RIJNDAEL_256;
        $encMode = MCRYPT_MODE_ECB;
        $return  = mcrypt_decrypt($encType, $key, $value, $encMode, mcrypt_create_iv(mcrypt_get_iv_size($encType, $encMode), MCRYPT_RAND));
        return trim($return);	
    }
    
}

?>