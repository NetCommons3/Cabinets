<?php
/**
 * Cabinet frame setting element
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->Form->hidden('Frame.id'); ?>
<?php echo $this->Form->hidden('CabinetFrameSetting.id'); ?>
<?php echo $this->Form->hidden('CabinetFrameSetting.frame_key'); ?>

<?php echo $this->DisplayNumber->select('CabinetFrameSetting.articles_per_page', array(
	'label' => __d('cabinets', 'Show articles per page'),
	'unit' => array(
		'single' => __d('cabinets', '%s article'),
		'multiple' => __d('cabinets', '%s articles')
	),
)); ?>

<?php //echo $this->DisplayNumber->select('CabinetFrameSetting.comments_per_page', array(
//	'label' => __d('cabinets', 'Show comments per page'),
//	'unit' => array(
//		'single' => __d('cabinets', '%s article'),
//		'multiple' => __d('cabinets', '%s articles')
//	),
//));