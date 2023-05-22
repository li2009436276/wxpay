<?php

return[
    'api_prefix'        => 'api',                                    //路由前缀
    'mch_id'            => env('MCH_ID'),                            //商户号ID
    'merchant_certificate_serial' => env('MET_NO'),                  //商户序列号
    'apiclient_key'     => env('API_CLIENT_KEY'),                    //支付密钥KEY文件路径
    'apiclient_cert'    => env('API_CLIENT_CERT'),                   //支付密钥CERT文件路径
    'platform_secret'   => env('PLATFORM_SECRET'),                   //支付平台密钥，工具下载得来
    'chain'             => env('CHAIN'),                             //调起微信支付聚到
    'app_id'            => env('APP_ID'),                            //支付的APPId  如小程序，公众号,移动端，H5
    'notify_url'        => env('NOTIFY_URL'),                        //支付异步回调地址
    'api_v3_key'        => env('API_V3_KEY'),                        //商户平台设置的32位秘钥

    //微信红包
    'red_envelope'      => [
        'app_id'    => env('APP_ID'),                                //公众号APPID
        'send_name' => env('RED_SEND_NAME'),                         //红包发送商家名称,
        'scene_id'  => env('RED_SCENE_ID'),                          //红包应用场景ID  PRODUCT_2
        'act_name'  => env('RED_ACT_NAME'),                          //活动名称
        'remark'    => env('RED_REMARK'),                            //活动备注
        'wishing'   => env('RED_WISHING'),                           //红包祝福语
        'total_num' => env('RED_TOTAL_NUM') ? : 1,                   //发送红包个数
        'api_v2_key' => env('API_V2_KEY'),                           //商户号设置的V2接口KEY值
        'apiclient_key'   => env('API_CLIENT_KEY'),                //支付密钥KEY文件路径
        'apiclient_cert'  => env('API_CLIENT_CERT'),               //支付密钥CERT文件路径
    ],


    'code' => [
        'success'   => [0,'成功'],
        'fail'      => [4001,'失败']
    ],
];