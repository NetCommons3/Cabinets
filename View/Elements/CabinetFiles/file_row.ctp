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
	<div class="dropdown">
		<button class="btn btn-default dropdown-toggle btn-xs"
			type="button"
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
						'action' => 'view',
						'key' => $cabinetFile['CabinetFile']['key']
					]
				); ?>
			</li>
			<?php if ($this->Workflow->canEdit('Cabinets.CabinetFile', $cabinetFile)) : ?>
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
					$tokens = $this->Token->getToken(
						'CabinetFileTree',
						NetCommonsUrl::actionUrl(array(
							'plugin' => 'cabinets',
							'controller' => 'cabinet_files_edit',
							'action' => 'move',
							'block_id' => Current::read('Block.id'),
							'key' => $cabinetFile['CabinetFile']['key'],
							'frame_id' => Current::read('Frame.id')
						)),
						$tokenFields,
						$hiddenFields
					);
					$data += $tokens;
				?>
				<a href="#"
					ng-click="moveFile('<?php echo $cabinetFile['CabinetFile']['key'] ?>', false,
					 <?php echo h(json_encode($data))?>
					)"><?php echo __d(
						'net_commons',
						'Move'
					); ?></a>
				<?php
				?>
			</li>
			<li>
				<?php echo $this->NetCommonsHtml->link(
					__d('net_commons', 'Edit'),
					[
						'controller' => 'cabinet_files_edit',
						'action' => 'edit',
						'key' => $cabinetFile['CabinetFile']['key']
					]
				); ?>
			</li>
			<?php endif ?>

			<?php if (Current::permission('content_publishable')): ?>

				<?php if ($cabinetFile['UploadFile']['file']['extension'] !== 'zip'):?>
					<?php
					$unzipDisabled = 'class="disabled"';
					$ngClick = '';
					?>
				<?php else : ?>
					<?php
					$unzipDisabled = '';
					$data = [
						'Frame' => [
							'id' => Current::read('Frame.id'),
						],
						'Block' => [
							'id' => Current::read('Block.id')
						]
					];
					$tokenFields = Hash::flatten($data);
					//$hiddenFields = $tokenFields;
					//unset($hiddenFields['LikesUser.is_liked']);
					$hiddenFields = [
						'Frame.id',
						'Block.id'
					];

					$this->request->data = Hash::merge($this->request->data, $data);
					$this->request->data = $data;
					//$tokens = $this->Token->getToken('CabinetFileTree', '/cabinets/cabinet_files_edit/move',
					$tokens = $this->Token->getToken(
						'CabinetFile',
						NetCommonsUrl::actionUrl(array(
							'plugin' => 'cabinets',
							'controller' => 'cabinet_files_edit',
							'action' => 'unzip',
							'block_id' => Current::read('Block.id'),
							'key' => $cabinetFile['CabinetFile']['key'],
							'frame_id' => Current::read('Frame.id')
						)),
						$tokenFields,
						$hiddenFields
					);
					$data += $tokens;

					$ngClick = sprintf(
						'ng-click="unzip(\'%s\', %s)"',
						$cabinetFile['CabinetFile']['key'],
						h(json_encode($data))
					);
					?>
				<?php endif ?>

				<li <?php echo $unzipDisabled ?>>
					<a href="#" <?php echo $ngClick ?> ><?php echo __d(
							'cabinets',
							'Unzip'
						); ?></a>
				</li>
			<?php endif ?>
		</ul>
	</div>

</td>
