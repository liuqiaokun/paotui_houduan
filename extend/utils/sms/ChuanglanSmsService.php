<?php

//聚合数据短信发送

namespace utils\sms;
use think\facade\Cache;

class ChuanglanSmsService
{

    protected static $sendUrl = 'http://smssh1.253.com/msg/send/json';  //短信接口的URL


    /**
     * 发送短信
     * @param  array $data [发送参数]
     * @return  Bool
     */
    public static function sendSms($data,$config){
        $smsConf = array(
            'account'   	=> $config['cl_account'],
            'password'   	=> $config['cl_pwd'],
            'phone'    => $data['mobile'], //接受短信的用户手机号码
            'msg'    => urlencode($config['cl_sign']),
            'report'    => true
        );
//        print_r($smsConf);die;
        $content = self::curlPosts(self::$sendUrl,$smsConf); //请求发送短信
        if($content){
            $result = json_decode($content,true);
            if(isset($result['code'])  && $result['code']=='0'){
                return true;
            }else{
                throw new \Exception($result['errorMsg']);
            }
        }else{
            throw new \Exception('发送失败');
        }
    }

    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    function curlPosts($url,$postFields){
        $postFields = json_encode($postFields);

        $ch = curl_init ();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8'   //json版本需要填写  Content-Type: application/json;
            )
        );
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,60);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec ( $ch );
        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close ( $ch );
        return $result;
    }

}
