/**
 * Cabinets edit Javascript
 */
NetCommonsApp.controller('CabinetFile.edit',
    ['$scope', '$filter', 'NetCommonsModal', function($scope, $filter, NetCommonsModal) {
      $scope.init = function(blockId, frameId, parentId) {
        $scope.frameId = frameId;
        $scope.blockId = blockId;
        $scope.parent_id = parentId;
      }

      $scope.showFolderTree = function() {
        NetCommonsModal.show(
            $scope, 'CabinetFile.edit.selectFolder',
            $scope.baseUrl + '/cabinets/cabinet_files_edit/select_folder/' + $scope.blockId + '/parent_tree_id:'+$scope.parent_id+'?frame_id=' + $scope.frameId
        );
      };

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
      $scope.select = function() {
        return false;
      };
  $scope.select2 = function(frameId) {
    var parentScope = angular.element($('#cabinetFileForm_' + frameId)).scope();
    parentScope.parent_id = 1;
  }
  }]
);

