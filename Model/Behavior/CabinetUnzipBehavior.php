<?php
/**
 * CabinetUnzipBehavior
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Class CabinetUnzipBehavior
 */
class CabinetUnzipBehavior extends ModelBehavior {

/**
 * キャビネットファイルのUnzip
 *
 * @param Model $model CabinetFile
 * @param array $cabinetFile CabinetFileデータ
 * @return bool
 * @throws InternalErrorException
 */
	public function unzip(Model $model, $cabinetFile) {
		$model->begin();

		try {
			// テンポラリフォルダにunzip
			$zipPath = WWW_ROOT . $cabinetFile['UploadFile']['file']['path'] .
				$cabinetFile['UploadFile']['file']['id'] . DS .
				$cabinetFile['UploadFile']['file']['real_file_name'];
			//debug($zipPath);
			App::uses('UnZip', 'Files.Utility');
			$unzip = new UnZip($zipPath);
			$tmpFolder = $unzip->extract();
			if ($tmpFolder === false) {
				throw new InternalErrorException('UnZip Failed.');
			}

			$parentCabinetFolder = $model->find(
				'first',
				['conditions' => ['CabinetFileTree.id' => $cabinetFile['CabinetFileTree']['parent_id']]]
			);

			// unzipされたファイル拡張子のバリデーション
			// unzipされたファイルのファイルサイズバリデーション
			$files = $tmpFolder->findRecursive();
			$unzipTotalSize = 0;
			foreach ($files as $file) {
				//
				$unzipTotalSize += filesize($file);

				// ここでは拡張子だけチェックする
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				if (!$model->isAllowUploadFileExtension($extension)) {
					// NG
					$model->validationErrors = [
						__d('cabinets', 'Unzip failed. Contains does not allow file format.')
					];
					return false;
				}
			}
			// ルームファイルサイズ制限
			$maxRoomDiskSize = Current::read('Space.room_disk_size');
			if ($maxRoomDiskSize !== null) {
				// nullだったらディスクサイズ制限なし。null以外ならディスクサイズ制限あり
				// 解凍後の合計
				// 現在のルームファイルサイズ
				$roomId = Current::read('Room.id');
				$roomFileSize = $model->getTotalSizeByRoomId($roomId);
				if (($roomFileSize + $unzipTotalSize) > $maxRoomDiskSize) {

					$model->validationErrors[] = __d(
						'cabinets',
						'Failed to expand. The total size exceeds the limit.<br />' .
						'The total size limit is %s (%s left).',
						CakeNumber::toReadableSize($roomFileSize + $unzipTotalSize),
						CakeNumber::toReadableSize($maxRoomDiskSize)
					);
					return false;
				}
			}

			// 再帰ループで登録処理
			list($folders, $files) = $tmpFolder->read(true, false, true);
			foreach ($files as $file) {
				$this->_addFileFromPath($model, $parentCabinetFolder, $file);
			}
			foreach ($folders as $folder) {
				$this->_addFolderFromPath($model, $parentCabinetFolder, $folder);
			}
		} catch (Exception $e) {
			return $model->rollback($e);
		}
		$model->commit();
		return true;
	}

/**
 * フォルダパスにある実フォルダをキャビネットに登録する
 *
 * @param Model $model Model
 * @param array $parentCabinetFolder 登録する親フォルダ
 * @param string $folderPath 実フォルダのパス
 * @throws InternalErrorException
 * @return void
 */
	protected function _addFolderFromPath(Model $model, $parentCabinetFolder, $folderPath) {
		$newFolder = [
			'CabinetFile' => [
				'cabinet_key' => $parentCabinetFolder['CabinetFile']['cabinet_key'],
				'is_folder' => true,
				'filename' => $model->basename($folderPath),
				'status' => WorkflowComponent::STATUS_PUBLISHED,
			],
			'CabinetFileTree' => [
				'parent_id' => $parentCabinetFolder['CabinetFileTree']['id'],
				'cabinet_key' => $parentCabinetFolder['CabinetFileTree']['cabinet_key'],
			],
		];
		$newFolder = $model->create($newFolder);

		if (!$savedFolder = $model->saveFile($newFolder)) {
			throw new InternalErrorException('Save Failed');
		}
		//// folder配下のread
		$thisFolder = new Folder($folderPath);
		list($folders, $files) = $thisFolder->read(true, false, true);
		// 配下のファイル登録
		foreach ($files as $childFilePath) {
			$this->_addFileFromPath($model, $savedFolder, $childFilePath);
		}
		// 配下のフォルダ登録
		foreach ($folders as $childFolderPath) {
			$this->_addFolderFromPath($model, $savedFolder, $childFolderPath);
		}
	}

/**
 * ファイルパスにある実ファイルをキャビネットに登録する
 *
 * @param Model $model Model
 * @param array $parentCabinetFolder 登録する親フォルダ
 * @param string $filePath 実ファイルのパス
 * @throws InternalErrorException
 * @return void
 */
	protected function _addFileFromPath(Model $model, $parentCabinetFolder, $filePath) {
		$newFile = $this->_makeCabinetFileDataFromPath($model, $parentCabinetFolder, $filePath);

		if (!$model->saveFile($newFile)) {
			throw new InternalErrorException('Save Failed');
		}
	}

/**
 * CabinetFileデータをファイルパスから作成する
 *
 * @param Model $model Model
 * @param array $parentCabinetFolder 親フォルダデータ
 * @param string $filePath ファイルパス
 * @return array フォームからポストされる形のCabinetFileデータ
 */
	protected function _makeCabinetFileDataFromPath(Model $model, $parentCabinetFolder,
		$filePath) {
		//MIMEタイプの取得
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mimeType = $finfo->file($filePath);

		$newFile = [
			'CabinetFile' => [
				'cabinet_key' => $parentCabinetFolder['CabinetFile']['cabinet_key'],
				'is_folder' => false,
				'filename' => $model->basename($filePath),
				'status' => WorkflowComponent::STATUS_PUBLISHED,
				'file' => [
					'name' => $model->basename($filePath),
					'type' => $mimeType,
					'tmp_name' => $filePath,
					'error' => 0,
					'size' => filesize($filePath),
				],
			],
			'CabinetFileTree' => [
				'parent_id' => $parentCabinetFolder['CabinetFileTree']['id'],
				'cabinet_key' => $parentCabinetFolder['CabinetFileTree']['cabinet_key'],
			],
		];
		$newFile = $model->create($newFile);
		return $newFile;
	}
}
