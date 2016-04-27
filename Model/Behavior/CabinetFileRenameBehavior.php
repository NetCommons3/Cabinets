<?php

class CabinetFileRenameBehavior extends ModelBehavior {

	public function beforeSave(Model $model, $options = array()) {
		//$isFile = ! $model->data['CabinetFile']['is_folder'];
		// ファイル名を変更したらUploadFileのファイル名も合わせる。
		//if($isFile && $model->data['CabinetFile']['file']['error'] == UPLOAD_ERR_NO_FILE){
		//	// uploadされてないときはUploadFileをリネーム
		//
		//	$uploadFile = $model->data['UploadFile']['file']['original_name'] = 'test';
		//}


		// 名称重複対策
		// 名称重複チェック
		$cabinetFile = $model->data;
		if ($this->_existSameFilename($model, $cabinetFile)) {
			//
			// 重複してたら自動リネーム
			$this->_autoRename($model, $cabinetFile);
			//if($isFile && $model->data['CabinetFile']['file']['error'] == UPLOAD_ERR_OK){
			//	// ファイルがアップされたときにリネームしたらファイルもリネームする
			//	$model->data['CabinetFile']['file']['name'] = $model->data['CabinetFile']['filename'];
			//}
		}
		return parent::beforeSave($model, $options);

	}


	protected function _existSameFilename(CabinetFile $model, $cabinetFile) {
		$conditions = [
			'CabinetFile.key !=' => $cabinetFile['CabinetFile']['key'],
			'CabinetFileTree.parent_id' => $cabinetFile['CabinetFileTree']['parent_id'],
			'CabinetFile.filename' => $cabinetFile['CabinetFile']['filename'],
		];
		$count = $model->find('count', ['conditions' => $conditions]);
		return ($count > 0);
	}

	protected function _autoRename(CabinetFile $model, $cabinetFile) {
		$index = 0;
		if ($cabinetFile['CabinetFile']['is_folder']) {
			// folder
			$baseFolderName = $cabinetFile['CabinetFile']['filename'];
			while($this->_existSameFilename($model, $cabinetFile)){
				// 重複し続ける限り数字を増やす
				$index++;
				$newFilename = sprintf('%s%03d', $baseFolderName, $index);
				$cabinetFile['CabinetFile']['filename'] = $newFilename;
			}
			$model->data['CabinetFile']['filename'] = $cabinetFile['CabinetFile']['filename'];
		} else {
			list($baseFileName, $ext) = $model->splitFileName($cabinetFile['CabinetFile']['filename']);
			$extString = is_null($ext) ? '' : '.' . $ext;

			while($this->_existSameFilename($model, $cabinetFile)){
				// 重複し続ける限り数字を増やす
				$index++;
				$newFilename = sprintf('%s%03d', $baseFileName, $index);
				$cabinetFile['CabinetFile']['filename'] = $newFilename . $extString;
			}
			$model->data['CabinetFile']['filename'] = $cabinetFile['CabinetFile']['filename'];
		}
	}




}
