/**
 * Cabinets Javascript
 */


/**
 * 圧縮ダウンロード
 */
NetCommonsApp.controller('CabinetFiles.zipDownload',
    ['$scope', '$http', 'NC3_URL', 'ajaxSendPost',
      function($scope, $http, NC3_URL, ajaxSendPost) {

        /**
         * ファイル(フォルダ)キー
         *
         * @type {object}
         */
        $scope.postData = {};

        /**
         * イニシャライズ処理
         *
         * @return {void}
         */
        $scope.initialize = function(postData) {
          $scope.postData = postData;
        };

        /**
         * ダウンロード処理
         *
         * @return {void}
         */
        $scope.download = function($event) {
          if ($scope.$parent.sending) {
            event.preventDefault();
            return;
          }
          $scope.$parent.sending = true;
          $event.preventDefault();

          var postData = {
            CabinetFile: $scope.postData['Check']['request'],
            _Token: $scope.postData['Check']['token']
          };
          ajaxSendPost('POST', $scope.postData['Check']['action'], postData)
              .success(function(response) {
                $scope.__submitDownload();
              })
              .error(function(response) {
                //エラー処理
                var data = response.data;
                $scope.flashMessage(data.message, 'danger', data.interval);
                $scope.$parent.sending = false;
              });
        };

        /**
         * ダウンロードするためのformエレメントを作成する
         *
         * @return {void}
         */
        $scope.__submitDownload = function() {
          $http.get(NC3_URL + '/net_commons/net_commons/csrfToken.json')
              .then(function(response) {
                var token = response.data;
                $scope.postData['Download']['token']['key'] = token.data._Token.key;
                var formId = 'CabinetFileZipDownload' + Math.random().toString(36).slice(2);
                var formElement = $scope.__createFormElement(formId);
                formElement.appendTo(document.body).submit();
                $('#' + formId).remove();
                $scope.$parent.sending = false;
              },
              function(response) {
                $scope.$parent.sending = false;
              });
        };

        /**
         * ダウンロードするためのformエレメントを作成する
         *
         * @return {void}
         */
        $scope.__createFormElement = function(formId) {
          var formElement = $('<form/>', {
            id: formId,
            target: '_blank',
            action: $scope.postData['Download']['action'],
            method: 'post'
          });

          angular.forEach($scope.postData['Download']['token'], function(value, key) {
            var inputElement = $('<input/>', {
              type: 'hidden',
              name: 'data[_Token][' + key + ']',
              value: value
            });
            formElement.append(inputElement);
          });
          angular.forEach($scope.postData['Download']['request'], function(value, key) {
            var inputElement = $('<input/>', {
              type: 'hidden',
              name: 'data[CabinetFile][' + key + ']',
              value: value
            });
            formElement.append(inputElement);
          });

          return formElement;
        };

      }]);


NetCommonsApp.controller('CabinetFiles.loadZipDownload',
    ['$scope', '$http', 'NC3_URL',
      function($scope, $http, NC3_URL) {

        /**
         * イニシャライズ処理
         *
         * @return {void}
         */
        $scope.load = function(frameId, fileKeys) {
          var params = '?frame_id=' + frameId + '&file_keys=' + fileKeys;
          $http.get(NC3_URL + '/cabinets/cabinet_files/load_download_folder.json' + params)
          .then(
              function(response) {
              },
              function() {
              });
        };
      }]
);
