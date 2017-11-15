import Chart from 'chart.js';

$('.chart').each((idx, item) => {
    item.style.minHeight = '300px';

    const canvas = document.createElement('canvas');
    canvas.height = 300;
    canvas.width = item.width;
    canvas.id = item.id + '-chart';

    console.log(item.dataset.labels);
    console.log(item.dataset.datasets);

    const chart = new Chart(canvas, {
        type: item.dataset.type,
        data: {
            labels: JSON.parse(item.dataset.labels),
            datasets: JSON.parse(item.dataset.datasets),
            options: {
                scales: {
                    yAxes: [{ ticks: { beginAtZero:true }}]
                }
            }
        }
    });

    item.appendChild(canvas);
});

