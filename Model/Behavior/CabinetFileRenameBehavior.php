<?php
//beforesaveでやらずにsaveFileでやったほうがいいか？
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





}
