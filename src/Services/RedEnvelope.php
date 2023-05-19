<?php

namespace Www\WxPay\Services;

class RedEnvelope
{
    private $arrayMap=array();

    function __construct($openid,$money,$orderNo,$totalNum = null,$wishing = null,$actName = null,$remark = null){
        //随机字符串$obj['nonce_str'];//签名 $obj['sign'];

        $this->arrayMap['mch_billno']       = $orderNo;                                                 //商户订单号
        $this->arrayMap['mch_id']           = config('wxpay.mch_id');                                   //商户号
        $this->arrayMap['wxappid']          = config('wxpay.red_envelope.app_id');                      //公众账号appid
        $this->arrayMap['send_name']        = config('wxpay.red_envelope.send_name');                   //商户名称
        $this->arrayMap['re_openid']        = $openid;                                                  //用户openid    ouNtss-9LeY49B9aQekGX7y43okE
        $this->arrayMap['total_amount']     = $money;                                                   //付款金额
        $this->arrayMap['total_num']        = $totalNum ? : config('wxpay.red_envelope.total_num');     //红包发放总人数
        $this->arrayMap['wishing']          = $wishing ? : config('wxpay.red_envelope.wishing');        //红包祝福语
        $this->arrayMap['client_ip']        = $_SERVER['REMOTE_ADDR'];                                  //获取当天Ip地址
        $this->arrayMap['act_name']         = $actName ? : config('wxpay.red_envelope.act_name');       //活动名称
        $this->arrayMap['remark']           = $remark ? : config('wxpay.red_envelope.remark');          //活动备注

        //$this->arrayMap['notify_way'] = 'MINI_PROGRAM_JSAPI';

        //备注//场景id
        $this->arrayMap['scene_id']         = config('wxpay.red_envelope.scene_id');

        //活动信息$obj['risk_info'];//资金授权商户号$obj['consume_mch_id'];

        //设置随机字符串
        $this->arrayMap['nonce_str']        = $this->get_nonce_str(32);

        //设置证书签名
        $this->sign($this->arrayMap);
    }

    function pay(){
        //将请求值转给xml格式
        $xmldata=$this->Array2XML();
        return $this->curl_post_ssl($xmldata);
    }
    //生成签名
    function sign($parmMap){
        ksort($parmMap);
        //键名升序排序
        $keys=array_keys($parmMap);
        $stringA='';
        foreach($keys as $k){
            $stringA.=$k.'='.$parmMap[$k].'&';
        }
        $stringSignTemp=$stringA.'key='.config('wxpay.red_envelope.api_v2_key');
        $this->arrayMap['sign']=strtoupper(MD5($stringSignTemp));
    }

    //创建32位长度的随机字符串
    function get_nonce_str($length){
        $charts='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $max=strlen($charts)-1;
        $str='';
        for($i=0;$i<$length;$i++){
            $str.=$charts[mt_rand(0,$max)];
        }
        return $str;
    }

    function Array2XML(){
        $xml='<xml>';
        $keys=array_keys($this->arrayMap);
        foreach($keys as $k){
            $xml.='<'.$k.'>'.$this->arrayMap[$k].'</'.$k.'>';
        }
        $xml.='</xml>';
        return $xml;
    }

    function curl_post_ssl($vars, $second=30,$aHeader=array()){

        try {

            $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
            $ch = curl_init();
            //超时时间
            curl_setopt($ch,CURLOPT_TIMEOUT,$second);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
            //这里设置代理，如果有的话
            //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
            //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
            //以下两种方式需选择一种
            //第一种方法，cert 与 key 分别属于两个.pem文件
            //默认格式为PEM，可以注释
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT,config('wxpay.red_envelope.apiclient_cert'));
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY,config('wxpay.red_envelope.apiclient_key'));
            //第二种方式，两个文件合成一个.pem文件
            //curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/rootca.pem');
            if( count($aHeader) >= 1 ){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
            }
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
            $data = curl_exec($ch);
            if($data){
                curl_close($ch);
                return $data;
            }else {
                $error = curl_errno($ch);
                curl_close($ch);
                throw new \Exception('红包发送失败'.$error);

            }
        } catch (\Exception $exception) {

            throw new \Exception($exception->getMessage());
        }


    }
}