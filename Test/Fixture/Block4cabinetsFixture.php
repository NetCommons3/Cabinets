<?php
/**
 * Created by PhpStorm.
 * User: ryuji
 * Date: 2015/07/13
 * Time: 10:40
 */
App::uses('BlockFixture', 'Blocks.Test/Fixture');

/**
 * Class Frame4CabinetFixture
 */
class Block4cabinetsFixture extends BlockFixture {

/**
 * モデル名
 *
 * @var string name
 */
	public $name = 'Block';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 5,
			'language_id' => 2,
			'room_id' => 1,
			'plugin_key' => 'cabinets',
			'key' => 'block_5',
			'public_type' => '1',
			'created_user' => 5,
			'created' => '2014-06-18 02:06:22',
			'modified_user' => 5,
			'modified' => '2014-06-18 02:06:22'
		),
		array(
			'id' => 202,
			'language_id' => 2,
			'room_id' => 1,
			'plugin_key' => 'cabinets',
			'key' => 'block_202',
			'public_type' => '1',
			'created_user' => 5,
			'created' => '2014-06-18 02:06:22',
			'modified_user' => 5,
			'modified' => '2014-06-18 02:06:22'
		),
	);
}
