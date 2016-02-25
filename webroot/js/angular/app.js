/**
 * Created by cite on 24.02.16.
 */
var angularMspace = angular.module('mspace', []);

angularMspace.controller('tracklist', function($scope, $http){

    $scope.deleted = [];

    $http.get('/soundfiles/get_music').then(function(tracksResponse) {

        $scope.tracks = tracksResponse.data;
    });

    $scope.deleteTrack = function(trackId) {
        $http.get('/soundfiles/delete/' + trackId ).then(function(tracksResponse) {
            $scope.deleted[trackId] = true;
        });
    };

    $scope.remove = function() {
       $scope.destroy();
    };
});

angularMspace.directive('tracklist', [ function() {

}]);


