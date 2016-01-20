<?php
/**
 * Cabinet Test Case
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('Cabinet', 'Cabinets.Model');
App::uses('CabinetsAppModelTestBase', 'Cabinets.Test/Case/Model');

/**
 * Summary for Cabinet Test Case
 *
 * @property Cabinet $Cabinet
 */
class SandBoxTest extends CabinetsAppModelTestBase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		//'plugin.cabinets.cabinet',
		//'plugin.cabinets.cabinet_file',
		//'plugin.cabinets.cabinet_setting',
		//'plugin.blocks.block',
		//'plugin.blocks.block_role_permission',
		//'plugin.rooms.room',
		//'plugin.rooms.roles_room',
		//'plugin.categories.category',
		//'plugin.categories.categoryOrder',
		//'plugin.frames.frame',
		//'plugin.boxes.box',
		//'plugin.cabinets.plugin',
		//'plugin.m17n.language',
		//'plugin.users.user',
	);

	public function testIndex() {
		$e1 = new extend1();
		$e2 = new extend2();
		$e1::$staticVal = 'foo';
		debug($e2::$staticVal);
	}

}

class base
{
	static $staticVal;
}
class extend1 extends base{

}
class extend2 extends base{

}