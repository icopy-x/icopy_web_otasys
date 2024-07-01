<?PHP

class AES {
    /*
    * $data: 加密明文
    * $method: 加密方法
    * $passwd: 加密密钥
    * $iv: 加密初始化向量（可选）
    */
    public static function encrypt($string, $key, $iv)
    {
        $data = openssl_encrypt($string, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $data = strtolower(bin2hex($data));
        return $data;
    }
    /*
    * $data: 解密密文
    * $method: 解密加密方法
    * $passwd: 解密密钥
    * $iv: 解密初始化向量（可选）
    */
    public static function decrypt($string, $key,$iv)
    {
        $decrypted = openssl_decrypt(hex2bin($string), 'AES-128-CBC', $key, OPENSSL_RAW_DATA,$iv);
        return $decrypted;
    }
}
//查询openssl支持的对称加密算法
//print_r(openssl_get_cipher_methods());

//$string = 'abaabababa';
//$encrypt = AES::encrypt($string,'jrgfjrgf','qwertyuiasdfghjk');
//$decrypt = AES::decrypt($encrypt, 'jrgfjrgf','qwertyuiasdfghjk');
//echo '加密后:'.$encrypt;
//echo '解密后:'.$decrypt;
?>