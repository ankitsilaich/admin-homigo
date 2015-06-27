app.controller('hCtrl', ['$scope', '$http','$stateParams','$q','$state', function($scope, $http, $stateParams,$q,$state) {
   
var deferredAbort = $q.defer();
    $http.get('api/homigo.php/houses',{timeout : deferredAbort.promise}).
  then(function(response) {
    $scope.house = response.data.aaData;
    console.log(response);
  });
   
 
}]);