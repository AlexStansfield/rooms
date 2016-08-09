var calendarApp = angular.module('calendarApp', ["xeditable"]);

calendarApp.run(function(editableOptions) {
    editableOptions.theme = 'bs3';
});

calendarApp.controller('CalendarCtrl', function ($scope, $http, $filter) {
    $scope.startDate = 1;
    $scope.roomTypes = [];
    $scope.dates = [];
    $scope.calendar = [];
    $scope.bulkUpdateForm = {};
    $scope.defaultRoomTypes = [];

    $scope.months = [
        {value: 1, text: 'January'},
        {value: 2, text: 'February'},
        {value: 3, text: 'March'},
        {value: 4, text: 'April'},
        {value: 5, text: 'May'},
        {value: 6, text: 'June'},
        {value: 7, text: 'July'},
        {value: 8, text: 'August'},
        {value: 9, text: 'September'},
        {value: 10, text: 'October'},
        {value: 11, text: 'November'},
        {value: 12, text: 'December'}
    ];
    $scope.years = [
        {value: 2016, text: '2016'},
        {value: 2017, text: '2017'},
        {value: 2018, text: '2018'}
    ];

    // Set the Starting View
    var today = new Date();
    $scope.month = today.getMonth()+1;
    $scope.year = today.getFullYear();

    // Get the default room types
    $http.get('/api/room_type').success(function (data) {
        $scope.defaultRoomTypes = data;
    });

    // Watch if the Month changes in order to Refresh Calendar
    $scope.$watch('month', function (newValue, oldValue, scope) {
        if (newValue == oldValue) {
            return;
        }
        scope.startDate = 1;
        scope.refreshCalendar();
    });

    // Watch if the Year changes in order to Refresh Calendar
    $scope.$watch('year', function (newValue, oldValue, scope) {
        if (newValue == oldValue) {
            return;
        }
        scope.startDate = 1;
        scope.refreshCalendar();
    });

    // Watch if the Calendar changes in order to find Dates and RoomTypes
    $scope.$watch('calendar', function (newCalendar, oldCalendar) {
        if (newCalendar == oldCalendar) {
            return;
        }
        // Get Dates and RoomTypes from Calendar
        var calendar = newCalendar.calendar;
        var dates = [];
        var roomTypes = [];
        for (var date in calendar) {
            if ($filter('date')(date, 'd') && (dates.length < 10)) {
                dates.push(date);
                for (var roomType in calendar[date]) {
                    if (roomTypes.indexOf(roomType) == -1) {
                        roomTypes.push(roomType);
                    }
                }
            }
        }
        $scope.dates = dates;
        $scope.roomTypes = roomTypes;
    }, true);

    // Method for Submitting Bulk Update
    $scope.bulkSubmit = function(bulkUpdateForm) {
        var data = {

        };
        return $http.post('/api/calendar', data);
    };

    // Method for Reseting Bulk Update form
    $scope.bulkReset = function() {
        $scope.bulkUpdateForm = {};
    };

    // Method to Update Price for a particular room on a day
    $scope.updatePrice = function(roomType, date, price) {
        return $http.post('/api/room/price', {room_type: roomType, date: date, price: parseFloat(price)})
            .success(function (data) {
                $scope.refreshCalendar();
            });
    };

    // Method to Update Availability for a particular room on a day
    $scope.updateAvailability = function(roomType, date, availability) {
        return $http.post('/api/room/availability', {room_type: roomType, date: date, availability: parseInt(availability)})
            .success(function (data) {
                $scope.refreshCalendar();
            });
    };

    // Display the chosen month
    $scope.showMonth = function() {
        var selected = $filter('filter')($scope.months, {value: $scope.month});
        return ($scope.month && selected.length) ? selected[0].text : 'Not set';
    };

    // Display the chosen year
    $scope.showYear = function() {
        var selected = $filter('filter')($scope.years, {value: $scope.year});
        return ($scope.year && selected.length) ? selected[0].text : 'Not set';
    };

    // Display the Room Type
    $scope.showRoomType = function(type) {
        var selected = $filter('filter')($scope.defaultRoomTypes, {type: type});
        return (selected.length) ? selected[0].name : 'Not set';
    };

    // Refresh the calendar with current Month and Year
    $scope.refreshCalendar = function() {
        var dateFrom = new Date($scope.year, $scope.month - 1, 1);
        var dateTo = new Date($scope.year, $scope.month, 0);

        var url = '/api/calendar/from/' + $filter('date')(dateFrom, 'yyyy-MM-dd') + '/to/' + $filter('date')(dateTo, 'yyyy-MM-dd');

        $http.get(url).success(function (data) {
            $scope.calendar = data;
        });
    };

    $scope.refreshCalendar();
});
