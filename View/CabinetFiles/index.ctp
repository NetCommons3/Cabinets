<?php echo $this->element('NetCommons.javascript_alert'); ?>
<div ng-controller="Cabinets"
		ng-init="init(<?php echo Current::read('Block.id') . ', ' . Current::read('Frame.id'); ?>)"
		class="nc-content-list">

	<?php echo $this->NetCommonsHtml->css('/cabinets/css/cabinets.css'); ?>
	<?php echo $this->NetCommonsHtml->script('/cabinets/js/cabinets.js'); ?>
	<?php echo $this->NetCommonsHtml->script('/AuthorizationKeys/js/authorization_keys.js'); ?>
	<?php echo $this->NetCommonsHtml->scriptStart(array('inline' => false)); ?>
		$(function() {
			$('.cabinets__index__description').popover({html: true})
		})
		// popover外クリックでpopoverを閉じる
		$('body').on('click', function(e) {
			$('[data-toggle="popover"]').each(function() {
				//the 'is' for buttons that trigger popups
				//the 'has' for icons within a button that triggers a popup
				if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
					$(this).popover('hide');
				}
			});
		});
	<?php echo $this->Html->scriptEnd(); ?>

	<?php echo $this->NetCommonsHtml->blockTitle($cabinet['Cabinet']['name'], null, ['class' => 'cabinets_cabinetTitle']); ?>

	<div class="clearfix">
		<div class="pull-left cabinets__index__file-path">
			<?php echo $this->element('file_path', ['currentFile' => $currentFolder]); ?>
		</div>

		<div class="pull-right">
			<?php if (Current::permission('content_creatable')) : ?>
				<div class="pull-right" style="margin-left: 10px;">
					<?php
					$parentId = $folderPath[count($folderPath) - 1]['CabinetFileTree']['id'];

					$addUrl = array(
						'controller' => 'cabinet_files_edit',
						'action' => 'add_folder',
						'frame_id' => Current::read('Frame.id'),
						'parent_id' => $parentId,
					);
					if (Current::permission('content_publishable')) {
						echo $this->Button->addLink(
							__d('cabinets', 'Add Folder'),
							$addUrl,
							array(
								'escapeTitle' => false,
								'escape' => false,
								'addIcon' => 'glyphicon-folder-close'
							)
						);
					}
					?>
				</div>
			<?php endif ?>
			<?php if (Current::permission('content_creatable')) : ?>
				<div class="pull-right" ng-controller="CabinetFile.addFile"
					ng-init="init(<?php echo $parentId ?>)">
					<?php
					$addUrl = array(
						'controller' => 'cabinet_files_edit',
						'action' => 'add',
						'frame_id' => Current::read('Frame.id'),
						'parent_id' => $parentId,
					);
					echo $this->Button->addLink(
						__d('cabinets', 'Add File'),
						'#',
						array(
							'ng-click' => 'addFile()',
							'escapeTitle' => false,
							'escape' => false,
							'addIcon' => 'glyphicon-file',
						)
					);
					?>
				</div>
			<?php endif ?>
		</div>
	</div>


	<div class="row">
		<?php // ============ フォルダツリー ============?>
		<?php
		$is3columnLayout = $this->PageLayout->hasContainer(Container::TYPE_MAJOR) && $this->PageLayout->hasContainer(Container::TYPE_MINOR);
		$listCol = $is3columnLayout ? 'col-md-12' : 'col-md-9';
		?>
		<?php if (! $is3columnLayout): ?>
		<div class="col-md-3 hidden-sm hidden-xs cabinets-folder-tree inline"
			ng-controller="Cabinets.FolderTree as foldertree">
			<div resize="foldertree.resizeHandler()">
				<?php echo $this->element('CabinetFiles/folder_tree'); ?>
			</div>
		</div>
		<?php endif ?>
		<div class="<?php echo $listCol?>">
			<?php
				$currentDirUrl = NetCommonsUrl::actionUrlAsArray(array(
					'block_id' => Current::read('Block.id'),
					'key' => $currentFolder['CabinetFile']['key'],
					'frame_id' => Current::read('Frame.id'),
				));
			?>

			<table
				class="table table-hover cabinets__index__file-list"
				style="table-layout: fixed"
				ng-controller="CabinetFile.index"
				ng-init="init(<?php echo $currentTreeId; ?>)">
				<thead>
				<tr>
					<th class="cabinets__index__name">
						<?php echo $this->Paginator->sort(
							'filename', __d('cabinets', 'Filename'), ['direction' => 'desc', 'url' => $currentDirUrl]
						); ?>
					</th>
					<th class="cabinets__index__size hidden-sm hidden-xs">
						<?php echo $this->Paginator->sort('size', __d('cabinets', 'Size'), ['url' => $currentDirUrl]); ?>
					</th>
					<th class="cabinets__index__modified">
						<?php echo $this->Paginator->sort(
							'modified', __d('net_commons', 'Modified datetime'), ['url' => $currentDirUrl]
						); ?>
					</th>
					<th class="cabinets__index__button"></th>
				</tr>
				</thead>

				<tbody>
				<?php if ($parentUrl): ?>
					<tr>
						<td>
							<?php
							echo $this->NetCommonsHtml->link(
								'<span class="glyphicon glyphicon-circle-arrow-up" aria-hidden="true"></span>' . __d(
									'cabinets',
									'Parent folder'
								),
								$parentUrl,
								['escape' => false]
							);
							?>
						</td>
						<td class="hidden-sm hidden-xs"></td>
						<!--<td></td>-->
						<td colspan="2" style="text-align: right; ">
							<?php
							if (count($cabinetFiles) > 0) {
								echo $this->NetCommonsHtml->link(
									__d('cabinets', 'Zip download'),
									[
										'action' => 'download_folder',
										'key' => $currentFolder['CabinetFile']['key']
									],
									['class' => 'btn btn-xs btn-default',
										'style' => 'margin-left:0px;'
									]
								);

							}
							?>
						</td>
					</tr>
				<?php endif ?>

				<?php if (count($cabinetFiles) == 0): ?>
					<tr>
						<td colspan="4">
							<?php echo __d(
								'net_commons',
								'%s is not.',
								__d('cabinets', 'File/Folder')
							); ?>
						</td>
					</tr>
				<?php endif ?>

				<?php foreach ($cabinetFiles as $cabinetFile): ?>
					<tr ng-hide="moved['<?php echo $cabinetFile['CabinetFile']['key'] ?>']"
						class="cabinet-file">
						<?php if ($cabinetFile['CabinetFile']['is_folder']) : ?>
							<?php echo $this->element('CabinetFiles/folder_row',
								['cabinetFile' => $cabinetFile]); ?>
						<?php else: ?>
							<?php echo $this->element('CabinetFiles/file_row',
								['cabinetFile' => $cabinetFile]); ?>
						<?php endif ?>
					</tr>
				<?php endforeach ?>

				</tbody>
			</table>

		</div>
	</div>

</div>
