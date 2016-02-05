<?php

$currentFolderTree = Hash::extract($folderPath, '{n}.CabinetFileTree.id');
$currentFolderTree = array_map('intval', $currentFolderTree);

?>
<style>
	.cabinets__folder-tree__folder{
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
	.cabinets-folder-tree li.list-group-item{
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
	/*.cabinets__folder-tree__folder:hover{*/
	/*background: #ccc;*/
	/*}*/
	.cabinets__folder-tree-toggle{
		cursor: pointer;
	}
	span.cabinets-nest{
		margin-left: 15px;
	}
</style>
<ul class="list-group" ng-controller="Cabinets.FolderTree" ng-init="init(<?php echo json_encode($currentFolderTree)?>)">
	<li class="list-group-item ">
		<?php
		echo $this->NetCommonsHtml->link('<span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>' . h($cabinet['Cabinet']['name']), NetCommonsUrl::backToIndexUrl(), ['escape' => false]);
		?>
	</li>
	<?php


	$this->CabinetsFolderTree->renderSelectFolderTree($folders, $currentTreeId );

	?>
</ul>

<button ng-click="select(<?php echo Current::read('Frame.id'); ?>)">SELECT</button>