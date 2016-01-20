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
echo $this->Html->script(
	'/cabinets/js/cabinets.js',
	array(
		'plugin' => false,
		'once' => true,
		'inline' => false
	)
);
?>
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
echo $this->Html->script(
	'/AuthorizationKeys/js/authorization_keys.js',
	array(
		'plugin' => false,
		'once' => true,
		'inline' => false
	)
);

?>

<div class="cabinetFiles index nc-content-list" ng-controller="Cabinets.Files" ng-init="init(<?php echo Current::read('Frame.id') ?>)">
	<h1 class="cabinets_cabinetTitle"><?php echo $listTitle ?></h1>

	<div class="clearfix cabinets_navigation_header">
		<div class="pull-left">
			<div class="dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
					<?php echo $filterDropDownLabel ?>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
					<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $this->NetCommonsHtml->url(
							array(
								'action' => 'index',
								'frame_id' => Current::read('Frame.id'),
							)
						);?>"><?php echo __d('cabinets', 'All Files') ?></a></li>
					<li role="presentation" class="dropdown-header"><?php echo __d('cabinets', 'Category') ?></li>

					<?php echo $this->Category->dropDownToggle(array(
						'empty' => false,
						'displayMenu' => false,
						$this->NetCommonsHtml->url(array('action' => 'index')),
					)); ?>

					<li role="presentation" class="divider"></li>

					<li role="presentation" class="dropdown-header"><?php echo __d('cabinets', 'Archive')?></li>
					<?php foreach($yearMonthOptions as $yearMonth => $label): ?>

						<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $this->NetCommonsHtml->url(
								array(
									'action' => 'year_month',
									'frame_id' => Current::read('Frame.id'),
									'year_month' => $yearMonth,
								)
							);?>"><?php echo $label ?></a></li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>

		<?php if (Current::permission('content_creatable')) : ?>
		<div class="pull-right">
			<?php
			$addUrl = $this->NetCommonsHtml->url(array(
				'controller' => 'cabinet_files_edit',
				'action' => 'add',
				'frame_id' => Current::read('Frame.id')
			));
			echo $this->Button->addLink('',
				$addUrl,
			array('tooltip' => __d('cabinets', 'Add file')));
			?>
		</div>
		<?php endif ?>

	</div>

	<div>
		<!--ファイル一覧-->
		<?php foreach ($cabinetFiles as $cabinetFile): ?>

			<div class="cabinets_file" ng-controller="Cabinets.Files.File">


				<div class="cabinets_file_status">
					<?php echo $this->Workflow->label($cabinetFile['CabinetFile']['status']); ?>
				</div>

				<article>
					<h2 class="cabinets_file_title">
						<?php echo $this->Html->link(
							$cabinetFile['CabinetFile']['title'],
							$this->NetCommonsHtml->url(
								array(
									'controller' => 'cabinet_files',
									'action' => 'view',
									//'frame_id' => Current::read('Frame.id'),
									'key' => $cabinetFile['CabinetFile']['key'],
										'photo'
								)
							)
						);
						?>
					</h2>
					<?php echo $this->element('file_meta_info', array('cabinetFile' => $cabinetFile)); ?>

					<!-- Files -->
					<div>
						Image :
						<?php echo $this->Html->image(
								$this->NetCommonsHtml->url(
										[
												'action' => 'download',
												'key' => $cabinetFile['CabinetFile']['key'],
												'photo',
											'thumb',
										]
								)
						); ?>
					</div>
					<?php if (Hash::get($cabinetFile, 'UploadFile.pdf')) :?>
					<div>
						PDF :
						<?php echo $this->Html->link('PDF',
								'#',
							['authorization-keys-popup-link',
								'url' => $this->NetCommonsHtml->url(
									[
										'action' => 'download_pdf',
										'key' => $cabinetFile['CabinetFile']['key'],
										'pdf',
									]
								),
								'frame-id' => Current::read('Frame.id')
							]
						); ?>
					</div>
					<?php endif ?>

					<div class="cabinets_file_body1">
						<?php echo $cabinetFile['CabinetFile']['body1']; ?>
					</div>
					<?php if ($cabinetFile['CabinetFile']['body2']) : ?>
						<div ng-hide="isShowBody2">
							<a ng-click="showBody2()"><?php echo __d('cabinets', 'Read more'); ?></a>
						</div>
						<div ng-show="isShowBody2">
							<?php echo $cabinetFile['CabinetFile']['body2'] ?>
						</div>
						<div ng-show="isShowBody2">
							<a ng-click="hideBody2()"><?php echo __d('cabinets', 'Close'); ?></a>
						</div>
					<?php endif ?>
					<?php echo $this->element('file_footer', array('cabinetFile' => $cabinetFile, 'index' => true)); ?>
				</article>

			</div>


		<?php endforeach; ?>
	</div>

	<?php echo $this->element('NetCommons.paginator') ?>
</div>
