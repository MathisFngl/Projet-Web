google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var data = google.visualization.arrayToDataTable([
    ['1', 20, 28, 38, 45],
    ['2', 31, 38, 55, 66],
    ['3', 50, 55, 77, 80],
    ['4', 77, 77, 66, 50],
    ['5', 68, 66, 22, 15],
    ['6', 68, 22, 12, 15],
    ['7', 9, 12, 41, 15],
    ['8', 29, 41, 39, 45],
    ['9', 68, 39, 73, 85],
    ['10', 29, 73, 108, 110],
    ['11', 98, 108, 159, 183],
    ['12', 108, 159, 148, 164],], true);

    var options = {
        legend:'none',
        candlestick: {
        fallingColor: { strokeWidth: 0, fill: '#a52714' },
         risingColor: { strokeWidth: 0, fill: '#0f9d58' }},
        backgroundColor : { strokeWidth: 0, fill: '#212b36' },
        chartArea: {'width': '90%', 'height': '85%'},
        };

        var chart = new google.visualization.CandlestickChart(document.getElementById('MainTrade'));

        chart.draw(data, options);
}

google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawBasic);

function drawBasic() {
    var data = new google.visualization.DataTable();
    data.addColumn('number', 'X');
    data.addColumn('number', 'Value');

    data.addRows([
        [0, 0], [1, 10], [2, 23], [3, 17], [4, 18], [5, 9],
        [6, 11], [7, 27], [8, 33], [9, 40], [10, 32], [11, 35]
        ]);

    var options = {
        legend: 'none',
        backgroundColor: { strokeWidth: 0, fill: '#212b36' },
        chartArea: { 'width': '90%', 'height': '85%' },
        vAxis: {
        viewWindow: {
            min: 0,
            max: 100
        },
        ticks: [30, 70],
        gridlines: {
            color: '#ccc',
            count: 2
            }
        },
                    
        hAxis: {
            viewWindow: {
                min: 0,
                max: 11
            },
            gridlines: {
                color: 'transparent'
                }
            },
            series: {
                0: {
                color: '#fdd835',
                lineWidth: 2 
                    }
                }
            };

        var chart = new google.visualization.LineChart(document.getElementById('RSI'));

        chart.draw(data, options);
}