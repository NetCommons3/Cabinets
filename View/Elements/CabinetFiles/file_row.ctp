<?php // File ?>
<td>
	<?php
	$icon = '<span class="glyphicon glyphicon-file text-primary cabinets__file-list-icon" aria-hidden="true"></span>';
	echo $icon;
	if (isset($cabinetFile['AuthorizationKey'])) {
		// 認証キー必要
		echo $this->NetCommonsHtml->link(
			h($cabinetFile['CabinetFile']['filename']),
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
				'popup-placeholder' => __d('authorization_keys', 'Please input authorization key')
			]
		);
	} else {
		// 認証キー不要
		echo $this->NetCommonsHtml->link(
			h($cabinetFile['CabinetFile']['filename']),
			[
				'action' => 'download',
				'key' => $cabinetFile['CabinetFile']['key']
			]
		);
	}
	?>
	<?php echo $this->Workflow->label(
		$cabinetFile['CabinetFile']['status']
	); ?>
	<?php if (isset($cabinetFile['AuthorizationKey'])): ?>
		<span class="glyphicon glyphicon-lock" aria-hidden="true"
			title="<?php echo __d(
				'cabinets',
				'Password is required to download.'
			) ?>"></span>
	<?php endif ?>
	<span class="badge ">
									<?php
									echo $cabinetFile['UploadFile']['file']['total_download_count'];
									?>
								</span>

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
<td class="hidden-sm hidden-xs text-right"><?php echo
	$this->Number->toReadableSize(
		$cabinetFile['UploadFile']['file']['size']
	) ?></td>
<td class="text-right"><?php echo $this->Date->dateFormat(
		$cabinetFile['CabinetFile']['modified']
	) ?></td>

<td class="text-right  cabinets__index__button">
	<?php echo $this->element('Cabinets.CabinetFiles/file_dropdown', ['cabinetFile' => $cabinetFile]); ?>
</td>
