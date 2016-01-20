<div class="clearfix cabinets_file_reaction">
	<div class="pull-left">
		<?php if ($cabinetSetting['use_sns']) : ?>

			<?php $contentUrl = FULL_BASE_URL . $this->NetCommonsHtml->url(array(
					'action' => 'view',
					'frame_id' => Current::read('Frame.id'),
					'key' => $cabinetFile['CabinetFile']['key'],
				));
			?>
			<!--Facebook-->
			<div class="fb-like pull-left" data-href="<?php echo $contentUrl ?>" data-layout="button_count" data-action="like"
				 data-show-faces="false" data-share="false"></div>

			<!--Twitter-->
			<div class="pull-left">
				<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $contentUrl ?>">Tweet</a>
				<script>!function (d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
						if (!d.getElementById(id)) {
							js = d.createElement(s);
							js.id = id;
							js.src = p + '://platform.twitter.com/widgets.js';
							fjs.parentNode.insertBefore(js, fjs);
						}
					}(document, 'script', 'twitter-wjs');</script>
			</div>
		<?php endif ?>

		<div class="pull-left">
			<?php if (isset($index) && ($index === true)) : ?>
				<!--view only-->
				<?php echo $this->Like->display($cabinetSetting, $cabinetFile, array('div' => true)); ?>
			<?php else : ?>
				<!--post like-->
				<?php echo $this->Like->buttons('CabinetFile', $cabinetSetting, $cabinetFile, array('div' => true)); ?>
			<?php endif ?>
		</div>
	</div>

	<div class="pull-right">
		<?php if (isset($index) && ($index === true)) : ?>
		<span style="padding-right: 15px;">
			<span class="glyphicon glyphicon-comment" aria-hidden="true"></span> <?php echo (int)$cabinetFile['ContentCommentCnt']['cnt']; ?>
		</span>
		<?php endif ?>
	</div>

</div>
