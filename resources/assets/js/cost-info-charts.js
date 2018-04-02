import Chart from 'chart.js';

require('chartjs-plugin-datalabels');


window.number_format = function(num, digits) {
    return parseFloat(parseFloat(num).toFixed(3)).toLocaleString({
        minimumFractionDigits : digits,
        maximumFractionDigits: digits
    });
};

window.number_format2 = function (num) {
    return window.number_format(num, 2);
};

window.number_format3 = function (num) {
    return window.number_format(num, 3);
}

window.percent = function (num) {
    return window.number_format(num, 2) + '%';
}

Chart.defaults.global.plugins.datalabels.align = 'center';
Chart.defaults.global.plugins.datalabels.anchor = 'center';
Chart.defaults.global.plugins.datalabels.formatter = window.number_format;
Chart.defaults.global.plugins.datalabels.color = '#fff';
// Chart.defaults.global.plugins.datalabels.backgroundColor = '#8ed3d8';
Chart.defaults.global.plugins.datalabels.font.weight = '700';

document.querySelectorAll('.chart').forEach((item) => {
    const canvas = document.createElement('canvas');
    canvas.height = item.height;
    canvas.width = item.width;
    canvas.id = item.id + '-chart';
    item.appendChild(canvas);

    let data = {
        labels: JSON.parse(item.dataset.labels),
        datasets: JSON.parse(item.dataset.datasets),
        maxBarThickness: 50
    };

    if (item.dataset.backgroundColors) {
        data['backgroundColors'] = data.dataset.backgroundColors;
    }

    let options = {};

    if (item.dataset.type !== 'pie') {
        options = {
            scales: {
                yAxes: [{ ticks: { beginAtZero:true }}],
                xAxes: [{ ticks: { beginAtZero:true }}],
                animation: {duration: 1500, easing: 'easeOutExpo'}
            }
        }
    }

    options.plugins = { datalabels: {} };
    if (item.dataset.formatter == 'number_2') {
        options.plugins.datalabels.formatter = window.number_format2;
    } else if (item.dataset.formatter == 'number_3') {
        options.plugins.datalabels.formatter = window.number_format3;
    } else if (item.dataset.formatter == 'percent') {
        options.plugins.datalabels.formatter = window.percent;
    }

    if (item.dataset.type == 'line') {
        options.plugins.datalabels.backgroundColor = '#8ed3d8';
    }

    const chart = new Chart(canvas, {
        type: item.dataset.type, data, options
    });
});

