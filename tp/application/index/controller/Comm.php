<?php
namespace app\index\controller;

use app\common\model\base\CommonModel;

class Comm
{    //__construct _initialize
	public function __construct()
	{
		CommonModel::setTable('users');
	}
	
	public function test()
    {
    	$data=[
    		"username" => 'qwe123',
    		"password" => md5('123qwe'),
    		'create_time' => time(),
    	];
    	$result = CommonModel::add($data);
        dump($result);
    }

    public function test1()
    {
    	$data=[
    		"username" => 'qwe123',
    		"password" => md5('123qwe'),
    		'create_time' => time(),
    	];
    	$id['uid'] = 3;
    	$result = CommonModel::edit($id,$data);
        dump($result);
    }

    public function test2()
    {
    	$data['uid'] = 2;
    	$result = CommonModel::del($data);
        dump($result);
    }

    public function test3()
    {
    	$result = CommonModel::getOneRow();
        dump($result);
    }

    public function test4()
    {
        $result = CommonModel::getRows();
        dump($result);
    }

}
