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

		$model->Behaviors->disable('AuthorizationKey');
		$model->Behaviors->disable('Attachment');

		$count = $model->find('count', ['conditions' => $conditions]);

		$model->Behaviors->enable('AuthorizationKey');
		$model->Behaviors->enable('Attachment');
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
		$model->Behaviors->disable('AuthorizationKey');
		$model->Behaviors->disable('Attachment');

		$rootFolder = $model->find('first', [
			'conditions' => $this->_getRootFolderConditions(
				$cabinet,
				array(
					'OR' => array(
						'CabinetFile.language_id' => Current::read('Language.id'),
						'CabinetFile.is_translation' => false,
					)
				)
			)
		]);

		$model->Behaviors->enable('AuthorizationKey');
		$model->Behaviors->enable('Attachment');

		return $rootFolder;
	}

/**
 * キャビネットのルートフォルダとキャビネットの同期
 * ルートフォルダがなければ作成する
 *
 * @param Model $model CabinetFile
 * @param array $cabinet Cabinet model data
 * @return bool
 */
	public function syncRootFolder(Model $model, $cabinet) {
		if ($this->rootFolderExist($model, $cabinet)) {
			// ファイル名同期
			$options = [
				'conditions' => $this->_getRootFolderConditions(
					$cabinet, ['CabinetFile.language_id' => Current::read('Language.id')]
				)
			];
			$rootFolder = $model->find('first', $options);
			if ($rootFolder['CabinetFile']['filename'] == $cabinet['Cabinet']['name']) {
				// ファイル名が同じならupdate不要
				return true;
			}
			$rootFolder['CabinetFile']['filename'] = $cabinet['Cabinet']['name'];
			$model->Behaviors->disable('Topics');
			$model->useNameValidation = false;
			$result = ($model->save($rootFolder)) ? true : false;
			$model->useNameValidation = true;
			$model->Behaviors->enable('Topics');
			return $result;
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
		$model->loadModels([
			'CabinetFileTree' => 'Cabinets.CabinetFileTree',
		]);

		// $modelのTopicビヘイビアを停止
		$model->Behaviors->disable('Topics');
		$model->create();
		$model->useNameValidation = false;

		$rootFolderTree = $model->CabinetFileTree->find('first', array(
			'recursive' => -1,
			'conditions' => $this->_getRootFolderConditions($cabinet),
		));

		$rootFolder = Hash::merge([
			'CabinetFileTree' => [
				'cabinet_key' => $cabinet['Cabinet']['key'],
			],
			'CabinetFile' => [
				'cabinet_key' => $cabinet['Cabinet']['key'],
				'status' => WorkflowComponent::STATUS_PUBLISHED,
				'filename' => $cabinet['Cabinet']['name'],
				'is_folder' => 1,
				'key' => Hash::get($rootFolderTree, 'CabinetFileTree.cabinet_file_key'),
			]
		], $rootFolderTree);

		$result = $model->save($rootFolder);
		if ($rootFolder) {
			$result = (bool)$result;
			$model->useNameValidation = true;
		} else {
			$result = false;
		}
		// $modelのTopicビヘイビアを復帰
		$model->Behaviors->enable('Topics');
		return $result;
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
		$conditions = $this->_getRootFolderConditions(
			$cabinet, ['CabinetFile.language_id' => Current::read('Language.id')]
		);
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
		// 配下全てのファイル key を取得する
		$cabinetKey = $folder['Cabinet']['key'];
		$conditions = [
			'CabinetFileTree.cabinet_key' => $cabinetKey,
			'CabinetFileTree.lft >' => $folder['CabinetFileTree']['lft'],
			'CabinetFileTree.rght <' => $folder['CabinetFileTree']['rght'],
			'CabinetFile.is_folder' => false,
		];
		$model->Behaviors->disable('AuthorizationKey');

		// HACK: ファイルサイズをCabinetsに持つようにすればFilesプラグインのテーブルを参照する必要はなくなるが、
		//       テーブル変更すると移行ツールも改修する必要があるので、
		//       ひとまずトータル取得ロジックのカイゼンだけにとどめた。
		$files = $model->find('all', [
			'conditions' => $conditions,
			//'fields' => ['CabinetFile.key']
			'fields' => ['CabinetFile.id']
		]);
		$contentIds = array_column(array_column($files, 'CabinetFile'), 'id');
		if (empty($contentIds)) {
			return 0;
		}

		/** @var UploadFilesContent $uploadFilesContent */
		$uploadFilesContent = ClassRegistry::init('Files.UploadFilesContent');
		$conditions = [
			'UploadFilesContent.plugin_key' => 'cabinets',
			'UploadFilesContent.content_id' => $contentIds,
		];
		$links = $uploadFilesContent->find('all', [
			'conditions' => $conditions,
			'recursive' => -1,
			'fields' => ['UploadFilesContent.upload_file_id']
		]);
		$uploadFileIds = array_column(array_column($links, 'UploadFilesContent'), 'upload_file_id');
		if (empty($uploadFileIds)) {
			return 0;
		}

		/** @var UploadFile $uploadFile */
		$uploadFile = ClassRegistry::init('Files.UploadFile');
		$conditions = [
			'UploadFile.id' => $uploadFileIds
		];
		$uploadFile->virtualFields['cabinet_folder_size'] = 'sum(size)';
		$result = $uploadFile->find('first', [
			'conditions' => $conditions,
			'fields' => ['cabinet_folder_size' ],
			//'recursive' => 0
		]);
		unset($uploadFile->virtualFields['cabinet_folder_size']);

		$total = (int)$result['UploadFile']['cabinet_folder_size'];
		return $total;
	}

/**
 * ルートフォルダ（＝キャビネット）をFindするためのconditionsを返す
 *
 * @param array $cabinet Cabinetデータ
 * @param array $addConditions 条件
 * @return array conditions
 */
	protected function _getRootFolderConditions($cabinet, $addConditions = array()) {
		$conditions = Hash::merge([
			'CabinetFileTree.cabinet_key' => $cabinet['Cabinet']['key'],
			'CabinetFileTree.parent_id' => null,
		], $addConditions);

		return $conditions;
	}

/**
 * Cabinet.total_sizeに容量をキャッシュする
 *
 * @param Model $model モデル
 * @param int $cabinetKey キャビネットKEY
 * @return void
 * @throws InternalErrorException
 */
	public function updateCabinetTotalSize(Model $model, $cabinetKey) {
		$model->loadModels([
			'Cabinet' => 'Cabinets.Cabinet',
		]);
		$cabinet = $model->Cabinet->find('first', array(
			'recursive' => -1,
			'conditions' => array('key' => $cabinetKey),
		));
		$totalSize = $this->calcCabinetTotalSize($model, $cabinet);

		// キャビネット更新
		$update = array(
			'Cabinet.total_size' => $totalSize
		);
		$conditions = array(
			'Cabinet.block_id' => $cabinet['Cabinet']['block_id']
		);
		if (! $model->Cabinet->updateAll($update, $conditions)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		//$cabinet['Cabinet']['total_size'] = $totalSize;
		//$model->Cabinet->save($cabinet, ['callbacks' => false]);
		//$model->Cabinet->id = $cabinetId;
		//$model->Cabinet->saveField('total_size', $totalSize, ['callbacks' => false]);
	}

	/**
	 * calcCabinetTotalSize
	 *
	 * @param Model $model
	 * @param $cabinet
	 * @return mixed
	 */
	public function calcCabinetTotalSize(Model $model, $cabinet) {
		// トータルサイズ取得
		$rootFolder = $model->getRootFolder($cabinet);
		$totalSize = $model->getTotalSizeByFolder(
			$rootFolder
		);
		return $totalSize;
	}
}