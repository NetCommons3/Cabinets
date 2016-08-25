/**
 * Cabinets Javascript
 */
// NetCommonsApp.directive('resize',
// ['$rootScope', '$window', '$timeout', function($rootScope, $window, $timeout){
//   return {
//     restrict: 'EA',
//     scope: {
//       resize: '&'
//     },
//     link: function(scope){
//       var timer = false;
//       angular.element($window).on('load resize', function(e){
//         console.log('resize');
//         if(timer) $timeout.cancel(timer);
//
//         timer = $timeout(function(){
//           scope.resize();
//         }, 200);
//
//       });
//     }
//   }
// }]);

NetCommonsApp.controller('Cabinets',
    ['$scope', function($scope) {
      $scope.folder = [];

      $scope.init = function(blockId, frameId) {
        $scope.frameId = frameId;
        $scope.blockId = blockId;
      };

      $scope.folderPath = [];


    }]
);


NetCommonsApp.controller('CabinetFile.index',
    ['$scope', '$filter', 'NetCommonsModal', '$http', 'NC3_URL',
      function($scope, $filter, NetCommonsModal, $http, NC3_URL) {
        $scope.moved = {};
        $scope.init = function(parentId) {
          $scope.parent_id = parentId;
        };

        $scope.moveFile = function(cabinetFileKey, isFolder, data) {
          var modal = NetCommonsModal.show(
              $scope, 'CabinetFile.edit.selectFolder',
              NC3_URL + '/cabinets/cabinet_files_edit/select_folder/' + $scope.blockId +
              '/' + cabinetFileKey + '?frame_id=' + $scope.frameId);
          modal.result.then(function(parentId) {

            if ($scope.parent_id != parentId) {
              // 移動を裏で呼び出す
              // get token
              $http.get(NC3_URL + '/net_commons/net_commons/csrfToken.json')
                  .success(function(token) {
                    var post = data;
console.log(post);
                    post._Token.key = token.data._Token.key;

                    post.CabinetFileTree.parent_id = parentId;
console.log(post);
                    // post.Test.foo = 1;
                    //POSTリクエスト
                    var url = NC3_URL + '/cabinets/cabinet_files_edit/move.json';
                    // var url = NC3_URL + '/cabinets/cabinet_files_edit/move/' + $scope.blockId +
                    //     '/' + cabinetFileKey + '?frame_id=' + $scope.frameId;
                    $http.post(
                        url,
                        $.param({_method: 'POST', data: post}),
                        {cache: false,
                          headers:
                          {'Content-Type': 'application/x-www-form-urlencoded'}
                        }
                    )
                        .success(function(data) {
                          if (isFolder) {
                            // フォルダを動かしたらリロード
                            location.reload();
                          } else {
                            $scope.flashMessage(data.name, data.class, data.interval);
                            // 違うフォルダへ移動なので、今のフォルダ内ファイル一覧から非表示にする
                            $scope.moved[cabinetFileKey] = true;
                          }
                        })
                        .error(function(data, status) {
                          // エラー処理
                          $scope.flashMessage(data.name, 'danger', 0);
                        });
                  })
                  .error(function(data, status) {
                    //Token error condition
                    // エラー処理
                    $scope.flashMessage(data.name, 'danger', 0);
                  });
            }
          });
        };
      }]
);

NetCommonsApp.controller('CabinetFile.addFile',
    ['$scope', '$filter', 'NetCommonsModal', '$http', 'NC3_URL',
      function($scope, $filter, NetCommonsModal, $http, NC3_URL) {
        $scope.init = function(parentId) {
          $scope.parent_id = parentId;
        };

        $scope.addFile = function() {

          var blockId = $scope.blockId;
          var frameId = $scope.frameId;
          var url = NC3_URL + '/cabinets/cabinet_files_edit/add/' + blockId;
          if ($scope.parent_id > 0) {
            url = url + '/parent_id:' + $scope.parent_id;
          }
          url = url + '?frame_id=' + frameId;
          var modal = NetCommonsModal.show($scope, 'CabinetFile.addFileModal', url);
        };
      }
    ]
);


/**
 * AddFile Modal
 */
