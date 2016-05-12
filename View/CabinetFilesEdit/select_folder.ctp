<?php

$currentFolderTree = Hash::extract($folderPath, '{n}.CabinetFileTree.id');
$currentFolderTree = array_map('intval', $currentFolderTree);

?>
<style>
	.cabinets__folder-tree__folder {
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}

	.cabinets-folder-tree li.list-group-item {
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}

	/*.cabinets__folder-tree__folder:hover{*/
	/*background: #ccc;*/
	/*}*/
	.cabinets__folder-tree-toggle {
		cursor: pointer;
	}

	span.cabinets-nest {
		margin-left: 15px;
	}
</style>
<ul class="list-group" ng-controller="Cabinets.FolderTree"
	ng-init="init(<?php echo json_encode($currentFolderTree) ?>)">
	<?php

	$this->CabinetsFolderTree->renderSelectFolderTree($folders, $currentTreeId);
	// TODO Modalで移動先を選択したら移動先行がactive化
	// TODO 移動の確定は「決定」ボタンを押して確定とする。（編集時は別）

	?>
</ul>
