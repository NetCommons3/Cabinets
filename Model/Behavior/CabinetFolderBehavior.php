<?php
/**
 * CabinetFolderBehavior
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Class CabinetFolderBehavior
 */
class CabinetFolderBehavior extends ModelBehavior {

/**
 * 親フォルダデータを返す
 *
 * @param Model $model CabinetFile
 * @param array $cabinetFile cabinetFile data
 * @return array cabinetFile data
 */
	public function getParent(Model $model, $cabinetFile) {
		$conditions = [
			'CabinetFileTree.id' => $cabinetFile['CabinetFileTree']['parent_id'],
		];

		$parentCabinetFolder = $model->find('first', ['conditions' => $conditions]);
		return $parentCabinetFolder;
	}

/**
 * 子ノードがあるか
 *
 * @param Model $model CabinetFile
 * @param array $cabinetFile cabinetFile(folder)data
 * @return bool true:あり
 */
	public function hasChildren(Model $model, $cabinetFile) {
		// 自分自身が親IDとして登録されてるデータがあれば子ノードあり
		$conditions = [
			'CabinetFileTree.parent_id' => $cabinetFile['CabinetFileTree']['id'],
		];
		$conditions = $model->getWorkflowConditions($conditions);
		$count = $model->find('count', ['conditions' => $conditions]);
		return ($count > 0);
	}

/**
 * ルートフォルダを得る
 *
 * @param Model $model CabinetFile
 * @param array $cabinet Cabinetデータ
 * @return array|null
 */
	public function getRootFolder(Model $model, $cabinet) {
		return $model->find('first', ['conditions' => $this->_getRootFolderConditions($cabinet)]);
	}

/**
 * キャビネットのルートフォルダとキャビネットの同期
 * ルートフォルダがなければ作成する
 *
 * @param Model $model CabinetFile
 * @param array $cabinet Cabinet model data
 * @return bool
 * @throws Exception
 */
	public function syncRootFolder(Model $model, $cabinet) {
		if ($this->rootFolderExist($model, $cabinet)) {
			// ファイル名同期
			$options = [
				'conditions' => $this->_getRootFolderConditions($cabinet)
			];
			$rootFolder = $model->find('first', $options);
			$rootFolder['CabinetFile']['filename'] = $cabinet['Cabinet']['name'];
			return ($model->save($rootFolder)) ? true : false;
		} else {
			return $model->makeRootFolder($cabinet);
		}
	}

/**
 * Cabinetのルートフォルダを作成する
 *
 * @param Model $model CabinetFile
 * @param array $cabinet Cabinetモデルデータ
 * @return bool
 */
	public function makeRootFolder(Model $model, $cabinet) {
		if ($this->rootFolderExist($model, $cabinet)) {
			return true;
		}
		//
		$model->create();
		$rootFolder = [
			'CabinetFile' => [
				'cabinet_id' => $cabinet['Cabinet']['id'],
				'status' => WorkflowComponent::STATUS_PUBLISHED,
				'filename' => $cabinet['Cabinet']['name'],
				'is_folder' => 1,
			]
		];

		if ($rootFolder = $model->save($rootFolder)) {
			$tree = [
				'CabinetFileTree' => [
					'cabinet_key' => $cabinet['Cabinet']['key'],
					'cabinet_file_key' => $rootFolder['CabinetFile']['key'],
					'cabinet_file_id' => $rootFolder['CabinetFile']['id'],
				]
			];
			$result = $model->CabinetFileTree->save($tree);
			return ($result) ? true : false;
		} else {
			return false;
		}
	}

/**
 * Cabinetのルートフォルダが存在するか
 *
 * @param Model $model CabinetFile
 * @param array $cabinet Cabinetデータ
 * @return bool true:存在する false:存在しない
 */
	public function rootFolderExist(Model $model, $cabinet) {
		// ルートフォルダが既に存在するかを探す
		$conditions = $this->_getRootFolderConditions($cabinet);
		$count = $model->find('count', ['conditions' => $conditions]);
		return ($count > 0);
	}

/**
 * フォルダの合計サイズを得る
 *
 * @param Model $model CabinetFile
 * @param array $folder CabinetFileデータ
 * @return int 合計サイズ
 */
	public function getTotalSizeByFolder(Model $model, $folder) {
		// ベタパターン
		// 配下全てのファイルを取得する
		//$this->CabinetFileTree->setup(]);
		$cabinetKey = $folder['Cabinet']['key'];
		$conditions = [
			'CabinetFileTree.cabinet_key' => $cabinetKey,
			'CabinetFileTree.lft >' => $folder['CabinetFileTree']['lft'],
			'CabinetFileTree.rght <' => $folder['CabinetFileTree']['rght'],
			'CabinetFile.is_folder' => false,
		];
		$files = $model->find('all', ['conditions' => $conditions]);
		$total = 0;
		foreach ($files as $file) {

			$total += Hash::get($file, 'UploadFile.file.size', 0);
		}
		return $total;
		// sumパターンはUploadFileの構造をしらないと厳しい… がんばってsumするより合計サイズをキャッシュした方がいいかも
	}

/**
 * ルートフォルダ（＝キャビネット）をFindするためのconditionsを返す
 *
 * @param array $cabinet Cabinetデータ
 * @return array conditions
 */
	protected function _getRootFolderConditions($cabinet) {
		$conditions = [
			'cabinet_key' => $cabinet['Cabinet']['key'],
			'parent_id' => null,
		];
		return $conditions;
	}

}