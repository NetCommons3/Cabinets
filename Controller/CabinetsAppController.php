<?php
/**
 * CabinetsApp
 */
App::uses('AppController', 'Controller');

/**
 * Class CabinetsAppController
 *
 * @property CabinetFrameSetting $CabinetFrameSetting
 */
class CabinetsAppController extends AppController {

/**
 * @var array キャビネット名
 */
	protected $_cabinetTitle;

/**
 * @var array キャビネット設定
 */
	protected $_cabinetSetting;

/**
 * @var array フレーム設定
 */
	protected $_frameSetting;

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		//'NetCommons.NetCommonsBlock',
		//'NetCommons.NetCommonsFrame',
		'Pages.PageLayout',
		'Security',
	);

/**
 * @var array helpers
 */
	public $helpers = array(
		'Cabinets.CabinetsFormat',
	);

/**
 * @var array use model
 */
	public $uses = array(
		'Cabinets.Cabinet',
		'Cabinets.CabinetSetting',
		'Cabinets.CabinetFrameSetting'
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * 現在時刻を返す。テストしやすくするためにメソッドに切り出した。
 *
 * @return int
 */
	protected function _getNow() {
		return time();
	}

/**
 * 現在の日時を返す
 *
 * @return string datetime
 */
	protected function _getCurrentDateTime() {
		return date('Y-m-d H:i:s', $this->_getNow());
	}

/**
 * ブロック名をキャビネットタイトルとしてセットする
 *
 * @return void
 */
	protected function _setupCabinetTitle() {
		$this->loadModel('Blocks.Block');
		$block = $this->Block->findById(Current::read('Block.id'));
		$this->_cabinetTitle = $block['Block']['name'];
	}

/**
 * フレーム設定を読みこむ
 *
 * @return void
 */
	protected function _loadFrameSetting() {
		$this->_frameSetting = $this->CabinetFrameSetting->getCabinetFrameSetting(true);
	}

/**
 * 設定等の呼び出し
 *
 * @return void
 */
	protected function _prepare() {
		$this->_setupCabinetTitle();
		$this->initCabinet(['cabinetSetting']);
		$this->_loadFrameSetting();
	}

/**
 * namedパラメータ取得
 * 
 * @param string $name namedパラメータ名
 * @param null $default パラメータが存在しなかったときのデフォルト値
 * @return int|string
 */
	protected function _getNamed($name, $default = null) {
		$value = isset($this->request->params['named'][$name]) ? $this->request->params['named'][$name] : $default;
		return $value;
	}

/**
 * initTabs
 *
 * @param string $mainActiveTab Main active tab
 * @param string $blockActiveTab Block active tab
 * @return void
 */
	public function initTabs($mainActiveTab, $blockActiveTab) {
		//タブの設定
		$settingTabs = array(
			'tabs' => array(
				'block_index' => array(
					'url' => array(
						'plugin' => $this->params['plugin'],
						'controller' => 'cabinet_blocks',
						'action' => 'index',
						Current::read('Frame.id'),
					)
				),
				'frame_settings' => array(
					'url' => array(
						'plugin' => $this->params['plugin'],
						'controller' => 'cabinet_frame_settings',
						'action' => 'edit',
						Current::read('Frame.id'),
					),
				),
			),
			'active' => $mainActiveTab
		);
		$this->set('settingTabs', $settingTabs);

		$blockSettingTabs = array(
			'tabs' => array(
				'block_settings' => array(
					'url' => array(
						'plugin' => $this->params['plugin'],
						'controller' => 'cabinet_blocks',
						'action' => $this->params['action'],
						Current::read('Frame.id'),
						Current::read('Block.id')
					)
				),
				'role_permissions' => array(
					'url' => array(
						'plugin' => $this->params['plugin'],
						'controller' => 'cabinet_block_role_permissions',
						'action' => 'edit',
						Current::read('Frame.id'),
						Current::read('Block.id')
					)
				),
			),
			'active' => $blockActiveTab
		);
		$this->set('blockSettingTabs', $blockSettingTabs);
	}

/**
 * initCabinet
 *
 * @param array $contains Optional result sets
 * @return bool True on success, False on failure
 */
	public function initCabinet($contains = []) {
		if (! $cabinet = $this->Cabinet->getCabinet(Current::read('Block.id'), Current::read('Room.id'))) {
			$this->throwBadRequest();
			return false;
		}
		$cabinet = $this->camelizeKeyRecursive($cabinet);
		$this->_cabinetTitle = $cabinet['cabinet']['name'];
		$this->set($cabinet);

		if (! $cabinetSetting = $this->CabinetSetting->getCabinetSetting($cabinet['cabinet']['key'])) {
			$cabinetSetting = $this->CabinetSetting->create(
				array('id' => null)
			);
		}
		$this->_cabinetSetting = $cabinetSetting;
		//$cabinetSetting = $this->camelizeKeyRecursive($cabinetSetting);
		$this->set('cabinetSetting', $cabinetSetting['CabinetSetting']);
		//$this->set($cabinetSetting);

		// Cabinetでは_loadFrameSettingでやってる。
		//if (in_array('cabinetFrameSetting', $contains, true)) {
		//	if (! $cabinetFrameSetting = $this->CabinetFrameSetting->getCabinetFrameSetting($this->viewVars['frameKey'])) {
		//		$cabinetFrameSetting = $this->CabinetFrameSetting->create(array(
		//			'frame_key' => $this->viewVars['frameKey']
		//		));
		//	}
		//	$cabinetFrameSetting = $this->camelizeKeyRecursive($cabinetFrameSetting);
		//	$this->set($cabinetFrameSetting);
		//}

		$this->set('userId', (int)$this->Auth->user('id'));

		return true;
	}

}
