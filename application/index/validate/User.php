<?php
namespace app\index\validate;
use think\Validate;

class User extends Validate
{
    protected $rule = [
        'username|用户名'  =>  'require|chsAlphaNum|max:25',
        'email|邮箱地址' =>  'require|email',
        'mobile|手机号' =>  'mobile',
        'd_price|价格' =>  'require|number',
    ];

}