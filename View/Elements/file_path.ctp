<?php
$_tmpFolderPath = $folderPath;
$currentFile = array_pop($_tmpFolderPath);
$encodedFolderPath = json_encode($_tmpFolderPath);
?>
<span ng-controller="Cabinets.path" ng-init='init(<?php echo h($encodedFolderPath) ?>)'>

<?php
// パンクズ
if($folderPath){
	echo $this->NetCommonsHtml->link($cabinet['Cabinet']['name'], NetCommonsUrl::backToIndexUrl());
}else{
	echo h($cabinet['Cabinet']['name']);
}
?>
<?php
?>
<span ng-repeat="cabinetFile in folderPath"> &gt; <a href="{{cabinetFile.url}}">{{cabinetFile.CabinetFile.filename}}</a></span>
<?php
//foreach($_tmpFolderPath as $folder){
//	echo ' &gt; ';
//	echo $this->NetCommonsHtml->link($folder['CabinetFile']['filename'], ['key' => $folder['CabinetFile']['key']]);
//}
echo ' &gt; ';
echo h($currentFile['CabinetFile']['filename']);
?>
</span>
