<?php
/**
 * CabinetFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CabinetFixture', 'Cabinets.Test/Fixture');

/**
 * CabinetFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Cabinets\Test\Fixture
 * @codeCoverageIgnore
 */
class Cabinet4paginatorFixture extends CabinetFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Cabinet';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		//Cabinet 1
		array(
			'id' => '1',
			'block_id' => '1',
			'key' => 'Cabinet_1',
			'name' => 'Cabinet name 1',
			//'language_id' => '1',
		),
		array(
			'id' => '2',
			'block_id' => '2',
			'key' => 'Cabinet_1',
			'name' => 'Cabinet name 1',
			//'language_id' => '2',
		),
		//Cabinet 2
		array(
			'id' => '3',
			'block_id' => '4',
			'key' => 'Cabinet_2',
			'name' => 'Cabinet name 2',
			//'language_id' => '2',
		),
		//Cabinet 3
		array(
			'id' => '4',
			'block_id' => '6',
			'key' => 'Cabinet_3',
			'name' => 'Cabinet name 2',
			//'language_id' => '2',
		),

		//101-200まで、ページ遷移のためのテスト
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		for ($i = 101; $i <= 200; $i++) {
			$this->records[$i] = array(
				'id' => $i,
				'block_id' => $i,
				'key' => 'Cabinet_' . $i,
				'name' => 'Cabinet_name_' . $i,
				//'language_id' => '2',
			);
		}
		parent::init();
	}

}
