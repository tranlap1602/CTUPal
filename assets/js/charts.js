// File: assets/js/charts.js
// Plugin emptyDoughnut cho trạng thái rỗng
const emptyDoughnutPlugin = {
    id: 'emptyDoughnut',
    afterDraw(chart, args, options) {
        const {datasets} = chart.data;
        const {color, width, radiusDecrease} = options;
        let hasData = false;
        for (let i = 0; i < datasets.length; i += 1) {
            const dataset = datasets[i];
            hasData |= dataset.data.length > 0;
        }
        if (!hasData) {
            const {chartArea: {left, top, right, bottom}, ctx} = chart;
            const centerX = (left + right) / 2;
            const centerY = (top + bottom) / 2;
            const r = Math.min(right - left, bottom - top) / 2;
            ctx.beginPath();
            ctx.lineWidth = width || 2;
            ctx.strokeStyle = color || 'rgba(255, 128, 0, 0.5)';
            ctx.arc(centerX, centerY, (r - (radiusDecrease || 0)), 0, 2 * Math.PI);
            ctx.stroke();
        }
    }
};

window.renderCharts = function(monthLabels, monthData, todayLabels, todayData) {
    // Biểu đồ tháng này
    const ctx = document.getElementById('monthChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Chi tiêu tháng này',
                data: monthData,
                hoverOffset: 8
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Chi tiêu tháng này',
                    font: { size: 18 },
                    align: 'center',
                    padding: { top: 0, bottom: 16 }
                },
                legend: {
                    display: true,
                    position: 'bottom',
                },
                emptyDoughnut: {
                    color: 'rgba(255, 128, 0, 0.5)',
                    width: 2,
                    radiusDecrease: 20
                }
            }
        },
        plugins: [emptyDoughnutPlugin]
    });

    // Biểu đồ hôm nay
    const ctxToday = document.getElementById('todayChart').getContext('2d');
    new Chart(ctxToday, {
        type: 'doughnut',
        data: {
            labels: todayLabels,
            datasets: [{
                label: 'Chi tiêu hôm nay',
                data: todayData,
                hoverOffset: 8
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Chi tiêu hôm nay',
                    font: { size: 18 },
                    align: 'center',
                    padding: { top: 0, bottom: 16 }
                },
                legend: {
                    display: true,
                    position: 'bottom',
                },
                emptyDoughnut: {
                    color: 'rgba(255, 128, 0, 0.5)',
                    width: 2,
                    radiusDecrease: 20
                }
            }
        },
        plugins: [emptyDoughnutPlugin]
    });
} 