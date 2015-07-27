$(function () {
    var intervalValues = {
        'Daily': 86400000,
        'Weekly': 604800000, 
        'Monthly': 2678400000
    };
    
    $(document).ready(function() {
        var date = new Date();
        var weekBefore = date.setDate(date.getDate() - 7);
        $('#date-start').datetimepicker({language: 'pl', initialDate: "-7d", format: 'mm/dd/yyyy', minView: 2});
        $('#date-end').datetimepicker({language: 'pl', format: 'mm/dd/yyyy', minView: 2});
        
        Highcharts.setOptions({
            lang: {
                months: i18n.charts.months,
                shortMonths: i18n.charts.shortMonths,
                weekdays: i18n.charts.weekdays,
                loading: i18n.charts.loading,
                noData: i18n.charts.noData
            },
        });
        var options = {
            title: {
                text: ''
            },
            xAxis: {
                tickInterval: 24 * 3600 * 1000,
                tickWidth: 0,
                gridLineWidth: 1,
                labels: {
                    align: 'left',
                    x: 3,
                    y: -3
                },
                type: 'datetime'
            },
            yAxis: [{
                    title: {
                        text: null
                    },
                    labels: {
                        align: 'left',
                        x: 3,
                        y: 16
                    },
                    showFirstLabel: false
                }],
            legend: {
                align: 'left',
                verticalAlign: 'top',
                y: 20,
                floating: true,
                borderWidth: 0
            },
            tooltip: {
                shared: true,
                crosshairs: true
            },
            plotOptions: {},
            series: []
        };

        function refreshChart() {
            if( typeof CURRENCY === "undefined" || CURRENCY === "" ) {
                return ;
            }
            // Get the CSV and create the chart
            var jqxhr = $.ajax({
                url: Routing.generate('currency_data', {currency: CURRENCY}),
                method: 'GET',
                data: {'start_date':$('#date-start').find("input").val(), 'end_date':$('#date-end').find("input").val()}
            })
            .done(function( response ) {
                var seriesArr = [];
                $.each(response['result'], function (key, data) { // prepare series for diffrent currencies
                    var series = {
                        name: key,
                        data: []
                    };
                    $.each(data, function (date, rate) {
                        series.data.push({
                            x: date*1000,
                            y: rate.average_rate
                        });
                    });
                    seriesArr.push(series);
                });

                options['series'] = seriesArr;
                options['title']['text'] = response['title'];
                options['xAxis']['tickInterval'] = intervalValues[response['tickInterval']];
                $('#chart-container').highcharts(options);
            });
        };

        $("#date-start").on('changeDate', function (e) {
            refreshChart();
        });
        $("#date-end").on('changeDate', function (e) {
            refreshChart();
        });
        refreshChart();
    });
});