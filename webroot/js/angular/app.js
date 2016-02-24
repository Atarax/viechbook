/**
 * Created by cite on 24.02.16.
 */
var angularMspace = angular.module('mspace', []);

angularMspace.controller('get_all_tracks', function($scope, $http){

    $http.get('/soundfiles/get_music').then(function(tracksResponse) {

        $scope.tracks = tracksResponse.data;
    });
});

angularMspace.controller('delet_track', function($scope, $http){
    $http.get('/soundfiles/delete_track').then(function(tracksResponse) {

        $scope.tracks = tracksResponse.data;
    });
});