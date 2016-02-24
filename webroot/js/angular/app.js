/**
 * Created by cite on 24.02.16.
 */
angular.module('mspace', []);

angular.module('mspace', [])
    .controller('get_all_tracks', function($scope, $http){

        $http.get('/users/get_music').then(function(tracksResponse) {
            $scope.tracks = tracksResponse.data;
        });
    });