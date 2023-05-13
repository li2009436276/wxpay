<?php


namespace Www\WxPay\Resources;


use Illuminate\Http\Resources\Json\Resource;

class BaseResource extends Resource
{

    public function toArray($request)
    {
        return $this->resource;
    }

    public function with($request){
        if (empty($this->resource) || !is_string($this->resource)) {

            $this->resource = 'success';
        }

        list($code,$msg) = config("wx_pay.code")[$this->resource];
        return [
            'errcode' => $code,
            'errmsg' => $msg
        ];

    }
}