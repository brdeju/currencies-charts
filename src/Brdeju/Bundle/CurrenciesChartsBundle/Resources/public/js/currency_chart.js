$(function () {
    var intervalValues = {
        'Daily': 86400000,
        'Weekly': 604800000, 
        'Monthly': 2678400000
    };
    
    $(document).ready(function() {
        var date = new Date();
        var weekBefore = date.setDate(date.getDate() - 7);
        $('#date-start').datetimepicker({defaultDate: weekBefore, format: 'MM/DD/YYYY'});
        $('#date-end').datetimepicker({defaultDate: "now", format: 'MM/DD/YYYY'});
        
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

        $("#date-start").on("dp.change", function (e) {
            refreshChart();
        });
        $("#date-end").on("dp.change", function (e) {
            refreshChart();
        });
        refreshChart();
    });
});