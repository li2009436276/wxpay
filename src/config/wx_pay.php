<?php

return[
    'api_prefix'        => 'api',                                    //路由前缀
    'mch_id'            => env('MCH_ID'),                            //商户号ID
    'apply_app_id'      => env('APPLY_APP_ID'),                      //小程序APP_ID
    'apply_secret'      => env('APPLY_SECRET'),                      //小程序SECRET
    'send_name'         => env('SEND_NAME'),                         //商家名称
    'code' => [
        'success'   => [0,'成功'],
        'fail'      => [4001,'失败']
    ],
];