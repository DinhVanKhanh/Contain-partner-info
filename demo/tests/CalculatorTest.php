<?php
$_POST['controller'] = "area";
$_SERVER['SERVER_ADDR'] = "::1";
// require_once __DIR__ . '/../src/view/V_area.class.php';
require_once __DIR__ . '/../libs/debug.class.php';
require_once __DIR__ . '/../libs/webserver_flg.class.php';
require_once __DIR__ . '/../config/database.class.php';
require_once __DIR__ . '/../src/model/M_area.class.php';

class CalculatorTest extends \PHPUnit\Framework\TestCase
{
	public function testAdd()
	{
		$model = new M_area;
		// $arr = $model->getList();
		$arr = $model->getById('12');
		// $result = $calculator->add(20, 5);
		// $this->assertEquals(15, $result);
		print_r($arr);
		die();
	}
}
