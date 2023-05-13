<?php

namespace Www\WxPay\Services;

use Curl\StrService\StrService;
use WeChatPay\Builder;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Util\PemUtil;

class WechatPay
{

    private $chain = '';
    private $appId = '';

    public function __construct($appId = null,$chain = null)
    {
        $this->chain = $chain ? : config('wx_pay.chain');
        $this->appId = $appId  ? : config('wx_pay.app_id');
    }

    /**
     * 支付调起
     * @param $orderNo
     * @param $money 分
     * @param $des
     * @return mixed|void
     */
    public function pay($orderNo,$money,$des){

        $instance = $this->payBuildInstance();

        try {
            $resp = $instance
                ->chain($this->chain)
                ->post(['json' => [
                    'mchid'        => config('wx_pay.mch_id'),
                    'out_trade_no' => $orderNo,
                    'appid'        => $this->appId,
                    'description'  => $des,
                    'notify_url'   => config('wx_pay.notify_url'),
                    'amount'       => [
                        'total'    => $money,
                        'currency' => 'CNY'
                    ],
                ]]);

            //echo $resp->getStatusCode(), PHP_EOL;
             return json_decode($resp->getBody(),true);
            //header('location: '.$redireurl['code_url']);
        } catch (\Exception $e) {
            // 进行错误处理
            echo $e->getMessage(), PHP_EOL;
            if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
                $r = $e->getResponse();
                echo $r->getStatusCode() . ' ' . $r->getReasonPhrase(), PHP_EOL;
                echo $r->getBody(), PHP_EOL, PHP_EOL, PHP_EOL;
            }
            echo $e->getTraceAsString(), PHP_EOL;
        }

    }

    /**
     * 创建支付实例
     * @return \WeChatPay\BuilderChainable
     */
    private function payBuildInstance(){


        // 商户号
        $merchantId = config('wx_pay.mch_id');

        // 从本地文件中加载「商户API私钥」，「商户API私钥」会用来生成请求的签名
        $merchantPrivateKeyFilePath = config('wx_pay.apiclient_key');
        $merchantPrivateKeyInstance = Rsa::from($merchantPrivateKeyFilePath, Rsa::KEY_TYPE_PRIVATE);

        // 「商户API证书」的「证书序列号」
        $merchantCertificateSerial = config('wx_pay.merchant_certificate_serial');

        // 从本地文件中加载「微信支付平台证书」，用来验证微信支付应答的签名
        $platformCertificateFilePath = config('wx_pay.platform_secret');;
        $platformPublicKeyInstance = Rsa::from($platformCertificateFilePath, Rsa::KEY_TYPE_PUBLIC);

        // 从「微信支付平台证书」中获取「证书序列号」
        $platformCertificateSerial = PemUtil::parseCertificateSerialNo($platformCertificateFilePath);

        // 构造一个 APIv3 客户端实例
        $instance = Builder::factory([
            'mchid'      => $merchantId,
            'serial'     => $merchantCertificateSerial,
            'privateKey' => $merchantPrivateKeyInstance,
            'certs'      => [
                $platformCertificateSerial => $platformPublicKeyInstance,
            ],
        ]);

        return $instance;
    }
}