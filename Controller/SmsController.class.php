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
	/*发送短信*/
	public function sms_send(){
		require_once C(EXTEND_PATH).'Alidayu/TopClient.php';
		require_once C(EXTEND_PATH).'Alidayu/ResultSet.php';
		require_once C(EXTEND_PATH).'Alidayu/RequestCheckUtil.php';
		require_once C(EXTEND_PATH).'Alidayu/TopLogger.php';
		require_once C(EXTEND_PATH).'Alidayu/AlibabaAliqinFcSmsNumSendRequest.php';
//		dump (class_exists(TopClient)); //检测是否有此类存在

		$rand_number = randNum(6);
		$sms_signName = "身份验证";
		$sms_param = "{code:'".$rand_number."',product:'力拓'}";
		$sms_recNum  = I('mobile');
		$sms_templateCode = "SMS_60025694";
		//将生成的验证码存入缓存中
		cookie('verificationCode',$rand_number);
		/*实例化Alidayu*/
		$sms = new \TopClient();
		$sms->appkey = "23736309";
		$sms->secretKey = "aef7d68ab59d5278a35d586bd9546a35";
		$req = new \AlibabaAliqinFcSmsNumSendRequest();
		$req->setSmsType("normal"); //这个不用改你短信的话就默认这个就好了
		$req->setSmsFreeSignName($sms_signName); //这个是签名
		$req->setSmsParam($sms_param); //验证码${code}，您正在注册成为${product}用户，感谢您的支持！
		$req->setRecNum($sms_recNum); //这个是写手机号码
		$req->setSmsTemplateCode($sms_templateCode); //这个是模版ID 主要也是短信内容
		$resp = $sms->execute($req);
		$_success = $resp->result->success;
		$_msg = $resp->sub_msg;
		if($_success){
			$arr['status'] = '1';
			$arr['msg']='发送成功';
		}else if($_msg){
			$arr['status'] = '0';
			$arr['msg'] = '1分钟内只能发一条，一小时7条验证码';
		}else{
			$arr['status'] = '0';
			$arr['msg'] = '发送失败，很抱歉';
		}
		
		$this->output($arr);
	}
	/*短信验证码校验*/
	public function verification(){
		//session中取出验证码
		$verificationCode = cookie('verificationCode');
		//用户传递验证码
		$verificationCodeCorr = I('code');
		if($verificationCode == $verificationCodeCorr){
			$arr['status'] = '1';
			$arr['msg'] = '验证码校验成功';
			$arr['code'] = $verificationCode;
		} else {
			$arr['status'] = '0';
			$arr['msg'] = '验证码校验失败';
		}
		$this->output($arr);
	}
}