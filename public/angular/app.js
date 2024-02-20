var myApp = angular.module('myApp', []).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
});

$('#fileToUpload').on("change", function(){
    var t = $(this).val();
    console.log(t)
    var labelText = 'Selected file : ' + t.substr(12, t.length);
    $(this).prev('label').text(labelText);
});



var URL = 'http://127.0.0.1:8001'
myApp.controller('myController', ['$scope', '$http', function($scope, $http ){
    console.log("In myContoller...");

    $scope.newPersona = {};
    $scope.checkedPersona = {};
    $scope.clickedPersona = {};
    $scope.message = "";
    $scope.token = "";
    $scope.errors = "";
    $scope.fileLoaded = '';

    $scope.setFiles = function (element) {
        $scope.$apply(function (scope) {
            $scope.files = [];
            for (var i = 0; i < element.files.length; i++) {
                scope.files.push(element.files[i])
            }
        });
    };

    $scope.uploadFile = function() {
        window._token = document.querySelector("[name=_csrf]").getAttribute("content");
        var fd = new FormData();
        for (var i in $scope.files) {
            fd.append('file', $scope.files[i])
            fd.append('token', window._token)
            console.log($scope.files[i]);
        }



        $http.post(URL+'/doUpload', fd, {
            transformRequest: angular.identity,
            headers: {
                'Content-Type': undefined
            }
        })
            .then(function successCallback(response) {
                    $scope.message = 'File uploaded successfully'
                    getResults()
                    getFiles()
            }, function errorCallback(response) {
                console.log(response)
                $scope.errors = response.data.detail
            });
    };

    $scope.personas = [];

    $scope.savePersona = function(){
        $scope.personas.push($scope.newPersona);
        $scope.newPersona = {};
        $scope.message = "Persona added Successful";
    };

    $scope.selectPersona = function(persona){
        console.log(persona);
        $scope.clickedPersona = persona;
    };

    $scope.updatePersona = function(persona){

    };

    $scope.deletePersona = function(){
        $scope.personas.splice($scope.personas.indexOf($scope.clickedPersona), 1);
    };

    getResults();
    getFiles();

    function getFiles(fileId)
    {
        const route = URL+'/api/excel/filesList';

        $scope.deposers = []
        $http({
            url: route,
            method: 'GET',
            data: {

            }
        }).then(function (res) {
            $scope.deposers = res.data
        });
    }


    $scope.getFile = function(personaId){
        console.log('ajunge')
        const route = URL+'/api/excel/listPersona/'+personaId;

        $scope.personas = []
        $http({
            url: route,
            method: 'GET',
            data: {

            }
        }).then(function (res) {
            $scope.personas = res.data
            $scope.fileLoaded = 'Imported file content loaded!'
        });
    };

    function getResults()
    {
        const route = URL+'/api/excel/listPersonas';

        console.log(route)
        $scope.personas = []
        $http({
            url: route,
            method: 'GET',
            data: {

            }
        }).then(function (res) {
            $scope.personas = res.data
        });
    }


    $scope.updatePersona = function(persona)
    {
        const route = URL+'/api/excel/update';
        $scope.message = '';
        console.log(route)
        $http({
            url: route,
            method: 'POST',
            data: {
                persona: $scope.clickedPersona
            }
        }).then(function (res) {
            $scope.message = "Persona updated Successful";
        });
    }

    $scope.deletePersona = function()
    {
        const route = URL+'/api/excel/delete/'+$scope.clickedPersona.id;
        $scope.message = '';
        console.log(route)
        $http({
            url: route,
            method: 'POST',
            data: {
                persona: $scope.clickedPersona
            }
        }).then(function (res) {
            $scope.clickedPersona = {}
            $scope.message = "Persona deleted Successful";
            getResults();
        });
    }
}]);
