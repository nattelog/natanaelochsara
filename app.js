var app = angular.module('wedding', []);

app.controller('globalController', function($scope){
    
});

app.controller('adminPanelController', function($scope){
    
});

app.directive("phpAlert", function(){
    return {
        restrict: 'E',
        templateUrl: 'php-alert.html'
    };
});