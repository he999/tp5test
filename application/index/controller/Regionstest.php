<?php
namespace app\index\controller;

use app\common\model\Regions;


class RegionsTest
{
    public function testa()
    {
        $level=1;

        $result = Regions::getLevel($level);
        dump($result);
    }

	public function test()
    {
        $id=43;
		$result = Regions::getName($id);
		var_dump($result);
    }

    public function test1()
    {
        $id=4;
		$result = Regions::getChildren($id);
		var_dump($result);
    }
}