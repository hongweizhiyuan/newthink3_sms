<?php
/**
 * Created by PhpStorm.
 * User: 郝飞
 * Date: 2017/4/5
 * Time: 12:08
 */
namespace Sms\Controller;
use Common\Controller\FrontbaseController;

class SmsController extends FrontbaseController{

    /* 发送短信 */
	public function sms_send(){

        //step1:配置 已经写到了config.php 配置里了
        $rand_number = randNum(6);
		$sms_signName = C('SMS_SIGNNAME');
		$sms_param = "{code:'".$rand_number."',product:'".C('SMS_PRODUCT')."'}";
		$sms_templateCode = C('SMS_TEMPLATECODE');

		//step2:将生成的验证码存入cookie中
		cookie('verificationCode',$rand_number);

		//step3:获取手机号,并把手机号验证码存入cookie中
        $sms_recNum  = I('mobile');
        cookie('verificationMobile',$sms_recNum);

		//step4:实例化Alidayu
		$sms = new \TopClient();
		$sms->appkey = C('APPKEY');
		$sms->secretKey = C('SECRETKEY');
		$req = new \AlibabaAliqinFcSmsNumSendRequest();
		$req->setSmsType("normal"); //这个不用改你短信的话就默认这个就好了
		$req->setSmsFreeSignName($sms_signName); //这个是签名
		$req->setSmsParam($sms_param); //验证码${code}，您正在注册成为${product}用户，感谢您的支持！
		$req->setRecNum($sms_recNum); //这个是写手机号码
		$req->setSmsTemplateCode($sms_templateCode); //这个是模版ID 主要也是短信内容
		$resp = $sms->execute($req);
		$_success = $resp->result->success;
		$_msg = $resp->sub_msg;

		//step6:状态及输出
		if($_success){
			$arr['status'] = '1';
			$arr['msg']='发送成功，验证码不要告诉其他人！';
		}else if($_msg){
			$arr['status'] = '0';
			$arr['msg'] = '1分钟内只能发一条，一小时7条验证码';
		}else{
			$arr['status'] = '0';
			$arr['msg'] = '发送失败，很抱歉';
		}
		
		$this->output($arr);
	}


	/* 短信验证码校验 */
	public function verification(){

		//step1:cookie中取出验证码手机号
		$verificationCode = cookie('verificationCode');
        $verificationMobile = cookie('verificationMobile');

		//step2:用户传递验证码
		$verificationCodeCorr = I('code');
        $verificationMobileCorr = I('mobile');

        //step3:验证手机号和验证码状态及输出
		if($verificationCode == $verificationCodeCorr && $verificationMobile==$verificationMobileCorr){
			$arr['status'] = '1';
			$arr['msg'] = '验证码校验成功';
		} else {
			$arr['status'] = '0';
			$arr['msg'] = '验证码校验失败';
		}
		$this->output($arr);
	}

}