var app = angular.module('cms', ['ngFlash', 'angularMoment'])
.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.ngEnter);
                });
 
                event.preventDefault();
            }
        });
    };
})
.directive('capitalize', function() {
   return {
     require: 'ngModel',
     link: function(scope, element, attrs, modelCtrl) {
        var capitalize = function(inputValue) {
           if(inputValue == undefined) inputValue = '';
           var capitalized = inputValue.toUpperCase();
           if(capitalized !== inputValue) {
              modelCtrl.$setViewValue(capitalized);
              modelCtrl.$render();
            }         
            return capitalized;
         }
         modelCtrl.$parsers.push(capitalize);
         capitalize(scope[attrs.ngModel]);  // capitalize initial value
     }
   };
})
.controller('NewIncidentController', ['$scope', '$http', '$window', 'Flash', '$timeout', function($scope, $http, $window, Flash, $timeout){
    var $this = this;
    this.id = 0;
    this.title = '';
    this.name = '';
    this.tel = '';
    this.description = '';
    this.categories = '';
    this.address = '';
    this.hasAddress = false;
    this.step = 0;
    this.selectedAddressIndex = 0;
    this.addressResults = [];
    this.incidents = [];
    this.reporters = [];
    this.reporterId = 0;
    this.hasTel = false;
    this.geocodingLoading = false;
    this.telLoading = false;
    
    this.resources = [];
    this.records = [];
    
    this.getLatLong = function(){
        var geocoder = $window.geocoder;
        this.geocodingLoading = true;
        geocoder.geocode({
            address: this.address,
            componentRestrictions: {
                country: 'SG'
            }
        }, function(results, status) {
            $this.geocodingLoading = false;
            if(status !== google.maps.GeocoderStatus.OK)
                return;
            $this.selectedAddressIndex = 0;
            $this.addressResults = [];
            if(results.length >= 1){
                $scope.$apply( function(){
                    $this.addressResults = $this.convertResults(results);
                    $this.addMarkers();
                    $this.showOnMap(0);
                    $this.hasAddress = true;
                });
            }
        });
    };
    
    this.fetchCasesNearMe = function(){
        $http({
            url: '/incident/find-incidents',
            method: 'POST',
            data: {
                lat: this.addressResults[this.selectedAddressIndex].lat,
                lng: this.addressResults[this.selectedAddressIndex].lng,
            },
        }).then(function successCallback(response) {
            console.log(response.data);
            var obj = response.data;
            if (obj.success == 'success') {
                $this.incidents = JSON.parse(obj.data.incidents);
                if($this.incidents.Incidents){
                    $this.incidents = $this.incidents.Incidents;
                    for(var i=0; i<$this.incidents.length; i++){
                        $this.incidents[i].dateTime = new Date($this.incidents[i].CreatedAt);
                        console.log($this.incidents[i]);
                    }
                }else
                    $this.incidents = 1;
                if (obj.message != 'success') {
                    Flash.create('success', obj.message, 0, {class: 'customAlert'});
                }
                if (obj.redirect != false) {
                    if (obj.message != 'success') {
                        $timeout(function () {
                            $window.location.href = obj.redirect;
                        }, 3000);
                    } else {
                        $window.location.href = obj.redirect;
                    }
                }
            } else {
                console.log(response.data);
                var message = 'We encountered an error in handling your submission. (Error: ' + obj.code + ':' + obj.message + ')';
                Flash.create('danger', message, 'customAlert');
            }
        }, function errorCallback(response) {
            var message = 'We encountered an error in handling your submission. (Error: Please check your internet connectivity)';
            console.log(response.data);
            Flash.create('danger', message, 'customAlert');
        });
    };
    
    this.selectIncident = function(selectedId){
        this.id = selectedId;
    };
    
    this.selectReporter = function(selectedId){
        this.reporterId = selectedId;
    };
    
    this.selectResource = function(resource){
        resource.selected = !resource.selected;
    };
    
    this.convertResults = function(results){
        var t = [];
        for(i=0; i<results.length; i++){
            t.push({'formatted_address': results[i].formatted_address,
                    'lat': results[i].geometry.location.lat(),
                    'lng': results[i].geometry.location.lng(),
                    'selected': false
                   });
        }
        return t;
    };
    
    this.addMarkers = function(){
        var i;
        for(i=0; i<$window.markers.length; i++){
            $window.markers[i].setMap(null);
        }
        $window.markers = [];
        for(i=0; i<this.addressResults.length; i++){
            var a = this.addressResults[i];
            var position = {lat: a.lat, lng: a.lng};
            var marker = new google.maps.Marker({
                position: position,
                map: $window.map,
                title: a.formatted_address,
                draggable: true,
            });
            marker.addListener('dragend',function(event) {
                $this.updateLatLng(event.latLng.lat(), event.latLng.lng())
            });
            $window.markers.push(marker);
        }
    };
    
    this.updateLatLng = function(lat, lng){
        this.addressResults[this.selectedAddressIndex].lat = lat;
        this.addressResults[this.selectedAddressIndex].lng = lng;
        
        this.fetchCasesNearMe();
    };
    
    this.showOnMap = function(index){
        this.selectedAddressIndex = index;
        var selectedAddress = this.addressResults[index];
        console.log(selectedAddress);
        for(var i=0; i<this.addressResults.length; i++){
            this.addressResults[i].selected = false;
        }
        selectedAddress.selected = true;
        var position = {lat: selectedAddress.lat, lng: selectedAddress.lng};
        $window.map.setCenter(position);
        $window.map.setZoom(15);
        
        this.fetchCasesNearMe();
    };
    
    this.createNewIncident = function(){
        if(this.id != 0){
            this.step = 1;
            return;
        }
        $http({
            url: './incident/new-incident',
            method: 'POST',
//            headers : {'Content-Type': 'application/x-www-form-urlencoded'},
            data: {
                title: this.title,
                address: this.address,
                lat: this.addressResults[this.selectedAddressIndex].lat,
                lng: this.addressResults[this.selectedAddressIndex].lng,
            },
        }).then(function successCallback(response) {
            console.log(response.data);
            var obj = response.data;
            if (obj.success == 'success') {
                $this.step = 1;
                $this.id = obj.data.id;
                if (obj.message != 'success') {
                    Flash.create('success', obj.message, 3000, {class: 'customAlert'});
                }
                if (obj.redirect != false) {
                    if (obj.message != 'success') {
                        $timeout(function () {
                            $window.location.href = obj.redirect;
                        }, 3000);
                    } else {
                        $window.location.href = obj.redirect;
                    }
                }
            } else {
                console.log(response.data);
                var message = 'We encountered an error in handling your submission. (Error: ' + obj.code + ':' + obj.message + ')';
                Flash.create('danger', message, 3000, {class: 'customAlert'});
            }
        }, function errorCallback(response) {
            var message = 'We encountered an error in handling your submission. (Error: Please check your internet connectivity)';
            console.log(response.data);
            Flash.create('danger', message, 3000, {class: 'customAlert'});
        });
    };
    
    this.createNewReporter = function(){
        $http({
            url: './incident/new-reporter',
            method: 'POST',
            data: {
                id: this.id,
                reporterId: this.reporterId,
                name: this.name,
                tel: this.tel,
                description: this.description
            },
        }).then(function successCallback(response) {
            console.log(response.data);
            var obj = response.data;
            if (obj.success == 'success') {
                $this.step = 2;
                $this.reporterId = obj.data.reporterId;
                $this.getCategoriesAndResource();
                if (obj.message != 'success') {
                    Flash.create('success', obj.message, 3000, {class: 'customAlert'});
                }
                if (obj.redirect != false) {
                    if (obj.message != 'success') {
                        $timeout(function () {
                            $window.location.href = obj.redirect;
                        }, 3000);
                    } else {
                        $window.location.href = obj.redirect;
                    }
                }
            } else {
                console.log(response.data);
                var message = 'We encountered an error in handling your submission. (Error: ' + obj.code + ':' + obj.message + ')';
                Flash.create('danger', message, 3000, {class: 'customAlert'});
            }
        }, function errorCallback(response) {
            var message = 'We encountered an error in handling your submission. (Error: Please check your internet connectivity)';
            console.log(response.data);
            Flash.create('danger', message, 3000, {class: 'customAlert'});
        });
    };
    
    this.createNewCategoriesAndResource = function(){
        $http({
            url: './incident/new-categories-and-resource',
            method: 'POST',
            data: {
                id: this.id,
                reporterid: this.reporterId,
                categories: this.categories,
                resource: this.resources
            },
        }).then(function successCallback(response) {
            console.log(response.data);
            var obj = response.data;
            if (obj.success == 'success') {
                $this.step = 3;
                if (obj.message != 'success') {
                    Flash.create('success', obj.message, 3000, {class: 'customAlert'});
                }
                if (obj.redirect != false) {
                    if (obj.message != 'success') {
                        $timeout(function () {
                            $window.location.href = obj.redirect;
                        }, 3000);
                    } else {
                        $window.location.href = obj.redirect;
                    }
                }
            } else {
                var message = 'We encountered an error in handling your submission. (Error: ' + obj.code + ':' + obj.message + ')';
                Flash.create('danger', message, 3000, {class: 'customAlert'});
            }
        }, function errorCallback(response) {
            var message = 'We encountered an error in handling your submission. (Error: Please check your internet connectivity)';
            console.log(response.data);
            Flash.create('danger', message, 3000, {class: 'customAlert'});
        });
    };
    
    this.findReporterByTel = function(){
        this.hasTel = false;
        this.telLoading = true;
        $http({
            url: './incident/find-reporters',
            method: 'POST',
            data: {
                tel: this.tel
            },
        }).then(function successCallback(response) {
            $this.telLoading = false;
            var obj = response.data;
            if (obj.success == 'success') {
                $this.hasTel = true;
                $this.reporters = JSON.parse(obj.data.reporters);
                if($this.reporters.Reporters)
                    $this.reporters = $this.reporters.Reporters;
                else
                    $this.reporters = 1;
                if (obj.message != 'success') {
                    Flash.create('success', obj.message, 3000, {class: 'customAlert'});
                }
                if (obj.redirect != false) {
                    if (obj.message != 'success') {
                        $timeout(function () {
                            $window.location.href = obj.redirect;
                        }, 3000);
                    } else {
                        $window.location.href = obj.redirect;
                    }
                }
            } else {
                console.log(response.data);
                var message = 'We encountered an error in handling your submission. (Error: ' + obj.code + ':' + obj.message + ')';
                Flash.create('danger', message, 3000, {class: 'customAlert'});
            }
        }, function errorCallback(response) {
            $this.telLoading = true;
            var message = 'We encountered an error in handling your submission. (Error: Please check your internet connectivity)';
            console.log(response.data);
            Flash.create('danger', message, 3000, {class: 'customAlert'});
        });
    };
    
    this.getCategoriesAndResource = function(){
        $http({
            url: './incident/get-categories-and-resources',
            method: 'POST',
            data: {
                id: this.id
            },
        }).then(function successCallback(response) {
            var obj = response.data;
            if (obj.success == 'success') {
                console.log(obj);
                $this.categories = JSON.parse(obj.data.categories);
                if($this.categories.Categories)
                    $this.categories = $this.categories.Categories.join(',');
                else
                    $this.categories = "";
                if (obj.message != 'success') {
                    Flash.create('success', obj.message, 3000, {class: 'customAlert'});
                }
                $this.resources = JSON.parse(obj.data.resources);
                if($this.resources.Resources)
                    $this.resources = $this.resources.Resources;
                else
                    $this.resources = [];
                for(var i=0; i<$this.resources.length; i++){
                    $this.resources[i].selected = false;
                }
                
                $this.records = JSON.parse(obj.data.records);
                console.log($this.records);
                
                if (obj.redirect != false) {
                    if (obj.message != 'success') {
                        $timeout(function () {
                            $window.location.href = obj.redirect;
                        }, 3000);
                    } else {
                        $window.location.href = obj.redirect;
                    }
                }
            } else {
                console.log(response.data);
                var message = 'We encountered an error in handling your submission. (Error: ' + obj.code + ':' + obj.message + ')';
                Flash.create('danger', message, 3000, {class: 'customAlert'});
            }
        }, function errorCallback(response) {
            $this.telLoading = true;
            var message = 'We encountered an error in handling your submission. (Error: Please check your internet connectivity)';
            console.log(response.data);
            Flash.create('danger', message, 3000, {class: 'customAlert'});
        });
    };
}])