<?php echo $this->element('shared_header'); ?>

<?php
echo $this->Html->css(
	'/cabinets/css/cabinets.css',
	array(
		'plugin' => false,
		'once' => true,
		'inline' => false
	)
); ?>
<?php
// Like
echo $this->Html->script(
	'/likes/js/likes.js',
	array(
		'plugin' => false,
		'once' => true,
		'inline' => false
	)
);
echo $this->Html->css(
	'/likes/css/style.css',
	array(
		'plugin' => false,
		'once' => true,
		'inline' => false
	)
);
?>
<?php echo $this->BackTo->pageLinkButton(__d('cabinets', 'Move list'), array('icon' => 'list')) ?>
<div class="cabinets_file_status">
	<?php echo $this->Workflow->label($cabinetFile['CabinetFile']['status']); ?>
</div>

<article>
	<h1><?php echo h($cabinetFile['CabinetFile']['title']); ?></h1>

	<?php echo $this->element('file_meta_info'); ?>

	<div>
		<?php echo $this->element('CabinetFiles/edit_link', array('status' => $cabinetFile['CabinetFile']['status'])); ?>
	</div>

	<!-- Files -->
	<div>
		Image :
		<?php echo $this->Html->image(
				$this->NetCommonsHtml->url(
						[
							'action' => 'download',
							'key' => $cabinetFile['CabinetFile']['key'],
							'photo',
							'big',
						]
				)
		); ?>
	</div>
	<div>
		PDF :
		<?php echo $this->Html->link('PDF',
				$this->NetCommonsHtml->url(
						[
							'action' => 'download_pdf',
							'key' => $cabinetFile['CabinetFile']['key'],
							'pdf',
						]
				)
		); ?>
	</div>



	<div>
		<?php echo $cabinetFile['CabinetFile']['body1']; ?>
	</div>
	<div>
		<?php echo $cabinetFile['CabinetFile']['body2']; ?>
	</div>

	<?php echo $this->element('file_footer'); ?>

	<!-- Tags -->
	<?php if (isset($cabinetFile['Tag'])) : ?>
	<div>
		<?php echo __d('cabinets', 'tag'); ?>
		<?php foreach ($cabinetFile['Tag'] as $cabinetTag): ?>
			<?php echo $this->Html->link(
				$cabinetTag['name'],
				$this->NetCommonsHtml->url(array('controller' => 'cabinet_files', 'action' => 'tag', 'frame_id' => Current::read('Frame.id'), 'id' => $cabinetTag['id']))
			); ?>&nbsp;
		<?php endforeach; ?>
	</div>
	<?php endif ?>

	<div>
		<?php /* コンテンツコメント */ ?>
		<div class="row">
			<div class="col-xs-12">
				<?php echo $this->element('ContentComments.index', array(
					'pluginKey' => $this->request->params['plugin'],
					'contentKey' => $cabinetFile['CabinetFile']['key'],
					'isCommentApproved' => $cabinetSetting['use_comment_approval'],
					'useComment' => $cabinetSetting['use_comment'],
					'contentCommentCnt' => $cabinetFile['ContentCommentCnt']['cnt'],
					'redirectUrl' => $this->NetCommonsHtml->url(array('plugin' => 'cabinets', 'controller' => 'cabinet_files', 'action' => 'view', 'frame_id' => Current::read('Frame.id'), 'key' => $cabinetFile['CabinetFile']['key'])),
				)); ?>
			</div>
		</div>
	</div>
</article>


