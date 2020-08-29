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
 *
 * @property NetCommonsHtmlHelper $NetCommonsHtml
 * @property TokenHelper $Token
 */
class CabinetFileHelper extends AppHelper {

/**
 * @var array helpers
 */
	public $helpers = [
		'NetCommons.NetCommonsHtml',
		'NetCommons.Token',
	];

/**
 * Before render callback. beforeRender is called before the view file is rendered.
 *
 * Overridden in subclasses.
 *
 * @param string $viewFile The view file that is going to be rendered
 * @return void
 */
	public function beforeRender($viewFile) {
		$this->NetCommonsHtml->script('/cabinets/js/cabinets_zip_download.js');
		parent::beforeRender($viewFile);
	}

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

/**
 * 圧縮ダウンロードリンク
 *
 * @param array $cabinetFile CabinetFile data
 * @param string $label ボタン(リンク)のラベル
 * @param string $options ボタン(リンク)のオプション
 * @return string
 */
	public function zipDownload($cabinetFile, $label, $options) {
		$html = '';

		$rootUrl = substr(Router::url('/'), 0, -1);

		//アクションURL生成
		$action = [
			'action' => 'download_folder',
			'key' => $cabinetFile['CabinetFile']['key']
		];
		$downloadUrl = substr($this->NetCommonsHtml->url($action), strlen($rootUrl));

		$action = [
			'action' => 'check_download_folder',
			'key' => $cabinetFile['CabinetFile']['key']
		];
		$checkUrl = substr($this->NetCommonsHtml->url($action), strlen($rootUrl));

		//POSTデータ生成
		$requestData = [
			'CabinetFile' => [
				'key' => $cabinetFile['CabinetFile']['key']
			],
		];
		$currentData = $this->_View->request->data;
		$tokenFields = Hash::flatten($requestData);
		$hiddenFields = array_keys($tokenFields);
		// * チェック用のToken作成
		$this->_View->request->data = $requestData;
		$checkToken = $this->Token->getToken(
			'CabinetFile', $checkUrl, $tokenFields, $hiddenFields
		);
		$checkToken['_Token']['key'] = '';
		// * ダウンロード用のToken作成
		$this->_View->request->data = $requestData;
		$downloadToken = $this->Token->getToken(
			'CabinetFile', $downloadUrl, $tokenFields, $hiddenFields
		);
		$downloadToken['_Token']['key'] = '';
		// * $thisi->request->dataを元に戻す
		$this->_View->request->data = $currentData;

		$requestData['Check'] = [
			'action' => $checkUrl,
			'token' => $checkToken['_Token'],
		];

		$requestData['Download'] = [
			'action' => $downloadUrl,
			'token' => $downloadToken['_Token'],
		];
		//アンカータグ生成
		$options['ng-controller'] = 'CabinetFiles.zipDownload';
		$options['ng-init'] = "initialize(" . json_encode($requestData) . ")";
		$options['ng-click'] = 'download($event)';
		$options['href'] = '';
		$attributes = $this->_parseAttributes($options);

		$html .= "<a$attributes>" . h($label) . "</a>";
		return $html;
	}

}
