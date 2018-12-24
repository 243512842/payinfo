<?php
namespace app\index\controller;
use think\Controller;

class Index extends Controller
{
    // 测试首页
    public function index()
    {
        return $this->fetch();
    }


    public function order(){

        $order = db('info')->order('pid desc')->paginate(5);
        $this->assign('order',$order);
        return $this->fetch();
    }
    // 支付页
    public function qrcode(){
        $config = pub_info();
        $pid = input('pid')/$config['o_num1'];
        if($pid==""|| $pid==null || floor($pid)!=$pid){
            $this->error('非法访问','Index/index');
        }
        $order_info = db('info')->where('pid',$pid)->where('state',0)->find();
        if(!$order_info){
            $this->error('该订单不存在或已经处理，请勿重复操作','Index/index');
        }
        switch($order_info['type']){
            case '1': $order_info['qrcode']='zfb.png';$order_info['pay_name']="支付宝";break;
            case '2': $order_info['qrcode']='wx.jpg';$order_info['pay_name']="微信支付";break;
            case '3': $order_info['qrcode']='qq.png';$order_info['pay_name']="QQ支付";break;
        }
        $this->assign('config',$config);
        $this->assign('order_info',$order_info);
        return $this->fetch();
    }
}
