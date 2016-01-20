<?php
/**
 * Created by PhpStorm.
 * User: ryuji
 * Date: 2015/07/13
 * Time: 10:40
 */
App::uses('FrameFixture', 'Frames.Test/Fixture');

/**
 * Class Frame4CabinetFixture
 */
class Frame4cabinetsFixture extends FrameFixture {

/**
 * モデル名
 *
 * @var string name
 */
	public $name = 'Frame';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'language_id' => 2,
			'room_id' => 1,
			'box_id' => 1,
			'plugin_key' => 'test_plugin',
			'block_id' => 5,
			'key' => 'frame_1',
			'name' => 'Test frame name 1',
			'weight' => 1,
			'is_deleted' => 0,
			'created_user' => 1,
			'created' => '2014-07-25 08:10:53',
			'modified_user' => 1,
			'modified' => '2014-07-25 08:10:53'
		),
		array(
			'id' => 201,
			'language_id' => 2,
			'room_id' => 1,
			'box_id' => 1,
			'plugin_key' => 'test_plugin',
			'block_id' => null,
			'key' => 'frame_1',
			'name' => 'Test frame name 1',
			'weight' => 1,
			'is_deleted' => 0,
			'created_user' => 1,
			'created' => '2014-07-25 08:10:53',
			'modified_user' => 1,
			'modified' => '2014-07-25 08:10:53'
		),
		array(
			'id' => 202,
			'language_id' => 2,
			'room_id' => 1,
			'box_id' => 1,
			'plugin_key' => 'test_plugin',
			'block_id' => 202,
			'key' => 'frame_202',
			'name' => 'Test frame name 1',
			'weight' => 1,
			'is_deleted' => 0,
			'created_user' => 1,
			'created' => '2014-07-25 08:10:53',
			'modified_user' => 1,
			'modified' => '2014-07-25 08:10:53'
		),
	);
}
