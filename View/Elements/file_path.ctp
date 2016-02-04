<?php
// パンクズ
if($folderPath){
	echo $this->NetCommonsHtml->link($cabinet['Cabinet']['name'], NetCommonsUrl::backToIndexUrl());
}else{
	echo h($cabinet['Cabinet']['name']);
}
?>
<?php foreach($folderPath as $index => $folder){
	echo ' &gt; ';
	if($index == count($folderPath) -1){
		// カレント位置はリンクなし
		echo h($folder['CabinetFile']['filename']);
	}else{
		echo $this->NetCommonsHtml->link($folder['CabinetFile']['filename'], ['key' => $folder['CabinetFile']['key']]);

	}

}

