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
<?php
echo $this->Html->script(
	'/AuthorizationKeys/js/authorization_keys.js',
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
	</header>
	<article>
		<dl class="cabinets__detail">
			<dt><?php echo __d('cabinets', 'File name'); ?></dt>
			<dd class="form-control nc-data-label">
				<?php echo h($cabinetFile['CabinetFile']['filename']); ?>
				<?php echo $this->Workflow->label($cabinetFile['CabinetFile']['status']); ?>
			</dd>

			<dt><?php echo __d('cabinets', 'Path'); ?></dt>
			<dd class="form-control nc-data-label"><?php echo $this->element('file_path'); ?></dd>

			<dt><?php echo __d('cabinets', 'Size'); ?></dt>
			<dd class="form-control nc-data-label"><?php echo $this->Number->toReadableSize(
					$cabinetFile['UploadFile']['file']['size']
				); ?></dd>

			<dt><?php echo __d('cabinets', 'Download count'); ?></dt>
			<dd class="form-control nc-data-label"><?php echo $cabinetFile['UploadFile']['file']['total_download_count']; ?></dd>

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
	</article>

	<footer class="text-center">
		<?php
		if (isset($cabinetFile['AuthorizationKey'])) {
			// 認証キー必要
			echo $this->NetCommonsHtml->link(
				__d('cabinets', 'Download'),
				'#',
				[
					'authorization-keys-popup-link',
					'url' => NetCommonsUrl::actionUrl(
						[
							'action' => 'download',
							'key' => $cabinetFile['CabinetFile']['key'],
							'block_id' => Current::read('Block.id'),
							'frame_id' => Current::read('Frame.id')
						]
					),
					'popup-title' => __d('authorization_keys', 'Authorization key confirm dialog'),
					'popup-label' => __d('authorization_keys', 'Authorization key'),
					'popup-placeholder' =>
						__d('authorization_keys', 'Please input authorization key'),
					'class' => 'btn btn-primary'
				]
			);
		} else {
			// 認証キー不要
			echo $this->NetCommonsHtml->link(
				__d('cabinets', 'Download'),
				[
					'action' => 'download',
					'key' => $cabinetFile['CabinetFile']['key']
				],
				['class' => 'btn btn-primary']
			);
		}

		//echo $this->NetCommonsHtml->link(
		//	__d('cabinets', 'Download'),
		//	['action' => 'download', 'key' => $cabinetFile['CabinetFile']['key']],
		//	['class' => 'btn btn-primary']
		//)
		?>
	</footer>
</div>
