<?php
/**
 * UpdateCabinetTotalSizeTest.php
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */


\App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * Class UpdateCabinetTotalSizeTest
 */
final class UpdateCabinetTotalSizeTest extends \NetCommonsModelTestCase {

/**
 * Fixture
 *
 * @var string[]
 */
	public $fixtures = [
		'plugin.cabinets.cabinet',
		'plugin.cabinets.cabinet_file',
		'plugin.cabinets.cabinet_file_tree',
		'plugin.cabinets.upload_file_for_cabinets',
		'plugin.cabinets.upload_files_content_for_cabinets',
		'plugin.workflow.workflow_comment',
	];

/**
 * testCalcCabinetTotalSize
 *
 * @return void
 */
	public function testCalcCabinetTotalSize() {
		$cabinetFile = \ClassRegistry::init('Cabinets.CabinetFile');
		$cabinet = [
			'Cabinet' => [
				'id' => 2,
				'block_id' => '2',
				'name' => 'Cabinet1',
				'key' => 'content_block_1',
				'total_size' => '1'
			]
		];
		/** @see \CabinetFolderBehavior::calcCabinetTotalSize */
		$totalSize = $cabinetFile->calcCabinetTotalSize($cabinet);

		$expectedTotal = 13638761;
		self::assertSame($expectedTotal, $totalSize);
	}

/**
 * testUpdateCabinetTotalSize
 *
 * @return void
 */
	public function testUpdateCabinetTotalSize() {
		$cabinetFile = \ClassRegistry::init('Cabinets.CabinetFile');
		/** @see \CabinetFolderBehavior::updateCabinetTotalSize() */
		$cabinetFile->updateCabinetTotalSize('content_block_1');

		$cabinetModel = ClassRegistry::init('Cabinets.Cabinet');
		$cabinet = $cabinetModel->find('first', [
			'Cabinet.id' => '2'
		]);
		$totalSize = $cabinet['Cabinet']['total_size'];

		// total_sizeはmysqlのfloatで保存されてるので、頭5桁だけで比較する
		$expectedTotal = '13638761';
		$expectedFloatLeft5 = $this->__truncate5($expectedTotal);

		$floatLeft5TotalSize = $this->__truncate5($totalSize);
		self::assertSame($expectedFloatLeft5, $floatLeft5TotalSize);
	}

/**
 * 上位5桁だけでのこし、端数を切り捨てる
 *
 * @param float $floatSize
 * @return float
 */
	private function __truncate5(float $floatSize) {
		$roundNumber = 5 - strlen($floatSize);
		return round($floatSize, $roundNumber, PHP_ROUND_HALF_DOWN);
	}

}