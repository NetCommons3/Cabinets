<?php
$tmpFolderPath = $folderPath;
$currentFile = array_pop($tmpFolderPath); // 現在のファイルを後ろから取り出す
$encodedFolderPath = json_encode($tmpFolderPath);
?>
<span ng-controller="Cabinets.path" ng-init='init(<?php echo h($encodedFolderPath) ?>,
 "<?php echo Router::url(NetCommonsUrl::backToPageUrl()); ?>")' ng-cloak>

<?php
//// パンクズ
//if($folderPath){
//	echo $this->NetCommonsHtml->link($cabinet['Cabinet']['name'], NetCommonsUrl::backToIndexUrl());
//}else{
//	echo h($cabinet['Cabinet']['name']);
//}
//?>
	<?php
	?>
	<span ng-repeat="cabinetFile in folderPath"><a href="{{cabinetFile.url}}">{{cabinetFile.CabinetFile.filename}}</a><span>&nbsp;&gt;&nbsp;</span></span>
	<?php
	//foreach($_tmpFolderPath as $folder){
	//	echo ' &gt; ';
	//	echo $this->NetCommonsHtml->link($folder['CabinetFile']['filename'], ['key' => $folder['CabinetFile']['key']]);
	//}
	echo h($currentFile['CabinetFile']['filename']);
	?>
</span>
