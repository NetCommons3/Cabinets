<?php

$folders = [
	[
		'CabinetFile' => [
			'id' => 1,
			'filename' => 'フォルダ1'
		],
		'children' => [
			[
				'CabinetFile' => [
					'id' => 2,
					'filename' => 'フォルダ1-1'
				],
				'children' => [
					[
						'CabinetFile' => [
							'id' => 3,
							'filename' => 'フォルダ1-1-1'
						],
						'children' => [

						]
					],
					[
						'CabinetFile' => [
							'id' => 4,
							'filename' => 'フォルダ1-1-1'
						],
					],

				]
			],
			[
				'CabinetFile' => [
					'id' => 5,
					'filename' => 'フォルダ1-2'
				],
				'children' => [
					[
						'CabinetFile' => [
							'id' => 6,
							'filename' => 'フォルダ1-2-1'
						],
					],

				]
			],
		]

	],
	[
		'CabinetFile' => [
			'id' => 7,
			'filename' => 'フォルダ2'
		],
	],

	[
		'CabinetFile' => [
			'id' => 8,
			'filename' => 'ミナミノシマコウテイペンギン写真集'
		],
	]
];

$folderPath = [
	0 => [
		'CabinetFile' => [
			'id' => 1,
			'filename' => 'フォルダ1'
		]
	],
	1 => [
		'CabinetFile' => [
			'id' => 5,
			'filename' => 'フォルダ1-2'
		]
	],
];

$currentFolderId = 5;
// ルートからカレントフォルダまで
$currentFolderTree = [
	1,5
];
// カレントフォルダのファイル&フォルダ
$cabinetFiles = [
	0 => [
		'CabinetFile' => [
			'filename' => '拡張モジュール',
			'size' => '15000',
			'download_count' => 0,
			'modified' => '2016-01-01 12:33:00',
			'is_file' => false,
		],
		'TrackableUpdater' => [
			'username' => '龍司'
		]
	],

	1 => [
		'CabinetFile' => [
			'filename' => 'NetCommons3.0.0_Beta1.zip',
			'size' => '1500000',
			'download_count' => 10,
			'modified' => '2016-01-01 12:45:00',
			'is_file' => true,
		],
		'TrackableUpdater' => [
			'username' => '龍司'
		]
	],

];
?>

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
<h1 class="cabinets_cabinetTitle"><?php echo $listTitle ?></h1>
<div class="clearfix">
	<div class="pull-left">
		キャビネット
		<?php foreach($folderPath as $folder){
			echo '＞';
			echo $this->Html->link($folder['CabinetFile']['filename'], '');

		}
		?>
	</div>
	<div class="pull-right">
		<?php if (Current::permission('content_creatable')) : ?>
			<div class="pull-right" style="margin-left: 10px;">
				<?php
				$addUrl = $this->NetCommonsHtml->url(array(
					'controller' => 'cabinet_files_edit',
					'action' => 'add',
					'frame_id' => Current::read('Frame.id')
				));
				echo $this->Button->addLink('<span class="glyphicon glyphicon-folder-close" aria-hidden="true"></span>フォルダ',
					$addUrl,
					array('tooltip' => __d('cabinets', 'Add folder'), 'escapeTitle' => false));
				?>
			</div>
		<?php endif ?>
		<?php if (Current::permission('content_creatable')) : ?>
			<div class="pull-right">
				<?php
				$addUrl = $this->NetCommonsHtml->url(array(
					'controller' => 'cabinet_files_edit',
					'action' => 'add',
					'frame_id' => Current::read('Frame.id')
				));
				echo $this->Button->addLink('<span class="glyphicon glyphicon-file" aria-hidden="true"></span>ファイル',
					$addUrl,
					array('tooltip' => __d('cabinets', 'Add file')));
				?>
			</div>
		<?php endif ?>
	</div>
</div>


<style>
	.cabinets__folder-tree__folder{
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
	/*.cabinets__folder-tree__folder:hover{*/
		/*background: #ccc;*/
	/*}*/
	.cabinets__folder-tree-toggle{
		cursor: pointer;
	}
	span.cabinets-nest{
		margin-left: 15px;
	}
</style>
<div class="row">
	<div class="col-md-3 hidden-sm hidden-xs cabinets-folder-tree inline" ng-controller="Cabinets.FolderTree" ng-init="init(<?php echo json_encode($currentFolderTree)?>)">
		<ul class="list-group">
			<a href="#" class="list-group-item ">
				<span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>キャビネット
			</a>
			<?php


			$this->CabinetsFolderTree->render($folders, $currentFolderId ,$currentFolderTree);

			?>
		</ul>



	</div>
	<div class="col-md-9 inline">
		<table class="table">
			<thead>
			<tr>
				<th>名前</th>
				<th class="hidden-sm hidden-xs">サイズ</th>
				<th>最終更新</th>
				<th class="hidden-md hidden-sm hidden-xs">ダウンロード回数</th>
				<th></th>

			</tr>
			</thead>
			<tbody>
			<tr>
				<td>
					<a href="">
						<span class="glyphicon glyphicon-circle-arrow-up" aria-hidden="true"></span>一つ上へ
					</a>
				</td>
				<td class="hidden-sm hidden-xs"></td>
				<td></td>
				<td class="hidden-md hidden-sm hidden-xs"></td>
				<td></td>
			</tr>
			<?php foreach ($cabinetFiles as $cabinetFile): ?>
				<tr>
					<td>
						<a href="#">
							<?php if ($cabinetFile['CabinetFile']['is_file']) :?>
								<span class="glyphicon glyphicon-file" aria-hidden="true"></span>

							<?php else: ?>
								<span class="glyphicon glyphicon-folder-close" aria-hidden="true"></span>

							<?php endif ?>
							<?php echo h($cabinetFile['CabinetFile']['filename']) ?>
						</a>
					</td>
					<td class="hidden-sm hidden-xs"><?php echo $this->Number->toReadableSize($cabinetFile['CabinetFile']['size']) ?></td>
					<td><?php echo $this->Date->dateFormat($cabinetFile['CabinetFile']['modified']) ?> <?php echo $cabinetFile['TrackableUpdater']['username'] ?></td>
					<td class="hidden-md hidden-sm hidden-xs"><?php echo $cabinetFile['CabinetFile']['download_count'] ?></td>
					<td>
						<button class="btn btn-default">
							<span class="glyphicon glyphicon-info-sign aria-hidden="true"></span>
						</button>
					</td>
				</tr>
			<?php endforeach ?>

			</tbody>
		</table>

	</div>
</div>

