<?php
/**
 * CabinetTreeBehavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('TreeBehavior', 'Model/Behavior');

/**
 * Class CabinetTreeBehavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Cabinets\Model\Behavior
 */
class CabinetTreeBehavior extends TreeBehavior {

/**
 * 多言語化があるため、TreeBehaviorとは別処理にする
 *
 * @param Model $Model Model using this behavior
 * @param int|string|null $id The ID of the record to read
 * @param string|array|null $fields Either a single string of a field name, or an array of field names
 * @param int|null $recursive The number of levels deep to fetch associated records
 * @return array Array of nodes from top most parent to current node
 * @link http://book.cakephp.org/2.0/en/core-libraries/behaviors/tree.html#TreeBehavior::getPath
 */
	public function getPath(Model $Model, $id = null, $fields = null, $recursive = null) {
		if (! isset($recursive)) {
			$recursive = 0;
		}

		//TreeBehaviorと同じ
		$options = array();
		if (is_array($id)) {
			$options = $this->_getOptions($id);
			extract(array_merge(array('id' => null), $id));
		}

		if (!empty($options)) {
			$fields = null;
			if (!empty($options['fields'])) {
				$fields = $options['fields'];
			}
			if (!empty($options['recursive'])) {
				$recursive = $options['recursive'];
			}
		}
		$overrideRecursive = $recursive;
		if (empty($id)) {
			$id = $Model->id;
		}
		extract($this->settings[$Model->alias]);
		if ($overrideRecursive !== null) {
			$recursive = $overrideRecursive;
		}

		//以下、TreeBehaviorと異なる
		$result = $Model->find('first', array(
			'recursive' => $recursive,
			'fields' => array(
				$Model->CabinetFile->alias . '.key',
				$Model->CabinetFile->alias . '.is_active',
				$Model->CabinetFile->alias . '.is_latest',
			),
			'conditions' => array($Model->escapeField() => $id),
			'order' => false,
			'callbacks' => false,
		));

		if (! $result) {
			return array();
		}

		$items = $Model->find('all', array(
			'recursive' => $recursive,
			'fields' => array($left, $right),
			'conditions' => array(
				$Model->CabinetFile->alias . '.key' => $result[$Model->CabinetFile->alias]['key'],
				$Model->CabinetFile->alias . '.is_active' => $result[$Model->CabinetFile->alias]['is_active'],
				$Model->CabinetFile->alias . '.is_latest' => $result[$Model->CabinetFile->alias]['is_latest'],
			),
			'order' => false,
			'callbacks' => false,
		));
debug($items);

		$rangeLeft = $rangeRight = null;
		foreach ($items as $item) {
			if (! isset($rangeLeft) || $rangeLeft > $item[$Model->alias]['lft']) {
				$rangeLeft = $item[$Model->alias]['lft'];
			}
			if (! isset($rangeRight) || $rangeRight < $item[$Model->alias]['rght']) {
				$rangeRight = $item[$Model->alias]['rght'];
			}
		}

		$options = array_merge(array(
			'conditions' => array(
				$scope,
				'OR' => array(
					'CabinetFile.language_id' => Current::read('Language.id'),
					'CabinetFile.is_translation' => false,
				),
				$Model->escapeField($left) . ' <=' => $rangeLeft,
				$Model->escapeField($right) . ' >=' => $rangeRight,
			),
			'fields' => $fields,
			'order' => array($Model->escapeField($left) => 'asc'),
			'recursive' => $recursive
		), $options);
		$results = $Model->find('all', $options);
		return $results;

//
//
//		$options = array();
//		if (is_array($id)) {
//			$options = $this->_getOptions($id);
//			extract(array_merge(array('id' => null), $id));
//		}
//
//		if (!empty($options)) {
//			$fields = null;
//			if (!empty($options['fields'])) {
//				$fields = $options['fields'];
//			}
//			if (!empty($options['recursive'])) {
//				$recursive = $options['recursive'];
//			}
//		}
//		$overrideRecursive = $recursive;
//		if (empty($id)) {
//			$id = $Model->id;
//		}
//		extract($this->settings[$Model->alias]);
//		if ($overrideRecursive !== null) {
//			$recursive = $overrideRecursive;
//		}
//		$result = $Model->find('first', array(
//			'conditions' => array($Model->escapeField() => $id),
//			'fields' => array($left, $right),
//			'order' => false,
//			'recursive' => $recursive
//		));
//		if ($result) {
//			$result = array_values($result);
//		} else {
//			return array();
//		}
//		$item = $result[0];
//		$options = array_merge(array(
//			'conditions' => array(
//				$scope,
//				$Model->escapeField($left) . ' <=' => $item[$left],
//				$Model->escapeField($right) . ' >=' => $item[$right],
//			),
//			'fields' => $fields,
//			'order' => array($Model->escapeField($left) => 'asc'),
//			'recursive' => $recursive
//		), $options);
//		$results = $Model->find('all', $options);
		return $results;
	}

}