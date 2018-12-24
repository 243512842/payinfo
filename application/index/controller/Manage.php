<?php
namespace app\index\controller;
use think\Controller;

class Manage extends Controller
{

    public function initialize()
    {
        $token = input('token');
        $config = pub_info();
        if($config['token'] != $token){
            $this->error('非法访问','Index/index');
        }
        $this->assign('token',$config['token']);
    }
    // 管理端待核实
    public function admin()
    {
        $order = db('info')->order('pid desc')->where('state',2)->paginate(5);
        $this->assign('order',$order);
        return $this->fetch();
    }
    // 待支付订单
    public function zhifu()
    {
        $order = db('info')->order('pid desc')->where('state',0)->paginate(5);
        $this->assign('order',$order);
        return $this->fetch();
    }

    // 已通过审核订单
    public function tongguo()
    {
        $order = db('info')->order('pid desc')->where('state',1)->paginate(5);
        $this->assign('order',$order);
        return $this->fetch();
    }

    // 被拒绝订单
    public function jujue()
    {
        $order = db('info')->order('pid desc')->where('state',3)->paginate(5);
        $this->assign('order',$order);
        return $this->fetch();
    }

    // 审核订单
    public function shenhe()
    {
        $pid = input('pid');
        $shenhe = db('info')->where('pid',$pid)->find();
        $this->assign('shenhe',$shenhe);
        return $this->fetch();
    }

    // 审核结果，并邮件短信通知
    public function order(){
        $con = input('post.');
        $pid = $con['pid'];
        $t_price = $con['t_price'];
        if($con['s_type'] == 1){
            $type = 1;
        }else{
            $type = 3;
        }
        $shenhe = db('info')->where('pid',$pid)->update(['t_price'=>$t_price,'state'=>$type]);
        if($shenhe){
            $dingdan = db('info')->where('pid',$pid)->find();
            $email = $dingdan['email'];
            $user_email = $dingdan['username'].'：您的订单已经审核，下单金额'.$dingdan['d_price']."元，实际支付：".$dingdan['t_price']."元，如有任何疑问，请加QQ群进行联系：QQ群：648120877！";
            $s_email = send_mail($email,$user_email); //给用户发邮件通知
            if($dingdan['mobile'] !=""){
                // 通过审核才发短信，毕竟短信也是花钱的
                if($type == 1){
                    sms_user($dingdan['mobile'],$dingdan['username'],$dingdan['d_price'],$dingdan['t_price']);
                }
            }
            $this->success('审核成功');
        }else{
            $this->error('系统错误');
        }
    }
    
}
