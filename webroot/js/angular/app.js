/**
 * Created by cite on 24.02.16.
 */
var angularMspace = angular.module('mspace', []);


angularMspace.factory('musicPlayerService', ['$window', function(window) {
    var musicPlayer = {};
    var registeredHtml5Player = null;

    musicPlayer.registerHtml5Player = function(player) {
        registeredHtml5Player = player;
    };

    musicPlayer.changeTrack = function(track) {
        if(registeredHtml5Player != null) {
            registeredHtml5Player.src = track.filename;
            registeredHtml5Player.load();
            //registeredHtml5Player.play();
        }
    };
    /*
    var msgs = [];
    return function(msg) {
        msgs.push(msg);
        window.alert(msgs.join("\n"));
        msgs = [];
    };*/

    return musicPlayer;
}]);

angularMspace.controller('musicPlayer', function($scope, $http, musicPlayerService){

    $scope.deleted = [];
    $scope.thistest = 'abs';

    $http.get('/soundfiles/get_music').then(function(tracksResponse) {
        var tracks = tracksResponse.data;

        if(tracks != null && tracks[0] != null) {
            $scope.currentTrack = tracks[0];
            musicPlayerService.changeTrack(tracks[0]);
        }

        $scope.tracks = tracks;
    });


    $scope.changeTrack = function(track) {
        $scope.currentTrack = track;
        musicPlayerService.changeTrack(track);
    };

    $scope.deleteTrack = function(track) {
        var userWantsToDelete = confirm("Do you really want to delete \"" + track.name + "\"?");

        if( userWantsToDelete ) {
            var trackId = track.id;
            $http.get('/soundfiles/delete/' + trackId ).then(function(tracksResponse) {
                $scope.deleted[trackId] = true;
                $scope.thistest = 'xyz';
            });
        }
    };
});

angularMspace.directive('musicplayer', function(musicPlayerService) {
    //'<audio>Hello World{{thistest}}!!</audio>'

    return {
        restrict: 'AE',
        replace: 'true',
        template: '<div>' +
            '{{currentTrack.name}}' +
            '<br>' +
            '<audio class="audio-player" controls></audio>' +
        '</div>',
        link: function(scope, elem, attrs) {
            var player = elem.children('.audio-player');
            musicPlayerService.registerHtml5Player(player[0]);
            console.debug(scope.thistest);
        }
    };
});

angularMspace.directive('tracklist', [ function() {

}]);


