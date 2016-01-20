<?php
/**
 * CabinetSettings edit template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->element('Blocks.form_hidden'); ?>

<?php echo $this->Form->hidden('Cabinet.id'); ?>
<?php echo $this->Form->hidden('Cabinet.key'); ?>
<?php echo $this->Form->hidden('CabinetSetting.id'); ?>
<?php echo $this->Form->hidden('CabinetFrameSetting.id'); ?>
<?php echo $this->Form->hidden('CabinetFrameSetting.frame_key'); ?>
<?php echo $this->Form->hidden('CabinetFrameSetting.articles_per_page'); ?>
<?php //echo $this->Form->hidden('CabinetFrameSetting.comments_per_page'); ?>

<?php echo $this->NetCommonsForm->input('Cabinet.name', array(
		'type' => 'text',
		'label' => __d('cabinets', 'Cabinet name'),
	)); ?>

<?php echo $this->element('Blocks.public_type'); ?>

<?php echo $this->NetCommonsForm->inlineCheckbox('CabinetSetting.use_comment', array(
			'label' => __d('cabinets', 'Use comment')
	)); ?>

<?php echo $this->Like->setting('CabinetSetting.use_like', 'CabinetSetting.use_unlike');?>

<!-- TODO もっと整理できる？-->
<div class="row form-group">
	<div class="col-xs-12">
		<?php echo $this->Form->checkbox('CabinetSetting.use_sns', array(
				'div' => false,
				//'hiddenField' => false,
				'checked' => isset($cabinetSetting['use_sns']) ? (int)$cabinetSetting['use_sns'] : null
			)
		); ?>
		<?php echo $this->Form->label('CabinetSetting.use_sns', __d('cabinets', 'Use sns')); ?>
	</div>
</div>


<?php
echo $this->element('Categories.edit_form', array(
	'categories' => isset($categories) ? $categories : null
));
