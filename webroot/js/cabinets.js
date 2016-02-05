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

    }]
);


NetCommonsApp.controller('Cabinets.FolderTree',
    ['$scope', function($scope) {
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
      //$scope.selectStatus = 0;
      //$scope.selectCategory = 0;
      //$scope.selectYearMonth = 0;
      //$scope.frameId = 0;
      //
      //$scope.init = function(frameId) {
      //  $scope.frameId = frameId;
      //};
    }]
);

NetCommonsApp.controller('Cabinets.path',
    ['$scope', function($scope) {
      $scope.folderPath = [];

      $scope.init = function(folderPath){
        angular.forEach(folderPath, function(value, key){
          value['url'] = $scope.baseUrl + '/cabinets/cabinet_files/index/' + $scope.blockId + '/'+ value.CabinetFile.key +'?frame_id=' + $scope.frameId

          $scope.folderPath[key] = value;
        })
        console.log($scope.folderPath);
        //$scope.folderPath = folderPath;

      };
    }]
);



