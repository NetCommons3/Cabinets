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

	<div class="clearfix">
		<div class="pull-left">
			<?php echo $this->Workflow->label($cabinetFile['CabinetFile']['status']); ?>
		</div>
		<?php if ($this->Workflow->canEdit('CabinetFile', $cabinetFile)) : ?>
			<div class="pull-right">
				<?php echo $this->Button->editLink(
					'',
					array(
						'controller' => 'cabinet_files_edit',
						'action' => 'edit',
						'key' => $cabinetFile['CabinetFile']['key']
					),
					array(
						'tooltip' => true,
					)
				); ?>
			</div>
		<?php endif ?>

	</div>
	<dl class="cabinets__detail">
		<dt><?php echo __d('cabinets', 'File name'); ?></dt>
		<dd class="form-control nc-data-label">
			<?php echo $cabinetFile['CabinetFile']['filename']; ?>
		</dd>

		<dt><?php echo __d('cabinets', 'Path'); ?></dt>
		<dd class="form-control nc-data-label"><?php echo $this->element('file_path'); ?></dd>

		<dt><?php echo __d('cabinets', 'Size'); ?></dt>
		<dd class="form-control nc-data-label"><?php echo $this->Number->toReadableSize(
				$cabinetFile['UploadFile']['file']['size']
			); ?></dd>

		<dt><?php echo __d('cabinets', 'Download count'); ?></dt>
		<dd class="form-control nc-data-label"><?php echo $cabinetFile['UploadFile']['file']['download_count']; ?></dd>

		<dt><?php echo __d('cabinets', 'Description'); ?></dt>
		<dd class="form-control nc-data-label"><?php echo h(
				$cabinetFile['CabinetFile']['description']
			); ?></dd>

		<dt><?php echo __d('net_commons', 'Created user'); ?></dt>
		<dd class="form-control nc-data-label"><?php echo $this->DisplayUser->handleLink(
				$cabinetFile,
				array('avatar' => true)
			); ?></dd>

		<dt><?php echo __d('net_commons', 'Created datetime'); ?></dt>
		<dd class="form-control nc-data-label"><?php echo $this->Date->dateFormat(
				$cabinetFile['CabinetFile']['created']
			); ?></dd>

		<dt><?php echo __d('net_commons', 'Modified user'); ?></dt>
		<dd class="form-control nc-data-label"><?php echo $this->DisplayUser->handleLink(
				$cabinetFile,
				array('avatar' => true),
				[],
				'TrackableUpdater'
			); ?></dd>

		<dt><?php echo __d('net_commons', 'Modified datetime'); ?></dt>
		<dd class="form-control nc-data-label"><?php echo $this->Date->dateFormat(
				$cabinetFile['CabinetFile']['modified']
			); ?></dd>
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

		} else {
			// 上の階層はキャビネット
			$url = [
				'action' => 'index'
			];
		}

		//echo $this->BackTo->linkButton(
		//	__d('net_commons', 'Go to List'),
		//	$this->NetCommonsHtml->url($url),
		//	['class' => 'btn btn-default']
		//);

		echo $this->Html->link(
			__d('cabinets', 'Go to List'),
			$this->NetCommonsHtml->url($url),
			['class' => 'btn btn-default']
		)
		?>
		<?php
		echo $this->Html->link(
			__d('cabinets', 'Download'),
			$this->NetCommonsHtml->url(
				['action' => 'download', 'key' => $cabinetFile['CabinetFile']['key']]
			),
			['class' => 'btn btn-primary']
		)
		?>
	</div>
</div>
