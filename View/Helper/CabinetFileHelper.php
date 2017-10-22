<?php
/**
 * Created by PhpStorm.
 * User: ryuji
 * Date: 15/03/06
 * Time: 14:57
 */
App::uses('AppHelper', 'View/Helper');

/**
 * Class CabinetsFormatHelper
 */
class CabinetFileHelper extends AppHelper {

/**
 * 解凍してよいか
 *
 * @param array $cabinetFile CabinetFile data
 * @return bool
 */
	public function isAllowUnzip($cabinetFile) {
		$CabinetFile = ClassRegistry::init('Cabinets.CabinetFile');
		return $CabinetFile->isAllowUnzip($cabinetFile);
	}
}
