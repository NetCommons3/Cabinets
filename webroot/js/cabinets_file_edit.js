/**
 * Cabinets edit Javascript
 */
NetCommonsApp.controller('Cabinets',
    function($scope, NetCommonsWysiwyg, $filter) {
      /**
       * tinymce
       *
       * @type {object}
       */
      $scope.tinymce = NetCommonsWysiwyg.new();

      $scope.writeBody2 = false;

      $scope.init = function(data) {
        if (data.CabinetFile) {
          $scope.cabinetFile = data.CabinetFile;
          if ($scope.cabinetFile.body2 !== null) {
            if ($scope.cabinetFile.body2.length > 0) {
              $scope.writeBody2 = true;
            }
          }
        }
      };

      $scope.cabinetFile = {
        body1: '',
        body2: '',
        publish_start: ''
      };
    }
);
