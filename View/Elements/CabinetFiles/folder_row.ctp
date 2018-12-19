<td>

	<?php
	$icon = '<span class="glyphicon glyphicon-folder-close cabinets__file-list-icon" aria-hidden="true"></span>';
	echo $icon;
	echo $this->NetCommonsHtml->link(
		h($cabinetFile['CabinetFile']['filename']),
		[
				'action' => 'index',
				'key' => $cabinetFile['CabinetFile']['key']
		],
		['escape' => false]
	);
	?>
	<div class="cabinets__index__description text-muted small"
		data-content="<?php echo nl2br(
			h($cabinetFile['CabinetFile']['description'])
		); ?>"
		data-toggle="popover"
		data-placement="bottom"
	>
		<?php echo h($cabinetFile['CabinetFile']['description']); ?>
	</div>


</td>
<td class="cabinets__index__size  hidden-sm hidden-xs text-right"><?php echo $this->Number->toReadableSize(
		$cabinetFile['CabinetFile']['size']
	) ?></td>
<td class="text-right">
	<?php echo $this->Date->dateFormat(
		$cabinetFile['CabinetFile']['modified']
	) ?></td>

<td class="text-right cabinets__index__button">
	<?php
	// link folder_detail
	$detailUrl = $this->NetCommonsHtml->url(
		[
			'action' => 'folder_detail',
			'key' => $cabinetFile['CabinetFile']['key']
		]
	);
	?>

	<div class="dropdown">
		<button class="btn btn-default dropdown-toggle btn-xs" type="button"
			id="cabinets__file-<?php echo $cabinetFile['CabinetFile']['id'] ?>"
			data-toggle="dropdown" aria-haspopup="true"
			aria-expanded="true">
			<span class="glyphicon glyphicon-option-vertical" aria-hidden="true"></span>
		</button>

		<ul class="dropdown-menu dropdown-menu-right"
			aria-labelledby="cabinets__file-<?php echo $cabinetFile['CabinetFile']['id'] ?>">
			<li>
				<?php echo $this->NetCommonsHtml->link(
					__d('cabinets', 'View'),
					[
						'controller' => 'cabinet_files',
						'action' => 'folder_detail',
						'key' => $cabinetFile['CabinetFile']['key']
					]
				); ?>
			</li>
			<?php // フォルダ移動・編集は公開権限必要 ?>
			<?php if (Current::permission('content_publishable')) :?>
			<li>
				<?php
				$data = [
					'CabinetFileTree' => [
						'parent_id' => $cabinetFile['CabinetFileTree']['parent_id'],
					],
					'Frame' => [
						'id' => Current::read('Frame.id'),
					]
				];
				$tokenFields = Hash::flatten($data);
				//$hiddenFields = $tokenFields;
				//unset($hiddenFields['LikesUser.is_liked']);
				$hiddenFields = [
					'Frame.id'
				];

				$this->request->data = Hash::merge($this->request->data, $data);
				$this->request->data = $data;
				$this->Token->unlockField('CabinetFileTree.parent_id');
				//$tokens = $this->Token->getToken('CabinetFileTree', '/cabinets/cabinet_files_edit/move',
				$tokens = $this->Token->getToken('CabinetFileTree', '/cabinets/cabinet_files_edit/move/' . Current::read('Block.id') . '/' . $cabinetFile['CabinetFile']['key'] . '?frame_id=' . Current::read('Frame.id'),
					$tokenFields, $hiddenFields);
				$data += $tokens;

				?>

				<a href="#"
					ng-click="moveFile('<?php echo $cabinetFile['CabinetFile']['key'] ?>', true,
					<?php echo h(json_encode($data)) ?>
					)"><?php echo __d(
						'net_commons',
						'Move'
					); ?></a>
			</li>
			<li>
				<?php echo $this->NetCommonsHtml->link(
					__d('net_commons', 'Edit'),
					[
						'controller' => 'cabinet_files_edit',
						'action' => 'edit_folder',
						'key' => $cabinetFile['CabinetFile']['key']
					]
				); ?>
			</li>
			<?php endif ?>
			<?php if ($cabinetFile['CabinetFile']['has_children'] === false):?>
				<li class="disabled">
					<a href="#"><?php echo __d('cabinets', 'Zip download'); ?></a>
				</li>
			<?php else: ?>
				<li>
					<?php
					echo $this->NetCommonsHtml->link(
						__d('cabinets', 'Zip download'),
						[
							'action' => 'download_folder',
							'key' => $cabinetFile['CabinetFile']['key']
						]
					);
					?>
				</li>
			<?php endif ?>
		</ul>
	</div>

</td>