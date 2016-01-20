<?php
$options = [
	'type' => 'get'
];
echo $this->NetCommonsForm->create(false, $options);
echo $this->NetCommonsForm->submit('ダウンロード');
echo $this->NetCommonsForm->end();