<!doctype html>
<!-- Application which allows users to participate in polls -->
<html lang="en" ng-app="votingApp">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="angularjs/css/polls.css">
    <title>Polls</title>
    <script src="angularjs/scripts/angular.js"></script>
    <script src="angularjs/scripts/angular-route.js"></script>
    <script src="angularjs/js/app.js"></script>
    <script src="angularjs/js/controllers.js"></script>
</head>
<body ng-cloak>
    
    <div class="all">
        <h1 class="site_header">Polls</h1>    
    <div ng-view ></div>
    
    </div>
</body>
</html>