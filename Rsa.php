<?php
/*使用示例
$rsa = new Rsa();
$rsa->privateKey = '私钥字符串';
$rsa->encodeByPrivateKey('要加密的字符串');
$rsa->publicKey = '公钥字符串';
$rsa->decodeByPublicKey('要解密的字符串');
*/

class Rsa
{

    public $publicKey; // 公钥内容，不要头尾和换行符，只要内容
    public $privateKey; // 私钥内容，不要头尾和换行符，只要内容

    /**
     * 获取完整的私钥
     * @return bool|resource
     */
    public function getPrivateKey()
    {
        $pem = "-----BEGIN RSA PRIVATE KEY-----" . PHP_EOL;

        $pem .= chunk_split($this->privateKey, 64, PHP_EOL);

        $pem .= "-----END RSA PRIVATE KEY-----" . PHP_EOL;

        return openssl_pkey_get_private($pem);
    }

    /**
     * 获取完整的公钥
     * @return bool|resource
     */
    public function getPublicKey()
    {
        $pem = "-----BEGIN PUBLIC KEY-----" . PHP_EOL;

        $pem .= chunk_split($this->publicKey, 64, PHP_EOL);

        $pem .= "-----END PUBLIC KEY-----" . PHP_EOL;

        return openssl_pkey_get_public($pem);
    }

    /**
     * 私钥加密
     * @param string $data 要加密的数据
     * @return mixed
     */
    public function encodeByPrivateKey($data)
    {
        $crypto = '';
        $length = $this->_getKeyLength() / 8 - 11;

        foreach (str_split($data, $length) as $chunk) {
            openssl_private_encrypt($chunk, $encrypted, $this->getPrivateKey());
            $crypto .= $encrypted;
        }

        return $this->_base64Encode($crypto);
    }

    /**
     * 公钥解密
     * @param string $data 要解密的字符串
     * @return mixed
     */
    public function decodeByPublicKey($data)
    {
        $crypto = '';
        $length = $this->_getKeyLength() / 8;

        foreach (str_split($this->_base64Decode($data), $length) as $chunk) {
            openssl_public_decrypt($chunk, $decrypted, $this->getPublicKey());
            $crypto .= $decrypted;
        }

        return $crypto;
    }

    /**
     * 获取密钥长度
     * @return mixed
     */
    private function _getKeyLength()
    {
        return openssl_pkey_get_details($this->getPublicKey())['bits'];
    }

    /**
     * @param string $value 待加密字符串
     * @return mixed
     */
    private function _base64Encode($value) {
        $data = base64_encode($value);
        return str_replace(['+', '/', '='], ['-', '_', ''], $data);
    }

    /**
     * @param string $value 待解密字符串
     * @return bool|string
     */
    private function _base64Decode($value) {
        $data = str_replace(['-', '_'], ['+', '/'], $value);

        if ($mod4 = strlen($data) % 4) {
            $data .= substr('====', $mod4);
        }

        return base64_decode($data);
    }

}
