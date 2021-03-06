<?php
echo $this->Html->css(
	'/cabinets/css/cabinets.css',
	array(
		'plugin' => false,
		'once' => true,
		'inline' => false
	)
); ?>
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

	<header class="clearfix">
		<div class="pull-left">
			<?php
			//  ひとつ上の一覧へ戻す
			if (count($folderPath) > 2) {
				// 上の階層はフォルダ
				$parentFolder = $folderPath[count($folderPath) - 2];
				$url = [
					'action' => 'index',
					'key' => $parentFolder['CabinetFile']['key']
				];

			} else {
				// 上の階層はキャビネット
				$url = NetCommonsUrl::backToPageUrl();
			}
			?>
			<?php echo $this->LinkButton->toList(__d('cabinets', 'Go to List'), $url); ?>
		</div>

		<?php if ($this->Workflow->canEdit('CabinetFile', $cabinetFile)) : ?>
			<div class="pull-right">
				<?php
				if (Current::permission('content_editable')) {
					echo $this->Button->editLink(
						'',
						array(
							'controller' => 'cabinet_files_edit',
							'action' => 'edit_folder',
							'key' => $cabinetFile['CabinetFile']['key']
						),
						array(
							'tooltip' => true,
						)
					);
				}
				?>
			</div>
		<?php endif ?>
	</header>

	<dl class="cabinets__detail">
		<dt><?php echo __d('cabinets', 'Folder name'); ?></dt>
		<dd class="form-control nc-data-label"><?php echo $cabinetFile['CabinetFile']['filename']; ?></dd>

		<dt><?php echo __d('cabinets', 'Path'); ?></dt>
		<dd class="form-control nc-data-label"><?php echo $this->element('Cabinets.file_path'); ?></dd>

		<dt><?php echo __d('cabinets', 'Total size'); ?></dt>
		<dd class="form-control nc-data-label"><?php echo $this->Number->toReadableSize(
				$cabinetFile['CabinetFile']['size']
			); ?></dd>

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
		<?php if ($cabinetFile['CabinetFile']['has_children'] === false):?>
			<?php
			echo $this->NetCommonsHtml->link(
				__d('cabinets', 'Download'),
				'#',
				['class' => 'btn btn-primary disabled']
			)
			?>
		<?php else: ?>
			<?php
			echo $this->CabinetFile->zipDownload(
				$cabinetFile,
				__d('cabinets', 'Download'),
				['class' => 'btn btn-primary']
			);
			?>
		<?php endif ?>
	</div>
</div>
