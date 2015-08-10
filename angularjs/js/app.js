(function () {
    'use-strict';

    /* App Module */
    var votingApp = angular.module('votingApp',
                    ['ngRoute', 'votingControllers']);
    
    /* Routing configuration */
    votingApp.config(['$routeProvider',
        function ($routeProvider) {
            $routeProvider.
                    when('/polls', {// Main page. Shows list of polls
                        templateUrl: 'angularjs/partials/polls.html',
                        controller: 'pollsCtrl'
                    }).
                    when('/polls/:id', {// Voting view
                        templateUrl: 'angularjs/partials/vote.html',
                        controller: 'voteCtrl'
                    }).
                    when('/admin/votes/:id', {// Admin view. Shows votes. Can delete votes
                        templateUrl: 'angularjs/partials/admin_vote.html',
                        controller: 'voteAdminCtrl'
                    }).
                    when('/admin/polls', {// Admin view. Can edit polls
                        templateUrl: 'angularjs/partials/admin_polls.html',
                        controller: 'pollsAdminCtrl'
                    }).
                    when('/admin/polls/create', {// Create new polls
                        templateUrl: 'angularjs/partials/edit_poll.html',
                        controller: 'editPollCtrl'
                    }).
                    when('/admin/polls/:id/edit', {// Edit existing polls
                        templateUrl: 'angularjs/partials/edit_poll.html',
                        controller: 'editPollCtrl'
                    }).
                    when('/about', {// About page. Details about website
                        templateUrl: 'angularjs/partials/about.html'
                    }).
                    otherwise({
                        redirectTo: '/polls'
            });
        }]);
}())
