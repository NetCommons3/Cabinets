<?php

$currentFolderTree = Hash::extract($folderPath, '{n}.CabinetFileTree.id');
$currentFolderTree = array_map('intval', $currentFolderTree);
?>


	<ul class="list-group" ng-cloak  ng-init="init(<?php echo json_encode($currentFolderTree)?>)" id="cabinet-files-folder-tree">
		<?php //if ($currentTreeId > 0): ?>
		<!--	<li class="list-group-item cabinets__folder-tree__folder">-->
		<!--		--><?php
		//		echo '<span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>';
		//		echo $this->NetCommonsHtml->link( h($cabinet['Cabinet']['name']), NetCommonsUrl::backToIndexUrl(), ['escape' => false]);
		//		?>
		<!--	</li>-->
		<?php //else:?>
		<!--	<li class="list-group-item active cabinets__folder-tree__folder">-->
		<!--		--><?php
		//		echo '<span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>'
		//			. h($cabinet['Cabinet']['name']);
		//		?>
		<!--	</li>-->
		<?php //endif ?>
		<?php

		$this->CabinetsFolderTree->render($folders, $currentTreeId);

		?>
	</ul>
