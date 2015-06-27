'use strict';

/* Controllers */

  // Form controller
app.controller('FormCtrl', ['$scope','$http','toaster','$stateParams','$filter','$state', function($scope,$http,toaster,$stateParams,$filter,$state) {
    var id = $stateParams.house_id;
  $scope.datechange = function(date,field){
  var newdate;
  var string = field + "date";
  
  if($scope.temp[string] === date){
  console.log($scope.temp[string] + 'conditionwastruej' + date);
   newdate = date;}
   else{
    console.log($scope.temp[string] + 'ghjghj' + date);
   console.log('sadsdada');
   newdate = date.getUTCFullYear() + '-' + (date.getUTCMonth() + 1) +  '-' + date.getUTCDate();}
   return newdate;
  
  };
   $http.get('api/homigo.php/houses/'+id).then(function (resp) {

  var temp = resp.data.aaData;
  console.log(temp);
  $scope.house = {
  id : temp['house_id'],
  name : temp['house_name'],
  address: temp['house_address'],
  totalrooms: temp['house_no_of_rooms'],
  rent : temp['house_rent'],
  dthbill : temp['house_dthbill'],
  powerbill: temp['house_powerbill'],
  totaldeposit : temp['house_totaldeposit'],
  depositleft : temp['house_totaldepositleft'],
  wifibill : temp['house_wifibill'],
  dthdate : temp['house_dthbilldate'],
  powerdate : temp['house_powerbilldate'],
  wifidate : temp['house_wifibilldate'],
  entrydate : temp['house_entry_date'],
  electricitydate : temp['house_electricitybilldate']


  }
  $scope.temp = {
  id : temp['house_id'],
  name : temp['house_name'],
  address: temp['house_address'],
  totalrooms: temp['house_no_of_rooms'],
  rent : temp['house_rent'],
  dthbill : temp['house_dthbill'],
  powerbill: temp['house_powerbill'],
  totaldeposit : temp['house_totaldeposit'],
  depositleft : temp['house_totaldepositleft'],
  wifibill : temp['house_wifibill'],
  dthdate : temp['house_dthbilldate'],
  powerdate : temp['house_powerbilldate'],
  wifidate : temp['house_wifibilldate'],
  entrydate : temp['house_entry_date'],
  electricitydate : temp['house_electricitybilldate']


  }
 
  console.log($scope.house);
  
});
 
 $scope.update = function(house){
 
 house.electricitydate = $scope.datechange(house.electricitydate,'electricity');
house.entrydate = $scope.datechange(house.entrydate,'entry');
house.wifidate = $scope.datechange(house.wifidate,'wifi');
house.dthdate = $scope.datechange(house.dthdate,'dth');
house.powerdate = $scope.datechange(house.powerdate,'power');
$filter('date')(house.electricitybilldate, 'yyyy-MM-dd');
  $http.put('api/homigo.php/updatehouses/'+house.id,house).
        success(function(data, status) {
            toaster.pop('success','Update House', 'Successfully updated House');
            $state.go('app.house.detail', { house_id: $stateParams.house_id });
          $scope.status = status;
          $scope.data = data;
          console.log($scope.data);
        }).
        error(function(data, status) {
          $scope.data = data || "Request failed";
          $scope.status = status;
      });

console.log(house);

 }

  }]);
 