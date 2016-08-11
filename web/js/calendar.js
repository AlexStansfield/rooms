var calendarApp = angular.module('calendarApp', ["xeditable"]);

calendarApp.run(function(editableOptions) {
    editableOptions.theme = 'bs3';
});

calendarApp.controller('CalendarCtrl', function ($scope, $http, $filter) {
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
    $scope.refineDays = [
        {value: 1, day: "monday"},
        {value: 2, day: "tuesday"},
        {value: 4, day: "wednesday"},
        {value: 8, day: "thursday"},
        {value: 16, day: "friday"},
        {value: 32, day: "saturday"},
        {value: 64, day: "sunday"}
    ];

    // Set the Starting View
    var today = new Date();
    $scope.period = {year: today.getFullYear(), month: today.getMonth()+1}
    $scope.startDate = today.getDate();

    // Get the default room types
    $http.get('/api/room_type').success(function (data) {
        $scope.roomTypes = data;
    });

    $scope.$watch('bulkUpdateForm["refineDays"]', function (newValue, oldValue, scope) {
        for (var day in newValue) {
            if (newValue[day] == true) {
                scope.bulkUpdateForm.refineGroup = null;
                return;
            }
        }
    }, true);

    $scope.$watch('bulkUpdateForm["refineGroup"]', function (newValue, oldValue, scope) {
        if (null != newValue) {
            scope.bulkUpdateForm.refineDays = {}
        }
    });

    // Watch if the Month and Year changes in order to Refresh Calendar
    $scope.$watchGroup(['period["year"]', 'period["month"]'], function (newValue, oldValue, scope) {
        if (newValue == oldValue) {
            return;
        }

        $scope.startDate = 1;
        scope.setPeriodDates();
        scope.refreshCalendar().then(function () {
            $scope.updateDateView();
        });
    });

    // Method for Submitting Bulk Update
    $scope.bulkSubmit = function(bulkUpdateForm) {
        var availability;
        var price;

        if (!bulkUpdateForm.availability) {
            availability = null;
        } else {
            availability = bulkUpdateForm.availability
        }

        if (!bulkUpdateForm.price) {
            price = null;
        } else {
            price = bulkUpdateForm.price
        }

        var data = {
            room_type: bulkUpdateForm.roomType,
            date_from: bulkUpdateForm.dateFrom,
            date_to: bulkUpdateForm.dateTo,
            price: price,
            availability: availability,
            day_refine: $scope.getRefineValue(bulkUpdateForm)
        };

        return $http.post('/api/calendar', data)
            .then(function() {
                $scope.refreshCalendar();
            },
            function(data) {
                var errorMessage = data.data.message;

                for (field in data.data.errors) {
                    errorMessage = errorMessage + "\n" + field + ': ' + data.data.errors[field];
                }

                alert (errorMessage);
            });
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
        var selected = $filter('filter')($scope.months, {value: $scope.period.month});
        return ($scope.period.month && selected.length) ? selected[0].text : 'Not set';
    };

    // Display the chosen year
    $scope.showYear = function() {
        var selected = $filter('filter')($scope.years, {value: $scope.period.year});
        return ($scope.period.year && selected.length) ? selected[0].text : 'Not set';
    };

    // Work out the refine value
    $scope.getRefineValue = function(bulkUpdateForm) {
        if (bulkUpdateForm.refineGroup) {
            return parseInt(bulkUpdateForm.refineGroup);
        }

        var refine = 0;
        for (var day in bulkUpdateForm.refineDays) {
            if (bulkUpdateForm.refineDays[day]) {
                var selected = $filter('filter')($scope.refineDays, {day: day});
                refine = (selected.length) ? refine + parseInt(selected[0].value) : refine;
            }
        }

        return (0 != refine) ? refine : null;
    };

    // Refresh the calendar with current Month and Year
    $scope.refreshCalendar = function() {
        var url = '/api/calendar/from/' + $scope.formatDate($scope.period.dateFrom) + '/to/' + $scope.formatDate($scope.period.dateTo);

        return $http.get(url).success(function (data) {
            $scope.calendar = data;
        });
    };

    // Set the Start, End and Dates array in the period object
    $scope.setPeriodDates = function() {
        $scope.period.dateFrom = new Date($scope.period.year, $scope.period.month - 1, 1);
        $scope.period.dateTo = new Date($scope.period.year, $scope.period.month, 0);

        var dates = [];
        var currentDate = new Date($scope.period.dateFrom.valueOf());
        while (currentDate <= $scope.period.dateTo) {
            dates.push($scope.formatDate(currentDate))
            currentDate.setDate(currentDate.getDate() + 1);
        }

        $scope.period.dates = dates;
    }

    // Update the Date View
    $scope.updateDateView = function () {
        var dates = [];

        var start = $scope.startDate - 1;
        var end = start + 10;
        for (var k in $scope.period.dates) {
            if ((k >= start) && (k < end)) {
                dates.push($scope.period.dates[k]);
            }
        }

        $scope.dates = dates;
    }

    // Method to format the date how we need it
    $scope.formatDate = function(date) {
        return $filter('date')(date, 'yyyy-MM-dd');
    }

    // Method to move forward a page of dates in calendar
    $scope.nextDates = function() {
        var newStartDate = $scope.startDate + 10;

        if (newStartDate > $scope.period.dates.length) {
            return;
        }

        $scope.startDate = newStartDate;
        $scope.updateDateView();
    };

    // Method to move back a page of dates in calendar
    $scope.previousDates = function() {
        var newStartDate = $scope.startDate - 10;

        if (newStartDate < 1) {
            newStartDate = 1;
        }

        $scope.startDate = newStartDate;
        $scope.updateDateView();
    };

    $scope.setPeriodDates();
    $scope.refreshCalendar();
    $scope.updateDateView();
});
