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
<?php $this->start('title_for_modal'); ?>
<?php echo $cabinet['Cabinet']['name'] ?>
<?php $this->end(); ?>

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
			<div>
				<?php echo $this->element('file_path'); ?>
			</div>
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

						<?php  echo $this->NetCommonsForm->uploadFile('file', ['label' => __d('cabinets', 'File'), 'remove' => false])?>

						<div class="form-group">
						<input type="checkbox" ng-model="use_auth_key"/><?php echo __d('cabinets', 'Set download password.');?>
						<div ng-show="use_auth_key">
							<?php echo $this->element('AuthorizationKeys.edit_form', ['options' => [
								'label' => __d('cabinets', 'Password')],
							]) ?>
						</div>
						</div>
						<?php echo $this->NetCommonsForm->hidden('CabinetFileTree.parent_id');?>

					</fieldset>
				</div>


					<?php echo $this->Workflow->buttons('CabinetFile.status'); ?>

				<?php echo $this->NetCommonsForm->end() ?>
			</div>
		</article>

	</div>

</div>
