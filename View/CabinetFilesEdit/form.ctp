<?php
echo $this->NetCommonsHtml->script(
	'/cabinets/js/cabinets.js'
);
?>

<div ng-controller="Cabinets" ng-init="init(
	 <?php echo Current::read('Block.id') ?>,
	 <?php echo Current::read('Frame.id') ?>
	 )">
	<div class="cabinetFiles form" ng-controller="CabinetFile.edit" ng-init="init(
	 <?php echo Hash::get($this->request->data, 'CabinetFileTree.parent_id', 0); ?>,
	 '<?php echo Hash::get($this->request->data, 'CabinetFile.key', 0); ?>'
	 )"
		id="cabinetFileForm_<?php echo Current::read('Frame.id') ?>"
	>
		<article>
			<h1><?php echo h($cabinet['Cabinet']['name']) ?></h1>
			<div class="panel panel-default">

				<?php echo $this->NetCommonsForm->create(
					'CabinetFile',
					array(
						'inputDefaults' => array(
							'div' => 'form-group',
							'class' => 'form-control',
							'error' => false,
						),
						'div' => 'form-control',
						'novalidate' => true,
						'type' => 'file',
					)
				);
				?>
				<?php echo $this->NetCommonsForm->hidden('Frame.id', array(
					'value' => Current::read('Frame.id'),
				)); ?>
				<?php echo $this->NetCommonsForm->hidden('Block.id', array(
					'value' => Current::read('Block.id'),
				)); ?>

				<?php echo $this->NetCommonsForm->input('key', array('type' => 'hidden')); ?>
				<?php echo $this->NetCommonsForm->input('is_folder', array('type' => 'hidden')); ?>

				<div class="panel-body">

					<fieldset>

						<div class="form-group">
							<?php echo $this->NetCommonsForm->label(
								'filename',
								__d(
									'cabinets',
									'File name'
								),
								['required' => true]
							); ?>
							<?php
							echo $this->NetCommonsForm->input(
								//'filename',
								'withOutExtFileName',
								array(
									//'label' => __d('cabinets', 'ファイル名'),
									'label' => false,
									'required' => 'required',
									'after' => ' .' .
										//$this->request->data['UploadFile']['file']['extension'],
										$this->request->data['CabinetFile']['extension'],
									'div' => 'form-inline'
								)
							);
							?>

						</div>
						<?php echo $this->NetCommonsForm->uploadFile(
							'file',
							[
								'label' => __d('cabinets', 'File'),
								'remove' => false,
								'filename' => false
							]
						) ?>

						<div class="form-group">
							<?php echo $this->NetCommonsForm->checkbox(
								'CabinetFile.use_auth_key',
								[
									'ng-model' => 'use_auth_key',
									'label' => __d('cabinets', 'Set download password.'),
								]
							); ?>
							<!--<input type="checkbox" ng-model="use_auth_key" name="data[use_auth_key]" value="1" />--><?php //echo __d('cabinets', 'Set download password.');?>
							<div ng-show="use_auth_key"
								<?php
								if (isset($this->request->data['AuthorizationKey'])) {
									echo 'ng-init="use_auth_key=true"';
								}
								?>
							>
								<?php echo $this->element(
									'AuthorizationKeys.edit_form',
									[
										'options' => [
											'label' => __d('cabinets', 'Password')
										],
									]
								) ?>
							</div>
						</div>

						<div class="form-group">
							<?php echo $this->NetCommonsForm->label(
								'parent_id',
								__d('cabinets', 'Path')
							); ?>
							<div>
								<?php echo $this->element('Cabinets.file_path'); ?>

								<a href="#" class="btn btn-default"
									ng-click="showFolderTree()"><span
										class="glyphicon glyphicon-move"
										aria-hidden="true"></span><?php echo __d(
										'net_commons',
										'Move'
									); ?></a>

								<?php
								$this->NetCommonsForm->unlockField('CabinetFileTree.parent_id');
								echo $this->NetCommonsForm->input(
									'CabinetFileTree.parent_id',
									['type' => 'hidden', 'ng-value' => 'parent_id']
								); ?>
							</div>
						</div>
						<?php
						echo $this->NetCommonsForm->input(
							'description',
							array(
								'label' => __d('cabinets', 'Description'),
							)
						);
						?>

					</fieldset>

					<hr/>
					<?php echo $this->Workflow->inputComment('CabinetFile.status'); ?>

				</div>

				<?php echo $this->Workflow->buttons('CabinetFile.status'); ?>

				<?php echo $this->NetCommonsForm->end() ?>

				<?php if ($isEdit && $isDeletable) : ?>
					<div class="panel-footer" style="text-align: right;">
						<?php echo $this->NetCommonsForm->create(
							'CabinetFile',
							array(
								'type' => 'delete',
								'url' => NetCommonsUrl::blockUrl(
									array(
										'controller' => 'cabinet_files_edit',
										'action' => 'delete',
										'frame_id' => Current::read('Frame.id')
									)
								)
							)
						) ?>
						<?php echo $this->NetCommonsForm->input(
							'key',
							array('type' => 'hidden')
						); ?>

						<?php echo $this->Button->delete('', __d('net_commons', 'Deleting the %s. Are you sure to proceed?', __d('cabinets', 'File')));?>

						<?php echo $this->NetCommonsForm->end() ?>
					</div>
				<?php endif ?>

			</div>
		</article>

	</div>

</div>
