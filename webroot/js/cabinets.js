/**
 * Cabinets Javascript
 */
//NetCommonsApp.value(
//    'CabinetsShareValue', {
//      frameId: null,
//      blockId: null
//    }
//);
NetCommonsApp.factory(
    'CabinetsShareValue', function(){
      return {
        frameId: null,
        blockId: null,
        parentId: null,
      }
    }
);

NetCommonsApp.controller('Cabinets',
    ['$scope', 'CabinetsShareValue', function($scope, CabinetsShareValue) {
      $scope.folder = [];

      $scope.init = function(blockId, frameId) {
        $scope.frameId = frameId;
        $scope.blockId = blockId;

        CabinetsShareValue.frameId = frameId;
        CabinetsShareValue.blockId = blockId;
      }

      $scope.folderPath = [];


    }]
);

NetCommonsApp.controller('CabinetFile.index',
    ['$scope', '$filter', 'NetCommonsModal', 'CabinetsShareValue', '$http', function($scope, $filter, NetCommonsModal, CabinetsShareValue, $http) {
      $scope.moved = {};
      $scope.init = function(parentId) {
       $scope.parent_id = parentId;
      }

      $scope.moveFile = function(cabinetFileKey) {
        console.log($scope.parentId);
        var modal = NetCommonsModal.show(
            $scope, 'CabinetFile.edit.selectFolder',
            $scope.baseUrl + '/cabinets/cabinet_files_edit/select_folder/' + CabinetsShareValue.blockId + '/parent_tree_id:'+$scope.parent_id+'?frame_id=' + CabinetsShareValue.frameId
        );
        modal.result.then(function(parentId){
          if($scope.parent_id != parentId){
            // 移動を裏で呼び出す
            var url = $scope.baseUrl + '/cabinets/cabinet_files_edit/move/' + CabinetsShareValue.blockId + '/' + cabinetFileKey + '/parent_id:'+parentId+'?frame_id=' + CabinetsShareValue.frameId

            $http({
              url: url,
              method: 'POST'
            })
                .success(function (data, status, headers, config) {
                  $scope.flashMessage(data.name, data.class, data.interval);

                  // 違うフォルダへ移動なので、今のフォルダ内ファイル一覧から非表示にする
                  $scope.moved[cabinetFileKey] = true;
                  // $scope.parent_id = parentId;

                  // TODO フォルダを動かしたら左のフォルダツリーを再読み込み
                  var treeUrl =  $scope.baseUrl + '/cabinets/cabinet_files/tree/' + CabinetsShareValue.blockId + '/' +$scope.parent_id+'?frame_id=' + CabinetsShareValue.frameId;
                  $http({
                    url: treeUrl,
                    method: 'GET'
                  }).success(function (data, status, headers, config) {
                    // console.log(data);
                    // $('#test').replaceWith(data);
                  });


                  // var result = [];
                  // angular.forEach(data['folderPath'], function(value, key){
                  //   value['url'] = $scope.baseUrl + '/cabinets/cabinet_files/index/' + $scope.blockId + '/'+ value.CabinetFile.key +'?frame_id=' + $scope.frameId
                  //
                  //   result[key] = value;
                  // })
                  // $scope.folderPath = result;
                })
                .error(function (data, status, headers, config) {
                  // エラー処理
                  $scope.flashMessage(data.name, 'danger', 0);
                });
          }
        })
      };

    }]
);

