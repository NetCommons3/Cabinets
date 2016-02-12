<?php echo $this->Html->script(
	'/cabinets/js/cabinet_file_edit.js',
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
	<div class="cabinetFiles form" ng-controller="CabinetFile.edit" ng-init="init(
	 <?php echo Hash::get($this->request->data, 'CabinetFileTree.parent_id', 0); ?>
	 )"
		 id="cabinetFileForm_<?php echo Current::read('Frame.id')?>"
	>
		<article>
			<h1><?php echo $cabinet['Cabinet']['name'] ?></h1>
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
				<?php echo $this->NetCommonsForm->input('key', array('type' => 'hidden')); ?>
				<?php echo $this->NetCommonsForm->input('is_folder', array('type' => 'hidden')); ?>

				<div class="panel-body">

					<fieldset>

						<?php
						echo $this->NetCommonsForm->input(
							'filename',
							array(
								'label' => __d('cabinets', 'ファイル名'),
								'required' => 'required',
							)
						);
						?>
						<?php  echo $this->NetCommonsForm->uploadFile('file', ['label' => __d('cabinets', 'ファイル'), 'remove' => false])?>

						<div class="form-group">
						<input type="checkbox" ng-model="use_auth_key"/><?php echo __d('cabinets', 'ダウンロードパスワードを設定する');?>
						<div ng-show="use_auth_key">
							<?php echo $this->element('AuthorizationKeys.edit_form', ['options' => [
								'label' => __d('cabinets', 'パスワード')],
							]) ?>
						</div>
						</div>

						<div class="form-group">
							<?php echo $this->NetCommonsForm->label('parent_id', __d('cabinets', 'パス')); ?>
							<div>
								<?php echo $this->element('file_path'); ?>

								<a href="#" class="btn btn-default" ng-click="showFolderTree()"><span class="glyphicon glyphicon-move" aria-hidden="true"></span><?php echo __d(
										'cabinets',
										'移動'
									); ?></a>

								<?php
								$this->NetCommonsForm->unlockField('CabinetFileTree.parent_id');
								echo $this->NetCommonsForm->input('CabinetFileTree.parent_id', ['type' => 'hidden', 'ng-value' => 'parent_id']); ?>
							</div>
						</div>
						<?php
						echo $this->NetCommonsForm->input(
							'description',
							array(
								'label' => __d('cabinets', '説明'),
								'required' => 'required',
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
					<div  class="panel-footer" style="text-align: right;">
						<?php echo $this->NetCommonsForm->create('CabinetFile',
							array(
								'type' => 'delete',
								'url' => $this->NetCommonsHtml->url(
									array('controller' => 'cabinet_files_edit', 'action' => 'delete', 'frame_id' => Current::read('Frame.id')))
							)
						) ?>
						<?php echo $this->NetCommonsForm->input('key', array('type' => 'hidden')); ?>

						<span class="nc-tooltip" tooltip="<?php echo __d('net_commons', 'Delete'); ?>">
						<button class="btn btn-danger" onClick="return confirm('<?php echo __d('net_commons', 'Deleting the %s. Are you sure to proceed?', __d('cabinets', 'CabinetFile')) ?>')"><span class="glyphicon glyphicon-trash"> </span></button>


					</span>


						<?php echo $this->NetCommonsForm->end() ?>
					</div>
				<?php endif ?>

			</div>
		</article>

	</div>

</div>
