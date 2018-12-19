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
			<?php if ($this->CabinetFile->isAllowUnzip($cabinetFile)):?>
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
			<?php else : ?>
				<?php
				$unzipDisabled = 'class="disabled"';
				$ngClick = '';
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