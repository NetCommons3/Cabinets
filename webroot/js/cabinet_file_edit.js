/**
 * Cabinets edit Javascript
 */
NetCommonsApp.controller('CabinetFile.edit',
    ['$scope', '$filter', 'NetCommonsModal', function($scope, $filter, NetCommonsModal) {
      $scope.init = function(blockId, frameId) {
        $scope.frameId = frameId;
        $scope.blockId = blockId;
      }

      $scope.showFolderTree = function() {
        NetCommonsModal.show(
            $scope, 'CabinetFile.edit.selectFolder',
            $scope.baseUrl + '/cabinets/cabinet_files_edit/select_folder/' + $scope.blockId + '?frame_id=' + $scope.frameId
        );
      };

      $scope.parent_id = 2;
    }]
);

/**
 * User modal controller
 */
NetCommonsApp.controller('CabinetFile.edit.selectFolder',
    ['$scope', '$modalInstance', function($scope, $modalInstance) {
  /**
   * dialog cancel
   *
   * @return {void}
   */
  $scope.cancel = function() {
    $modalInstance.dismiss('cancel');
  };
  $scope.select = function(frameId) {
    var parentScope = angular.element($('#cabinetFileForm_' + frameId)).scope();
    parentScope.parent_id = 1;
  }
  }]
);

