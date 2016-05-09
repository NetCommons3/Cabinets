<div class="cabinets_file_meta">
	<div>

		<?php echo __d(
			'cabinets',
			'posted : %s',
			$this->CabinetsFormat->publishedDatetime($cabinetFile['CabinetFile']['publish_start'])
		); ?>&nbsp;

		<!--	TODO 投稿者アバター-->
		<!--	TODO　投稿者名 リンク-->
		<?php echo $this->Html->link($cabinetFile['TrackableCreator']['handlename'], array()); ?>&nbsp;
		<?php echo __d('cabinets', 'Category') ?>:<?php echo $this->Html->link(
			$cabinetFile['Category']['name'],
			$this->NetCommonsHtml->url(
				array(
					'controller' => 'cabinet_files',
					'action' => 'index',
					'frame_id' => Current::read('Frame.id'),
					'category_id' => $cabinetFile['CabinetFile']['category_id']
				)
			)
		); ?>
	</div>
</div>
