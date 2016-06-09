/**
 * Cabinets Javascript
 */

NetCommonsApp.controller('Cabinets',
    ['$scope', function($scope) {
      $scope.folder = [];

      $scope.init = function(blockId, frameId) {
        $scope.frameId = frameId;
        $scope.blockId = blockId;
      }

      $scope.folderPath = [];


    }]
);

NetCommonsApp.controller('CabinetFile.index',
    ['$scope', '$filter', 'NetCommonsModal', '$http', function($scope, $filter, NetCommonsModal, $http) {
      $scope.moved = {};
      $scope.init = function(parentId) {
        $scope.parent_id = parentId;
      }

      $scope.moveFile = function(cabinetFileKey, isFolder) {

        var modal = NetCommonsModal.show(
            $scope, 'CabinetFile.edit.selectFolder',
            $scope.baseUrl + '/cabinets/cabinet_files_edit/select_folder/' + $scope.blockId + '/' + cabinetFileKey + '?frame_id=' + $scope.frameId
        );
        modal.result.then(function(parentId) {

          if ($scope.parent_id != parentId) {
            // 移動を裏で呼び出す
            var url = $scope.baseUrl + '/cabinets/cabinet_files_edit/move/' + $scope.blockId + '/' + cabinetFileKey + '/parent_id:' + parentId + '?frame_id=' + $scope.frameId

            $http({
              url: url,
              method: 'POST'
            })
                .success(function(data, status, headers, config) {

                  if (isFolder) {
                    // フォルダを動かしたらリロード
                    location.reload();
                  } else {
                    $scope.flashMessage(data.name, data.class, data.interval);

                    // 違うフォルダへ移動なので、今のフォルダ内ファイル一覧から非表示にする
                    $scope.moved[cabinetFileKey] = true;
                  }
                })
                .error(function(data, status, headers, config) {
                  // エラー処理
                  $scope.flashMessage(data.name, 'danger', 0);
                });
          }
        })
      };

    }]
);

NetCommonsApp.controller('CabinetFile.addFile',
    ['$scope', '$filter', 'NetCommonsModal', '$http', function($scope, $filter, NetCommonsModal, $http) {
      $scope.init = function(parentId) {
        $scope.parent_id = parentId;
      }

      $scope.addFile = function() {

        var blockId = $scope.blockId;
        var frameId = $scope.frameId;
        var url = $scope.baseUrl + '/cabinets/cabinet_files_edit/add/' + blockId;
        if ($scope.parent_id > 0) {
          url = url + '/parent_id:' + $scope.parent_id;
        }
        url = url + '?frame_id=' + frameId;
        var modal = NetCommonsModal.show(
            $scope,
            'CabinetFile.addFileModal',
            url
        );
      };
    }]
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
      $scope.folder = [];

      $scope.init = function(currentFolderPath) {
        console.log(currentFolderPath);
        angular.forEach(currentFolderPath, function(value, key) {
          $scope.folder[value] = true;
        })
      };

      $scope.toggle = function(folderId) {
        $scope.folder[folderId] = !$scope.folder[folderId];
      }
    }]
);

NetCommonsApp.controller('Cabinets.path',
    ['$scope', function($scope) {

      $scope.init = function(folderPath, pageUrl) {

        // TODO 一つ目だけPageUrlにする
        angular.forEach(folderPath, function(value, key) {
          if (key == 0) {
            value['url'] = pageUrl;
          } else {
            value['url'] = $scope.baseUrl + '/cabinets/cabinet_files/index/' + $scope.blockId + '/' + value.CabinetFile.key + '?frame_id=' + $scope.frameId

          }

          $scope.folderPath[key] = value;
        })
      };
    }]
);

/**
 * Cabinets edit Javascript
 */
NetCommonsApp.controller('CabinetFile.edit',
    ['$scope', '$filter', 'NetCommonsModal', '$http', function($scope, $filter, NetCommonsModal, $http) {
      $scope.init = function(parentId, fileKey) {
        $scope.parent_id = parentId;
        $scope.parent_id = parentId;
        $scope.fileKey = fileKey;
      }

      $scope.showFolderTree = function() {
        var modal = NetCommonsModal.show(
            $scope, 'CabinetFile.edit.selectFolder',
            $scope.baseUrl + '/cabinets/cabinet_files_edit/select_folder/' + $scope.blockId + '/' + $scope.fileKey + '?frame_id=' + $scope.frameId
        );
        modal.result.then(function(parentId) {
          console.log(parentId);
          $scope.parent_id = parentId;

          // 親ツリーIDが変更されたので、パス情報を取得しなおす。
          //  Ajax json形式でパス情報を取得する

          var url = $scope.baseUrl + '/cabinets/cabinet_files_edit/get_folder_path/' + $scope.blockId + '/tree_id:' + $scope.parent_id + '?frame_id=' + $scope.frameId

          $http({
            url: url,
            method: 'GET'
          })
              .success(function(data, status, headers, config) {
                var result = [];
                angular.forEach(data['folderPath'], function(value, key) {
                  value['url'] = $scope.baseUrl + '/cabinets/cabinet_files/index/' + $scope.blockId + '/' + value.CabinetFile.key + '?frame_id=' + $scope.frameId

                  result[key] = value;
                })
                $scope.folderPath = result;
              })
              .error(function(data, status, headers, config) {
                // TODO エラー処理
                ;
              });
        })
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
      }
    }]
);




