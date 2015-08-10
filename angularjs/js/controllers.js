(function () {
    'use-strict';
    
    /* Controllers */
    
    SERVER_URL = 'index.php/services/'; // Base url
    
    var votingControllers = angular.module('votingControllers', []);
    
    /*
     * Controller for home/polls template. It loads a list of all polls
     * each time it is loaded.
     */
    votingControllers.controller('pollsCtrl', ['$scope', '$http',
        function ($scope, $http) {
            $scope.ready = false;
            var pollsUrl = SERVER_URL + 'polls';
            
            $http.get(pollsUrl).
                    success(function(response) {
                        $scope.polls = response;
                        $scope.ready = true;
            }).
                    error(function(response, status) {
                        alert("Unable to retrieve polls: " + status);
            });
        }]);
    
    /*
     * Controller for votes template. It loads the poll details each time it
     * runs. When the submit button is clicked the addVote() function is called,
     * which makes a POST request to the web services.
     */
    votingControllers.controller('voteCtrl', ['$scope', '$routeParams', '$http',
        function ($scope, $routeParams, $http) {
            
            $scope.pollID = $routeParams.id;
            $scope.ready = false;

            var detailsUrl = SERVER_URL + 'polls/' + $scope.pollID;

            $http.get(detailsUrl).
                    success(function(response) {
                        $scope.pollDetails = response;
                        $scope.answers = response.answers;
                        $scope.choice = response.answers[0];
                        $scope.ready = true;
            }).
                    error(function(response, status) {
                        alert("Unable to retrieve poll details: " + status);
            });
            
            $scope.addVote = function () {
                // Get answer number
                var vote = $scope.answers.indexOf($scope.choice) + 1;
                var voteUrl = SERVER_URL +'votes/'+ $scope.pollID + '/' + vote;
                
                $http.post(voteUrl).
                        error(function(response, status) {
                            alert("Unable to submit vote: " + status);
                });
            };
        }]);
    
    /*
     * Controller for the admin view of the polls template. It allows users
     * to create, edit and delete polls. The deletePoll() function is called
     * when a delete hyperlink is selected, and it sends a DELETE request
     * to the web service. When a poll is deleted, the $route module is used
     * to reload the page without the deleted poll.
     */
    votingControllers.controller('pollsAdminCtrl', ['$scope', '$http', '$route',
        function ($scope, $http, $route) {
            $scope.ready = false;
            
            var url = SERVER_URL + 'polls';
            $http.get(url).
                    success(function(response) {
                        $scope.polls = response;
                        $scope.ready = true;
            }).
                    error(function(response, status) {
                        alert("Unable to retrieve polls: " + status);
            });
            
            $scope.deletePoll = function (id) {
                var url = SERVER_URL + 'polls/' + id;
                $http.delete(url).
                        success(function() {
                            $route.reload();
                }).
                        error(function(response, status) {
                            alert("Unable to delete poll: " + status);
                });
            };
        }]);
    
    /*
     * Controller for the edit poll view. It is used for both creating and
     * editing polls, and $location.path() is used to determine which it is
     * being used for. The addAnswer() and deleteAnswer() functions are only
     * used by the controller when it is being used in create poll mode.
     * The validPoll() function checks the current values for the poll, and
     * only allows a poll to be submitted if it meets the required conditions.
     */
    votingControllers.controller('editPollCtrl', ['$scope', '$routeParams',
                                                  '$http', '$location',
        function ($scope, $routeParams, $http, $location) {
            $scope.id = $routeParams.id;
            $scope.ready = false;
            $scope.answer = "";
            var detailsUrl = SERVER_URL + 'polls/' + $scope.id;
            
            // Determine whether this is a new or existing poll
            if ($location.path() === "/admin/polls/create") {
                $scope.create = true;
                $scope.poll = {title: "", question: "", answers: []};
                $scope.answers = []; // initialise poll values
            } else {
                $scope.create = false;
                getPollDetails(); //load existing details
            }
            
            $scope.addAnswer = function() {
                // Check whether answer is already in list
                $scope.debug = $scope.answer;
                var index = $scope.answers.indexOf($scope.answer);
                if ($scope.answer !== "" && index === -1) {
                    $scope.answers.push($scope.answer);
                    $scope.answer = "";
                }
            };
            
            $scope.deleteAnswer = function(answer) {
                var index = $scope.answers.indexOf(answer);
                $scope.answers.splice(index, 1);
            };
            
            $scope.submitPoll = function() {
                if (validPoll() && confirm("Finish editing and submit?")) {
                    $scope.poll.answers = $scope.answers;
                    var data = $scope.poll; // poll object to be sent
                    
                    if($scope.create) {
                        createPoll(data); // use http POST
                    } else { // path is /admin/polls/id/edit
                        replacePoll(data); // use http PUT
                    }
                }
            };
            
            function getPollDetails() {
                $http.get(detailsUrl).
                    success(function(response) {
                        $scope.poll = response;
                        $scope.answers = response.answers;
                        $scope.ready = true;
            }).
                    error(function(response, status) {
                        alert("Unable to retrieve poll details: " + status);
            });
            }
            
            function createPoll(data) {
                var pollUrl = SERVER_URL + 'polls';
                $http.post(pollUrl, data).
                            success(function() {
                                $location.path('/admin/polls');
                    }).
                            error(function(response, status) {
                                alert("Unable to submit poll: " + status);
                    });
            }
            
            function replacePoll(data) {
                var pollUrl = SERVER_URL + 'polls/' + $scope.id;
                $http.put(pollUrl, data).
                            success(function() {
                                $location.path('/admin/polls');
                    }).
                            error(function(response, status) {
                                alert("Unable to submit poll: " + status);
                    });
            }
            
            /*
             * Only returns valid if (title, question) are both not empty,
             * and there are at least two answers.
             */
            function validPoll() {
                var valid = false;
                // check each poll attribute
                if ($scope.poll.title === "") {
                    alert("Invalid title");
                } else if ($scope.poll.question === "") {
                    alert("Invalid question");
                } else if ($scope.answers.length < 2) {
                    alert("Poll needs at least two answers");
                } else {
                    valid = true;
                }
                return valid;
            }
        }]);
    
    /*
     * Controller for the admin view for votes. It requests the list of
     * answers and votes separately, and then combines them into a single array
     * for use in ng-repeat. The deleteVotes() function makes an AJAX request
     * to the web service, which deletes all the votes then reloads the page.
     */
    votingControllers.controller('voteAdminCtrl', ['$scope', '$routeParams',
                                                   '$http', '$route',
        function ($scope, $routeParams, $http, $route) {
            
            $scope.pollID = $routeParams.id;
            
            var voteUrl = SERVER_URL + 'votes/' + $scope.pollID;
            var detailsUrl = SERVER_URL + 'polls/' + $scope.pollID;
            
            $scope.ready = false;
           
            $http.get(detailsUrl).
                    success(function(response) {
                        $scope.pollDetails = response;
                        var answers = response.answers;
                        
                        $http.get(voteUrl).
                    success(function(response) {
                        
                        var votes = response;
                        var list = []; // List containing answers and votes
                        var len = votes.length;
                        
                        for(var i=0; i<len; i++) {
                            list.push({answer: answers[i],
                                       total: votes[i].votes});
                        }
                        $scope.votes = list;
                        $scope.ready = true;
                    });
            });
            
            $scope.deleteVotes = function () {
                if (confirm("Are you sure you wish to delete all votes?")) {
                    $http.delete(voteUrl).
                            success(function() {
                                $route.reload();
                    }).
                            error(function(response, status) {
                                alert("Unable to reset votes: " + status);
                    });
                }
            };
            
        }]);
}())