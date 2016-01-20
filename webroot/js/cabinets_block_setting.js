NetCommonsApp.controller('Cabinets.BlockSetting', function($scope) {

  /**
   * Use like button
   *
   * @return {void}
   */
  $scope.useLike = function() {
    var likeElement = $('#CabinetSettingUseLike');
    var unlikeElement = $('#CabinetSettingUseUnlike');
    if (likeElement[0].checked) {
      unlikeElement[0].disabled = false;
    } else {
      unlikeElement[0].disabled = true;
      unlikeElement[0].checked = false;
    }
  };
});
