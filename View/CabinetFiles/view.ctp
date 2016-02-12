<?php
echo $this->Html->script(
	'/cabinets/js/cabinets.js',
	array(
		'plugin' => false,
		'once' => true,
		'inline' => false
	)
);
?>
<div ng-controller="Cabinets" ng-init="init(
	 <?php echo Current::read('Block.id') ?>,
	 <?php echo Current::read('Frame.id') ?>
	 )">

	<?php if ($this->Workflow->canEdit('CabinetFile', $cabinetFile)) : ?>
<div class="text-right">
	<?php echo $this->Button->editLink('',
		array(
			'controller' => 'cabinet_files_edit',
			'action' => 'edit',
			'key' => $cabinetFile['CabinetFile']['key']
		),
		array(
			'tooltip' => true,
			'iconSize' => 'btn-xs'
		)
	); ?>
</div>
<?php endif ?>
<dl class="dl-horizontal">
	<dt><?php echo __d('cabinets', 'ファイル名'); ?></dt>
	<dd><?php echo $cabinetFile['CabinetFile']['filename']; ?></dd>

	<dt><?php echo __d('cabinets', 'パス'); ?></dt>
	<dd><?php echo $this->element('file_path'); ?></dd>

	<dt><?php echo __d('cabinets', 'サイズ'); ?></dt>
	<dd><?php echo $this->Number->toReadableSize($cabinetFile['UploadFile']['file']['size']); ?></dd>

	<dt><?php echo __d('cabinets', 'ダウンロードファイル名'); ?></dt>
	<dd><?php echo $cabinetFile['UploadFile']['file']['original_name']; ?></dd>


	<dt><?php echo __d('cabinets', 'ダウンロード回数'); ?></dt>
	<dd><?php echo $cabinetFile['UploadFile']['file']['download_count']; ?></dd>

	<dt><?php echo __d('cabinets', '説明'); ?></dt>
	<dd><?php echo h($cabinetFile['CabinetFile']['description']); ?></dd>

	<dt><?php echo __d('cabinets', '作成者'); ?></dt>
	<dd><?php echo $this->DisplayUser->handleLink($cabinetFile, array('avatar' => true)); ?></dd>

	<dt><?php echo __d('cabinets', '作成日時'); ?></dt>
	<dd><?php echo $this->Date->dateFormat($cabinetFile['CabinetFile']['created']); ?></dd>

	<dt><?php echo __d('cabinets', '更新者'); ?></dt>
	<dd><?php echo $this->DisplayUser->handleLink($cabinetFile, array('avatar' => true), [], 'TrackableUpdater'); ?></dd>

	<dt><?php echo __d('cabinets', '更新日時'); ?></dt>
	<dd><?php echo $this->Date->dateFormat($cabinetFile['CabinetFile']['modified']); ?></dd>
</dl>

<div class="text-center">
	<?php
	//  ひとつ上の一覧へ戻す
	if (count($folderPath) > 1) {
		// 上の階層はフォルダ
		$parentFolder = $folderPath[count($folderPath) - 2];
		$url = [
			'action' => 'index',
			'key' => $parentFolder['CabinetFile']['key']
		];

	}else{
		// 上の階層はキャビネット
		$url = [
			'action' => 'index'
		];
	}
	echo $this->Html->link(
		__d('cabinets', '一覧へ戻る'),
		$this->NetCommonsHtml->url($url),
		['class' => 'btn btn-default']
	)
	?>
	<?php
	echo $this->Html->link(
		__d('cabinets', 'ダウンロード'),
		$this->NetCommonsHtml->url(['action' => 'download', 'key' => $cabinetFile['CabinetFile']['key']]),
		['class' => 'btn btn-primary']
	)
	?>
</div>
	</div>
