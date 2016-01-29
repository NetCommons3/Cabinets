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
class CabinetsFolderTreeHelper extends AppHelper {

/**
 * @var array helpers
 */
	public $helpers = array('NetCommons.Date', 'Html');

	protected $_currentFolderTree = array();
	protected $_currentFolderId = 0;

	public function render($folders, $currentFolderId, $currentFolderTree){
		//$this->_currentFolderTree = $currentFolderTree;
		$this->_currentFolderId = $currentFolderId;
		$this->_render($folders);
	}

	public function _render($folders, $nest = 0, $parentFolderId = 0){
		foreach($folders as $folder){
			$folderId = $folder['CabinetFile']['id'];
			$isActiveFolder = ($folderId == $this->_currentFolderId);
			$tree = '';
			for($i = 0; $i < $nest; $i++){
				$tree .= $this->Html->tag('span', '', ['class' => 'cabinets-nest']);;
			}
			// currentフォルダか
			if ($folder['CabinetFile']['id'] == $this->_currentFolderId) {
				$active = 'active';
			}else {
				$active = '';
			}
			// open or close
			//if(in_array($folder['CabinetFile']['id'], $this->_currentFolderTree)){
			//	// カレント or カレントの親フォルダ
			//	$arrowIcon = '<span class="glyphicon glyphicon-menu-down" style="width: 15px"></span>';
			//	$folderIcon = ' <span class="glyphicon glyphicon-folder-open" aria-hidden="true" ></span>';
			//}else{
				// 下位のフォルダがなければアローアイコン不要
				if(Hash::get($folder, 'children', false)) {
					$arrowIcon = '<span class="glyphicon cabinets__folder-tree-toggle" aria-hidden="true"  style="width: 15px" ng-class="{\'glyphicon-chevron-down\': folder['.$folderId.'], \'glyphicon-chevron-right\': ! folder['.$folderId.']}" ng-click="toggle('.$folderId.')"></span> ';
					//$arrowIcon = $this->Html->link(
					//	$arrowIcon,
					//	'#cabinets-folder-tree-children-' . $folderId,
					//	[
					//		//'data-toggle' => 'collapse',
					//		//'aria-controls' => 'cabinets-folder-tree-children-' . $folderId,
					//		//'aria-expanded' => 'true',
					//		'escape' => false,
					//		'ng-click' => 'toggle('.$folderId.')',
					//	]
					//	);
				}else{
					$arrowIcon = '<span  class="glyphicon" style="width: 15px"></span> ';
				}

				//$arrowIcon = '   <a data-toggle="collapse" aria-controls="cabinets-folder-tree-children-'.$folderId.'" href="#cabinets-folder-tree-children-'.$folderId.'" aria-expanded="false">===</a>';
				$folderIcon = '<span class="glyphicon " aria-hidden="true" ng-class="{\'glyphicon-folder-open\': folder['.$folderId.'], \'glyphicon-folder-close\': ! folder['.$folderId.']}"></span>';
			//}
			//echo $this->Html->link($tree . $arrowIcon . $folderIcon . $folder['CabinetFile']['filename'],
			//	'',
			//	['escape' => false, 'class' => 'list-group-item ' . $active]
			//);
			$options = [
				'escape' => false,
				'class' => 'cabinets__folder-tree__folder list-group-item ' . $active,
			];
			if($parentFolderId > 0){
				$options['ng-show'] = 'folder[' . $parentFolderId .']';
			}


			// TODO actveだったらリンクしない
			if($isActiveFolder){
				echo $this->Html->tag('li',
					$tree . $arrowIcon . $folderIcon . $folder['CabinetFile']['filename'],
					$options
				);
			}else{
				echo $this->Html->tag('li',
					$tree . $arrowIcon . $folderIcon . '<a href="">'.$folder['CabinetFile']['filename'].'</a>',
					$options
				);
			}

			if(Hash::get($folder, 'children', false)){
				//echo '<div id="cabinets-folder-tree-children-'.$folderId.'" class="" __ng-show="folder['.$folderId.']">';
				$this->_render($folder['children'], $nest+1, $folderId);
				//echo '</div>';
			}

		}
	}
}
