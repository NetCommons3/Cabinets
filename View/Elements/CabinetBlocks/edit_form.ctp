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
<?php //echo $this->Form->hidden('CabinetFrameSetting.id'); ?>
<?php //echo $this->Form->hidden('CabinetFrameSetting.frame_key'); ?>
<?php //echo $this->Form->hidden('CabinetFrameSetting.articles_per_page'); ?>
<?php //echo $this->Form->hidden('CabinetFrameSetting.comments_per_page'); ?>

<?php echo $this->NetCommonsForm->input(
	'Cabinet.name',
	array(
		'type' => 'text',
		'label' => __d('cabinets', 'Cabinet name'),
	)
); ?>

<?php echo $this->element('Blocks.public_type');
