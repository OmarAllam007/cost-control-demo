import Chart from 'chart.js';

$('.chart').each((idx, item) => {
    item.style.minHeight = '300px';

    const canvas = document.createElement('canvas');
    canvas.height = 300;
    canvas.width = item.width;
    canvas.id = item.id + '-chart';

    let data = {
        labels: JSON.parse(item.dataset.labels),
        datasets: JSON.parse(item.dataset.datasets),
        maxBarThickness: 50
    };

    if (item.dataset.backgroundColors) {
        data['backgroundColors'] = data.dataset.backgroundColors;
    }

    const chart = new Chart(canvas, {
        type: item.dataset.type,
        data,
        options: {
            scales: {
                yAxes: [{ ticks: { beginAtZero:true }}]
            }
        }
    });

    item.appendChild(canvas);
});

