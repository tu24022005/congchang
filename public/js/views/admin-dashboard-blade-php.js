/* admin/dashboard.blade.php - h?nh vi m?n h?nh */
document.addEventListener("DOMContentLoaded", function() {
    
    // CẤU HÌNH BIỂU ĐỒ DOANH THU
    var revOptions = {
        series: [{ name: 'Doanh thu', data: [] }],
        chart: { type: 'area', height: 350, toolbar: { show: false }, zoom: { enabled: false } },
        colors: ['#2F80ED'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 90, 100] } },
        xaxis: { categories: ['Th 1', 'Th 2', 'Th 3', 'Th 4', 'Th 5', 'Th 6', 'Th 7', 'Th 8', 'Th 9', 'Th 10', 'Th 11', 'Th 12'] },
        yaxis: { labels: { formatter: function (val) { return val.toLocaleString('vi-VN') + " đ"; } } },
        tooltip: { y: { formatter: function (val) { return val.toLocaleString('vi-VN') + " VNĐ"; } } }
    };
    var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revOptions);
    revenueChart.render();

    // CẤU HÌNH BIỂU ĐỒ TRÒN
    var payOptions = {
        series: [],
        chart: { type: 'donut', height: 320 },
        labels: ['Tiền mặt (COD)', 'Chuyển khoản (PayOS)'],
        colors: ['#00E396', '#008FFB'],
        plotOptions: { donut: { size: '65%' } },
        dataLabels: { enabled: true },
        legend: { position: 'bottom' }
    };
    var paymentChart = new ApexCharts(document.querySelector("#paymentChart"), payOptions);
    paymentChart.render();

    var weeklyRevenueChart = new ApexCharts(document.querySelector("#weeklyRevenueChart"), {
        series: [{ name: 'Doanh thu', data: [] }],
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        colors: ['#20c997'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '48%' } },
        xaxis: { categories: [] },
        yaxis: { labels: { formatter: function (val) { return val.toLocaleString('vi-VN') + ' đ'; } } },
        tooltip: { y: { formatter: function (val) { return val.toLocaleString('vi-VN') + ' VNĐ'; } } }
    });
    weeklyRevenueChart.render();

    var orderStatusChart = new ApexCharts(document.querySelector("#orderStatusChart"), {
        series: [], chart: { type: 'donut', height: 300 },
        labels: [], colors: ['#ffc107', '#0d6efd', '#198754', '#dc3545'],
        legend: { position: 'bottom' }, dataLabels: { enabled: true }
    });
    orderStatusChart.render();

    // HÀM GỌI DỮ LIỆU TỪ API
    function loadChartData(year) {
        fetch("/admin/chart/data?year=" + year)
            .then(response => response.json())
            .then(data => {
                revenueChart.updateSeries([{ data: data.revenue }]);
                paymentChart.updateSeries(data.payments);
                weeklyRevenueChart.updateOptions({
                    xaxis: { categories: data.lastSevenDays.map(function (item) { return item.label; }) }
                });
                weeklyRevenueChart.updateSeries([{ name: 'Doanh thu', data: data.lastSevenDays.map(function (item) { return item.revenue; }) }]);
                orderStatusChart.updateOptions({ labels: data.statuses.labels });
                orderStatusChart.updateSeries(data.statuses.series);
            })
            .catch(error => console.error('Lỗi tải dữ liệu:', error));
    }

    loadChartData(document.getElementById('yearFilter').value);

    document.getElementById('yearFilter').addEventListener('change', function() {
        loadChartData(this.value);
    });
});
