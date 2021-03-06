<?php
echo $this->Html->css(
	'/cabinets/css/cabinets.css',
	array(
		'plugin' => false,
		'once' => true,
		'inline' => false
	)
); ?>
<h1 class="cabinets_cabinetTitle"><?php echo h($cabinet['Cabinet']['name']) ?></h1>
<div class="panel panel-warning">
	<div class="panel-heading">
		<?php echo __d('cabinets', 'Download failed'); ?>
	</div>
	<div class="panel-body">
		<?php echo $error ?>

	</div>
</div>
<div class="text-center">
	<?php
	$url = NetCommonsUrl::backToPageUrl();
	echo $this->NetCommonsHtml->link(
		__d('cabinets', 'Go to List'),
		$url,
		['class' => 'btn btn-default']
	);
	?>
</div>
