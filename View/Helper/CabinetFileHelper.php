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
 * 圧縮ダウンロードのキー
 *
 * var array
 */
	private $__zipDolowdKeys = [];

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
		$currentData = $this->_View->request->data;

		// * チェック用のToken作成
		$checkRequest = [
			'CabinetFile' => [
				'key' => $cabinetFile['CabinetFile']['key'],
				'_action' => 'check'
			],
		];
		$tokenFields = Hash::flatten($checkRequest);
		$hiddenFields = array_keys($tokenFields);
		$this->_View->request->data = $checkRequest;
		$checkToken = $this->Token->getToken(
			'CabinetFile', $checkUrl, $tokenFields, $hiddenFields
		);
		// * ダウンロード用のToken作成
		$downloadRequest = [
			'CabinetFile' => [
				'key' => $cabinetFile['CabinetFile']['key'],
				'_action' => 'download'
			],
		];
		$tokenFields = Hash::flatten($downloadRequest);
		$hiddenFields = array_keys($tokenFields);
		$this->_View->request->data = $downloadRequest;
		$downloadToken = $this->Token->getToken(
			'CabinetFile', $downloadUrl, $tokenFields, $hiddenFields
		);
		// * $thisi->request->dataを元に戻す
		$this->_View->request->data = $currentData;

		$requestData['Check'] = [
			'action' => $checkUrl,
			'request' => $checkRequest['CabinetFile'],
			'token' => $checkToken['_Token'],
		];
		$requestData['Download'] = [
			'action' => $downloadUrl,
			'request' => $downloadRequest['CabinetFile'],
			'token' => $downloadToken['_Token'],
		];

		//アンカータグ生成
		$options['ng-controller'] = 'CabinetFiles.zipDownload';
		$options['ng-init'] = "initialize(" . json_encode($requestData) . ")";
		$options['ng-click'] = 'download($event)';
		$options['href'] = '';
		$attributes = $this->_parseAttributes($options);

		$html .= "<a$attributes>" . h($label) . "</a>";

		$frameId = (string)Current::read('Frame.id');
		if (! isset($this->__zipDolowdKeys[$frameId])) {
			$this->__zipDolowdKeys[$frameId] = [];
		}
		$this->__zipDolowdKeys[$frameId][] = $cabinetFile['CabinetFile']['key'];

		return $html;
	}

/**
 * 圧縮ダウンロードリンクのロードタグ出力
 *
 * @param string $frameId フレームID
 * @return string
 */
	public function loadZipDownload($frameId) {
		$html = '';
		if (! isset($this->__zipDolowdKeys[$frameId])) {
			return $html;
		}

		$html .= '<div style="displya: none;"' .
				' ng-controller="CabinetFiles.loadZipDownload"' .
				' ng-init="load(\'' . $frameId . '\', ' .
					'\'' . implode(',', $this->__zipDolowdKeys[$frameId]) . '\')"></div>';
		return $html;
	}

/**
 * 圧縮ダウンロードのためのTokenセット
 *
 * @param string $cabinetFileKey ファイルキー
 * @return void
 */
	public function setZipDownloadToken($cabinetFileKey) {
		$cabinetFile = [
			'CabinetFile' => [
				'key' => $cabinetFileKey
			],
		];
		$this->zipDownload($cabinetFile, '', []);
	}

}
