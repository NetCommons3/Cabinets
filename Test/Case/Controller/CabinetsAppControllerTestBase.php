<?php
/**
 * CabinetsController Test Case
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('CabinetsController', 'Cabinets.Controller');

App::uses('NetCommonsFrameComponent', 'NetCommons.Controller/Component');
//App::uses('NetCommonsBlockComponent', 'NetCommons.Controller/Component');
App::uses('NetCommonsRoomRoleComponent', 'NetCommons.Controller/Component');
App::uses('YAControllerTestCase', 'NetCommons.TestSuite');
App::uses('RolesControllerTest', 'Roles.Test/Case/Controller');
App::uses('AuthGeneralControllerTest', 'AuthGeneral.Test/Case/Controller');

App::uses('YAControllerTestCase', 'NetCommons.TestSuite');

/**
 * Summary for CabinetsController Test Case
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CabinetsAppControllerTestBase extends YAControllerTestCase {

/**
 * Fixture merge
 *
 * @var array
 */
	protected $_isFixtureMerged = false;

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_setting',
		'plugin.cabinets.cabinet_frame_setting',
		'plugin.cabinets.tag',
		'plugin.cabinets.tags_content',
		'plugin.net_commons.site_setting',
		//'plugin.blocks.block',
		'plugin.cabinets.block4cabinets',
		'plugin.blocks.block_role_permission',
		'plugin.boxes.box',
		'plugin.comments.comment',
		//'plugin.frames.frame',
		'plugin.cabinets.frame4cabinets',
		'plugin.boxes.boxes_page',
		'plugin.containers.container',
		'plugin.containers.containers_page',
		'plugin.m17n.language',
		'plugin.m17n.languages_page',
		'plugin.pages.page',
		'plugin.rooms.space',
		'plugin.roles.role',
		'plugin.roles.default_role_permission',
		'plugin.rooms.roles_rooms_user',
		'plugin.rooms.roles_room',
		'plugin.rooms.room',
		'plugin.rooms.room_role',
		'plugin.rooms.room_role_permission',
		'plugin.plugin_manager.plugins_room',
		'plugin.users.user',
		'plugin.cabinets.plugin',
		'plugin.categories.category',
		'plugin.categories.category_order',
		'plugin.likes.like',
		'plugin.content_comments.content_comment',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		YACakeTestCase::loadTestPlugin($this, 'NetCommons', 'TestPlugin');
		Configure::write('Config.language', 'ja');
	}

}
