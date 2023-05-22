<?php

namespace Www\WxPay\Services;
use Curl\StrService\StrService;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Crypto\AesGcm;
use WeChatPay\Formatter;
class WechatPayAesGcm
{

    /**
     * 微信签名
     * @param $data
     * @return array
     */
    public static function sign($prepayId,$appId = null){

        $data = [
            'app_id'=>$appId ? : config('wx_pay.app_id'),
            'timeStamp' => time(),
            'nonceStr' => strtoupper(StrService::randStr(32)),
            'package' => 'prepay_id='.$prepayId['prepay_id'],

        ];

        $merchantPrivateKeyInstance = Rsa::from(config('wx_pay.apiclient_key'));

        $data += ['paySign' => Rsa::sign(
            Formatter::joinedByLineFeed(...array_values($data)),
            $merchantPrivateKeyInstance
        ), 'signType' => 'RSA'];

        return $data;
    }

    /**
     * 验签 数据解密 ciphertext
     * @param $signature
     * @param $nonce
     * @param $serial
     * @return array|false
     */
    public static function verifySign($signature,$nonce,$timestamp,$serial){

        $inWechatpaySignature = $signature;// 请根据实际情况获取
        $inWechatpayTimestamp = $timestamp;// 请根据实际情况获取
        $inWechatpaySerial = $serial;// 请根据实际情况获取
        $inWechatpayNonce = $nonce;// 请根据实际情况获取
        $inBody = file_get_contents('php://input');// 请根据实际情况获取，例如: file_get_contents('php://input');

        $apiv3Key = config('wx_pay.api_v3_key');// 在商户平台上设置的APIv3密钥

        // 根据通知的平台证书序列号，查询本地平台证书文件，
        // 假定为 `/path/to/wechatpay/inWechatpaySerial.pem`
        $platformPublicKeyInstance = Rsa::from(config('wx_pay.platform_secret'), Rsa::KEY_TYPE_PUBLIC);

        // 检查通知时间偏移量，允许5分钟之内的偏移
        $timeOffsetStatus = 300 >= abs(Formatter::timestamp() - (int)$inWechatpayTimestamp);
        // 构造验签名串
        $verifiedStatus = Rsa::verify(

            Formatter::joinedByLineFeed($inWechatpayTimestamp, $inWechatpayNonce, $inBody),
            $inWechatpaySignature,
            $platformPublicKeyInstance
        );
        if ($timeOffsetStatus && $verifiedStatus) {
            // 转换通知的JSON文本消息为PHP Array数组
            $inBodyArray = (array)json_decode($inBody, true);
            // 使用PHP7的数据解构语法，从Array中解构并赋值变量
            ['resource' => [
                'ciphertext'      => $ciphertext,
                'nonce'           => $nonce,
                'associated_data' => $aad
            ]] = $inBodyArray;
            // 加密文本消息解密
            $inBodyResource = AesGcm::decrypt($ciphertext, $apiv3Key, $nonce, $aad);
            // 把解密后的文本转换为PHP Array数组
            $inBodyResourceArray = (array)json_decode($inBodyResource, true);
            return $inBodyResourceArray;
        }

        return false;
    }
}