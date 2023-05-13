<?php

return[
    'api_prefix'        => 'api',                                    //路由前缀
    'mch_id'            => env('MCH_ID'),                            //商户号ID
    'merchant_certificate_serial' => env('MET_NO'),                  //商户序列号
    'apiclient_key'     => env('API_CLIENT_KEY'),                    //支付密钥KEY
    'platform_secret'   => env('PLATFORM_SECRET'),                   //支付平台密钥，工具下载得来
    'chain'             => env('CHAIN'),                             //调起微信支付聚到
    'app_id'            => env('APP_ID'),                            //支付的APPId  如小程序，公众号,移动端，H5
    'notify_url'        => env('NOTIFY_URL'),                        //支付异步回调地址
    'code' => [
        'success'   => [0,'成功'],
        'fail'      => [4001,'失败']
    ],
];