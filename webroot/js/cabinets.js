/**
 * Cabinets Javascript
 */

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




