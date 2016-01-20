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
class CabinetsFormatHelper extends AppHelper {

/**
 * @var array helpers
 */
	public $helpers = array('NetCommons.Date');

/**
 * publish_startのフォーマット
 *
 * @param string $datetime datetime
 * @return bool|string
 */
	public function publishedDatetime($datetime) {
		return $this->Date->dateFormat($datetime);
	}

}