NetCommonsApp.controller('CabinetFile.addFileModal',
    ['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {

      /**
       * dialog cancel
       *
       * @return {void}
       */
      $scope.cancel = function() {
        $uibModalInstance.dismiss('cancel');
      };
    }]
);


NetCommonsApp.controller('Cabinets.FolderTree',
    ['$scope', function($scope) {

      // $scope.treeVisible = true;
      //
      // this.trigger = function(event){
      //   $scope.$broadcast(event);
      // }
      // this.resizeHandler = function(){
      //   // Treeのサイズを取得
      //   main = angular.element(document).find('#container-main');
      //   if (main.width() < 600) {
      //     $scope.treeVisible = false;
      //   } else {
      //     $scope.treeVisible = true;
      //   }
      // }

      $scope.folder = [];

      $scope.init = function(currentFolderPath) {
        angular.forEach(currentFolderPath, function(value, key) {
          $scope.folder[value] = true;
        });
      };

      $scope.toggle = function(folderId) {
        $scope.folder[folderId] = !$scope.folder[folderId];
      };
    }]
);


NetCommonsApp.controller('Cabinets.path',
    ['$scope', 'NC3_URL', function($scope, NC3_URL) {

      $scope.init = function(folderPath, pageUrl) {

        // 一つ目だけPageUrlにする
        angular.forEach(folderPath, function(value, key) {
          if (key == 0) {
            value['url'] = pageUrl;
          } else {
            value['url'] = NC3_URL + '/cabinets/cabinet_files/index/' +
                $scope.blockId + '/' + value.CabinetFile.key + '?frame_id=' + $scope.frameId;
          }

          $scope.folderPath[key] = value;
        });
      };
    }]
);


/**
 * Cabinets edit Javascript
 */
NetCommonsApp.controller('CabinetFile.edit',
    ['$scope', '$filter', 'NetCommonsModal', '$http', 'NC3_URL',
      function($scope, $filter, NetCommonsModal, $http, NC3_URL) {
        $scope.init = function(parentId, fileKey) {
          $scope.parent_id = parentId;
          $scope.parent_id = parentId;
          $scope.fileKey = fileKey;
        };

        $scope.showFolderTree = function() {

          var selectFolderUrl = NC3_URL + '/cabinets/cabinet_files_edit/select_folder/' +
              $scope.blockId + '/';
          selectFolderUrl = selectFolderUrl + $scope.fileKey;
          // 新規作成時はfileKeyがないのでparent_idで現在位置を特定
          selectFolderUrl = selectFolderUrl + '/parent_id:' + $scope.parent_id;
          selectFolderUrl = selectFolderUrl + '?frame_id=' + $scope.frameId;

          var modal = NetCommonsModal.show($scope, 'CabinetFile.edit.selectFolder',
              selectFolderUrl);
          modal.result.then(function(parentId) {
            $scope.parent_id = parentId;

            // 親ツリーIDが変更されたので、パス情報を取得しなおす。
            //  Ajax json形式でパス情報を取得する

            var url = NC3_URL + '/cabinets/cabinet_files_edit/get_folder_path/' +
                $scope.blockId + '/tree_id:' + $scope.parent_id + '?frame_id=' + $scope.frameId;

            $http({
              url: url,
              method: 'GET'
            })
                .success(function(data, status, headers, config) {
                  var result = [];
                  angular.forEach(data['folderPath'], function(value, key) {
                    value['url'] = NC3_URL + '/cabinets/cabinet_files/index/' +
                        $scope.blockId + '/' + value.CabinetFile.key +
                        '?frame_id=' + $scope.frameId;

                    result[key] = value;
                  });
                  $scope.folderPath = result;
                })
                .error(function(data, status, headers, config) {
                  $scope.flashMessage(data.name, 'danger', 0);
                });
          });
        };

      }]
);


/**
 * selectFolder
 */
NetCommonsApp.controller('CabinetFile.edit.selectFolder',
    ['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {
      /**
       * dialog cancel
       *
       * @return {void}
       */
      $scope.cancel = function() {
        $uibModalInstance.dismiss('cancel');
      };
      $scope.select = function(parentid) {
        $uibModalInstance.close(parentid);
      };
    }]
);