NetCommonsApp.controller('CabinetFile.addFile',
    ['$scope', '$filter', 'NetCommonsModal', 'CabinetsShareValue', '$http', function($scope, $filter, NetCommonsModal, CabinetsShareValue, $http) {
      $scope.init = function(parentId) {
        $scope.parent_id = parentId;
      }

      $scope.addFile = function() {
        var url = $scope.baseUrl + '/cabinets/cabinet_files_edit/add/' + CabinetsShareValue.blockId;
        if($scope.parent_id > 0){
          url = url + '/parent_id:'+$scope.parent_id;
        }
        url = url + '?frame_id=' + CabinetsShareValue.frameId;
        console.log(url);
        var modal = NetCommonsModal.show(
            $scope,
            'CabinetFile.edit.selectFolder',
            url
        );
        //modal.result.then(function(parentId){
        //  console.log(parentId);
        //  $scope.parent_id = parentId;
        //
        //  // 親ツリーIDが変更されたので、パス情報を取得しなおす。
        //  //  Ajax json形式でパス情報を取得する
        //
        //  var url = $scope.baseUrl + '/cabinets/cabinet_files_edit/get_folder_path/' + CabinetsShareValue.blockId + '/tree_id:'+$scope.parent_id+'?frame_id=' + CabinetsShareValue.frameId
        //
        //  $http({
        //    url: url,
        //    method: 'GET'
        //  })
        //      .success(function (data, status, headers, config) {
        //        var result = [];
        //        angular.forEach(data['folderPath'], function(value, key){
        //          value['url'] = $scope.baseUrl + '/cabinets/cabinet_files/index/' + $scope.blockId + '/'+ value.CabinetFile.key +'?frame_id=' + $scope.frameId
        //
        //          result[key] = value;
        //        })
        //        $scope.folderPath = result;
        //      })
        //      .error(function (data, status, headers, config) {
        //        // TODO エラー処理
        //        ;
        //      });
        //})
      };

    }]
);

NetCommonsApp.controller('Cabinets.FolderTree',
    ['$scope', 'CabinetsShareValue', function($scope, CabinetsShareValue) {
      $scope.folder = [];

      $scope.init = function(currentFolderPath){
        console.log(currentFolderPath);
        angular.forEach(currentFolderPath, function(value, key){
          $scope.folder[value] = true;
        })
      };

      $scope.toggle = function(folderId){
        $scope.folder[folderId] = ! $scope.folder[folderId];
      }
    }]
);

NetCommonsApp.controller('Cabinets.path',
    ['$scope', function($scope) {

      $scope.init = function(folderPath){

        angular.forEach(folderPath, function(value, key){
          value['url'] = $scope.baseUrl + '/cabinets/cabinet_files/index/' + $scope.blockId + '/'+ value.CabinetFile.key +'?frame_id=' + $scope.frameId

          $scope.folderPath[key] = value;
        })
      };
    }]
);

/**
 * Cabinets edit Javascript
 */
NetCommonsApp.controller('CabinetFile.edit',
    ['$scope', '$filter', 'NetCommonsModal', 'CabinetsShareValue', '$http', function($scope, $filter, NetCommonsModal, CabinetsShareValue, $http) {
      $scope.init = function(parentId) {
        $scope.parent_id = parentId;
        CabinetsShareValue.parent_id = parentId;
      }

      $scope.showFolderTree = function() {
        var modal = NetCommonsModal.show(
            $scope, 'CabinetFile.edit.selectFolder',
            $scope.baseUrl + '/cabinets/cabinet_files_edit/select_folder/' + CabinetsShareValue.blockId + '/parent_tree_id:'+CabinetsShareValue.parent_id+'?frame_id=' + CabinetsShareValue.frameId
        );
        modal.result.then(function(parentId){
          console.log(parentId);
          $scope.parent_id = parentId;

          // 親ツリーIDが変更されたので、パス情報を取得しなおす。
          //  Ajax json形式でパス情報を取得する

          var url = $scope.baseUrl + '/cabinets/cabinet_files_edit/get_folder_path/' + CabinetsShareValue.blockId + '/tree_id:'+$scope.parent_id+'?frame_id=' + CabinetsShareValue.frameId

          $http({
            url: url,
            method: 'GET'
          })
            .success(function (data, status, headers, config) {
              var result = [];
              angular.forEach(data['folderPath'], function(value, key){
                value['url'] = $scope.baseUrl + '/cabinets/cabinet_files/index/' + $scope.blockId + '/'+ value.CabinetFile.key +'?frame_id=' + $scope.frameId

                result[key] = value;
              })
                $scope.folderPath = result;
              })
              .error(function (data, status, headers, config) {
                // TODO エラー処理
                ;
              });
        })
      };

    }]
);

/**
 * User modal controller
 */
NetCommonsApp.controller('CabinetFile.edit.selectFolder',
    ['$scope', '$modalInstance', 'CabinetsShareValue', function($scope, $modalInstance, CabinetsShareValue) {
      /**
       * dialog cancel
       *
       * @return {void}
       */
      $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
      };
      $scope.select = function(parentid) {
        $modalInstance.close(parentid);
      }
    }]
);




