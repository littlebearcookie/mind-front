angular.module('kityminderEditor')
    .directive('hyperLink', ['$modal', function($modal) {
        return {
            restrict: 'E',
            templateUrl: 'ui/directive/EnterButton/EnterButton.html',
            scope: {
                minder: '='
            },
            replace: true,
            // link: function($scope) {
            //     var minder = $scope.minder;

            //     $scope.addHyperlink = function() {

            //         var link = minder.queryCommandValue('HyperLink');

            //         hyperlinkModal.result.then(function(result) {
            //             minder.execCommand('HyperLink', result.url, result.title || '');
            //         });
            //     }
            // }
        }
    }]);