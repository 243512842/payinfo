<?php
namespace app\index\controller;
use think\Controller;

class Pay extends Controller
{
    // 下订单
    public function order()
    {
        error_reporting(0);
        $config = pub_info();
        $con = input('post.');
        $con['d_price'] = ceil($con['d_price']*100);
        // 简单数据验证
        $validate = new \app\index\validate\User;
        if (!$validate->check($con)) {
            $esms = $validate->getError();
            $this->error($esms);
        }
        // 对提交金额进行简单验证过滤
        $con['d_price'] = $con['d_price']/100;
        if($con['d_price']==0){
            $this->error('支付金额不能小于等于0');
        }
        // 手机和邮箱必须填写其中一种
        if($con['email']=="" and $con['mobile']==""){
            $this->error('邮箱或手机号必须填写其中一个');
        }

        // 验证图形验证码
        $code = $con['imgcode'];
        if(!captcha_check($code)){
            $this->error('图形验证码输入错误');
        };
        
        // 拼接订单数据
        $info['username'] = $con['username'];
        $info['d_price'] = $con['d_price'];
        $info['type'] = $con['type'];
        $info['email'] = $con['email'];
        $info['mobile'] = $con['mobile'];
        $info['t_price'] = 0;
        $info['rand_num'] = rand(1000,9999);
        $info['state'] = '0';
        $info['time'] = time();
        $add_order = db('info')->insertGetId($info);  //获取订单ID
        if($add_order){
            // 跳转到支付页面
            $this->redirect('Index/qrcode', ['pid' => $add_order*$config['o_num1']]);
        }else{
            $this->error('系统错误');
        }
    }


    // 确认订单
    public function order2(){
        $config = pub_info();
        $pid = input('pid')/$config['o_num2'];
        if($pid==""|| $pid==null || floor($pid)!=$pid){
            $this->error('非法访问','Index/index');
        }
        $p_type = input('p_type');
        if($p_type == 1){
            $edit_order = db('info')->where('pid',$pid)->where('state',0)->update(['state'=>2]);
            if($edit_order){
                $order = db('info')->where('pid',$pid)->find();
                $admin_email = '收到一条来自'.$order['username'].'的订单，价格'.$order['d_price']."元，请尽快核实，确认地址如下：";
                $user_email = $order['username'].'：您的订单已经提交，价格'.$order['d_price']."元，系统将在30分钟内将审核结果通知给您，请您耐心等候！";
                
                
                
                
                $sms_notice = sms_msg($config['admin_mobile'],$order['type'],$order['d_price']); //短信通知管理员审核

                $con = send_mail($config['admin_email'],$admin_email); //给管理员发邮件通知
                $con = send_mail($order['email'],$user_email); //给用户发邮件通知
                $this->success('提交成功，订单审核中','Index/index');
            }else{
                $this->error('系统错误');
            }
        }else{
            $edit_order = db('info')->where('pid',$pid)->where('state',0)->update(['state'=>3]);
            if($edit_order){
                $this->redirect('Index/index');
            }else{
                $this->error('系统错误');
            }
        }
    }
    
}
