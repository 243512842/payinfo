<?php
namespace app\index\controller;
use think\Controller;
use PHPMailer\PHPMailer\PHPMailer;

class Test extends Controller
{
    // 测试邮件
    public function index()
    {
        $config = pub_info();
        $this->success('审核成功','Manage/admin',['token'=>$config['token']]);
    }
    
}
