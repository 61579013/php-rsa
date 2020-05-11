# php-rsa

#### 项目介绍
php版rsa加密类，和java兼容，基于openssl，支持长密文

### 使用示例

```
$rsa = new Rsa();
$rsa->privateKey = '私钥字符串';
$rsa->encodeByPrivateKey('要加密的字符串');
$rsa->publicKey = '公钥字符串';
$rsa->decodeByPublicKey('要解密的字符串');
```


