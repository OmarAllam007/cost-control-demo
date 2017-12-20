import Chart from 'chart.js';
window.number_format = function(num) {
    return parseFloat(parseFloat(num).toFixed(2)).toLocaleString();
};

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
                    xAxes: [{ ticks: { beginAtZero:false }}],
                    animation: {duration: 1500, easing: 'easeOutExpo'}
            }
        }
    }

    const chart = new Chart(canvas, {
        type: item.dataset.type, data, options
    });
});

