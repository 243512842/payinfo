<?php
use PHPMailer\PHPMailer\PHPMailer;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// 系统公共参数配置
function pub_info(){

    $info['admin_email'] = 'dongyao@dongyao.ren';  //管理员邮箱
    $info['admin_mobile'] = '13800000000';  //管理员手机号码，用于产生订单通知
    $info['token'] = '888888';  //管理端验证token



    // 以下两个参数是一个简单的PID倍数，防止PID被别人猜到，不用修改
    $info['o_num2'] = '9983577';  //提交订单ID扩增
    $info['o_num1'] = '7983577';  //提交订单ID扩增
    return $info;
}
// 邮件发送配置
function send_mail($email,$con){
        $mail = new PHPMailer;
        $mail->isSMTP();                                      // 设置邮件使用SMTP
        $mail->Host = 'smtp.exmail.qq.com';                     // 邮件服务器地址
        $mail->SMTPAuth = true;                               // 启用SMTP身份验证
        $mail->CharSet = "UTF-8";                             // 设置邮件编码
        $mail->setLanguage('zh_cn');                          // 设置错误中文提示
        $mail->Username = 'pay@xxx.com';              // SMTP 用户名，即个人的邮箱地址
        $mail->Password = 'your password';                        // SMTP 密码，即个人的邮箱密码
        $mail->SMTPSecure = 'ssl';                            // 设置启用加密，注意：必须打开 php_openssl 模块
        $mail->Port = 465;                                          //---------加密端口------
        $mail->Priority = 3;                                  // 设置邮件优先级 1：高, 3：正常（默认）, 5：低
        $mail->From = 'pay@xxx.com';                 // 发件人邮箱地址
        $mail->FromName = '个人体验下单通知';                     // 发件人名称
        $mail->addAddress($email);     // 添加接受者
        $mail->WordWrap = 50;                                 // 设置自动换行50个字符
        $mail->isHTML(true);                                  // 设置邮件格式为HTML
        $mail->Subject = '个人体验下单通知';
        $mail->Body    = $con;
        $mail->AltBody = '这是一封系统邮件，若您未申请，请忽略！';
        return $mail->send();
}

// 腾讯短信接口[通知管理员]
// 模板内容：有一笔来自{1}的订单已经产生，订单金额{2}元，请注意核实确认结果。
function sms_msg($mobile,$source,$money){
    $random = rand(1000,999);
    $appid = 'XXXXXXX';  //短信appid
    $appkey = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';   //短信appkley
    $sms['ext'] = "";
    $sms['extend'] = '';
    $sms['params'] = [$source,$money];
    $sms['sign'] = "短信签名";             //短信签名
    $sms['tel'] = ['mobile'=>$mobile,'nationcode'=>86];
    $sms['time'] = time();
    $sms['tpl_id'] = '252997';           //短信模板
    $time = $sms['time'];
    $sms['sig'] = hash('sha256',"appkey=$appkey&random=$random&time=$time&mobile=$mobile");
    $sms = json_encode($sms);
    $url = 'https://yun.tim.qq.com/v5/tlssmssvr/sendsms?sdkappid='.$appid.'&random='.$random;
    $info = send_post($url,$sms);
    $con['code'] = $info['result'];
    $con['msg'] = $info['errmsg'];
    return $con;
}

// 腾讯短信：通知结果发送给用户
// 模板内容：尊敬的{1}：您有一笔订单已经审核成功，应付{2}元，实际支付{3}元，请登录网站核实。如非本人操作，请忽略本短信。
function sms_user($mobile,$username,$d_price,$t_price){
    $random = rand(1000,999);
    $appid = 'XXXXXXX';  //短信appid
    $appkey = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';   //短信appkley
    $sms['ext'] = "";
    $sms['extend'] = '';
    $sms['params'] = [$username,$d_price,$t_price];
    $sms['sign'] = "短信签名";             //短信签名
    $sms['tel'] = ['mobile'=>$mobile,'nationcode'=>86];
    $sms['time'] = time();
    $sms['tpl_id'] = '252993';           //短信模板
    $time = $sms['time'];
    $sms['sig'] = hash('sha256',"appkey=$appkey&random=$random&time=$time&mobile=$mobile");
    $sms = json_encode($sms);
    $url = 'https://yun.tim.qq.com/v5/tlssmssvr/sendsms?sdkappid='.$appid.'&random='.$random;
    $info = send_post($url,$sms);
    $con['code'] = $info['result'];
    $con['msg'] = $info['errmsg'];
    return $con;
}






// 发送post数据
function send_post($url, $param = array(), $header = array(), $ssl = 0, $format = 'json',$log=0)
{
    $ch = curl_init();
    if (is_array($param)) {
        $urlparam = http_build_query($param);
    } else if (is_string($param)) { //json字符串
        $urlparam = $param;
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120); //设置超时时间
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回原生的（Raw）输出
    curl_setopt($ch, CURLOPT_POST, 1); //POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $urlparam); //post数据
    if ($header) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    if ($ssl) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    }

    $data = curl_exec($ch);
    if ($format == 'json') {
        $data = json_decode($data, true);
    }
    curl_close($ch);
    return $data;
}

