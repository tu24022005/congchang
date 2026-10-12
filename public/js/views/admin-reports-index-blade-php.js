/* admin/reports/index.blade.php - hành vi màn hình */
document.addEventListener('DOMContentLoaded', function () {
    if (!window.ApexCharts || !window.reportChartData) return;

    const chartData = window.reportChartData;
    const formatCurrency = value => new Intl.NumberFormat('vi-VN').format(value) + ' đ';

    // 1. BIỂU ĐỒ 12 THÁNG NĂM (DOANH THU & ĐƠN HÀNG)
    const monthlyElement = document.getElementById('monthlyRevenueAndOrdersChart');
    if (monthlyElement) {
        const monthlyCategories = chartData.monthly.map(item => item.label);
        const monthlyRevenues = chartData.monthly.map(item => item.revenue);
        const monthlyOrders = chartData.monthly.map(item => item.orders);

        new ApexCharts(monthlyElement, {
            chart: {
                height: 350,
                type: 'line',
                toolbar: { show: false },
                fontFamily: 'DM Sans, sans-serif'
            },
            series: [
                {
                    name: 'Doanh thu (VNĐ)',
                    type: 'column',
                    data: monthlyRevenues
                },
                {
                    name: 'Số đơn thành công',
                    type: 'line',
                    data: monthlyOrders
                }
            ],
            stroke: {
                width: [0, 3],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '40%'
                }
            },
            colors: ['#117c83', '#ea580c'],
            xaxis: {
                categories: monthlyCategories,
                labels: { style: { colors: '#64748b' } }
            },
            yaxis: [
                {
                    title: { text: 'Doanh thu (VNĐ)', style: { color: '#117c83' } },
                    labels: {
                        formatter: val => formatCurrency(val),
                        style: { colors: '#64748b' }
                    }
                },
                {
                    opposite: true,
                    title: { text: 'Số đơn thành công', style: { color: '#ea580c' } },
                    labels: {
                        formatter: val => Math.round(val) + ' đơn',
                        style: { colors: '#64748b' }
                    }
                }
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (y, { seriesIndex }) {
                        if (seriesIndex === 0) return formatCurrency(y);
                        return Math.round(y) + ' đơn hàng';
                    }
                }
            },
            grid: {
                borderColor: '#e7eeee',
                strokeDashArray: 4
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        }).render();
    }

    // 2. BIỂU ĐỒ BIẾN ĐỘNG DOANH THU THEO NGÀY TRONG THÁNG (NẾU ĐANG CHỌN 1 THÁNG)
    const dailyElement = document.getElementById('dailyRevenueChart');
    if (dailyElement) {
        const dailyData = chartData.daily;
        if (dailyData && dailyData.length > 0) {
            new ApexCharts(dailyElement, {
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'DM Sans, sans-serif'
                },
                series: [{
                    name: 'Doanh thu ngày',
                    data: dailyData.map(d => d.revenue)
                }],
                xaxis: {
                    categories: dailyData.map(d => d.day),
                    labels: { style: { colors: '#64748b' } }
                },
                yaxis: {
                    labels: {
                        formatter: val => formatCurrency(val),
                        style: { colors: '#64748b' }
                    }
                },
                colors: ['#238b5c'],
                stroke: { curve: 'smooth', width: 2.5 },
                fill: {
                    type: 'gradient',
                    gradient: { opacityFrom: 0.4, opacityTo: 0.05 }
                },
                tooltip: {
                    y: { formatter: val => formatCurrency(val) }
                },
                grid: {
                    borderColor: '#eef3f3',
                    strokeDashArray: 4
                }
            }).render();
        }
    }

    // 3. BIỂU ĐỒ PHƯƠNG THỨC THANH TOÁN (DONUT)
    const paymentElement = document.getElementById('paymentMethodChart');
    if (paymentElement) {
        new ApexCharts(paymentElement, {
            chart: {
                type: 'donut',
                height: 220,
                fontFamily: 'DM Sans, sans-serif'
            },
            series: chartData.payment,
            labels: ['Tiền mặt (COD)', 'Chuyển khoản (PayOS)'],
            colors: ['#16a34a', '#2563eb'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
            tooltip: {
                y: { formatter: val => formatCurrency(val) }
            }
        }).render();
    }

    // 4. BIỂU ĐỒ PHÂN BỔ TRẠNG THÁI ĐƠN HÀNG (DONUT)
    const statusElement = document.getElementById('orderStatusChart');
    if (statusElement) {
        new ApexCharts(statusElement, {
            chart: {
                type: 'donut',
                height: 220,
                fontFamily: 'DM Sans, sans-serif'
            },
            series: chartData.status,
            labels: ['Thành công', 'Đang xử lý', 'Đã hủy'],
            colors: ['#16a34a', '#ea580c', '#dc2626'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
            tooltip: {
                y: { formatter: val => val + ' đơn hàng' }
            }
        }).render();
    }

    // 5. BIỂU ĐỒ CÂN ĐỐI DÒNG TIỀN (BAR CHART)
    const cashFlowElement = document.getElementById('cashFlowBarChart');
    if (cashFlowElement) {
        new ApexCharts(cashFlowElement, {
            chart: {
                type: 'bar',
                height: 260,
                toolbar: { show: false },
                fontFamily: 'DM Sans, sans-serif'
            },
            series: [{
                name: 'Giá trị',
                data: chartData.cashFlow
            }],
            xaxis: {
                categories: ['Doanh thu thực thu', 'Tổn thất đơn hủy', 'Tổng GMV đặt hàng'],
                labels: { style: { colors: '#64748b' } }
            },
            yaxis: {
                labels: {
                    formatter: val => formatCurrency(val),
                    style: { colors: '#64748b' }
                }
            },
            colors: ['#16a34a', '#dc2626', '#2563eb'],
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    distributed: true,
                    columnWidth: '45%'
                }
            },
            dataLabels: { enabled: false },
            grid: {
                borderColor: '#e7eeee',
                strokeDashArray: 4
            },
            tooltip: {
                y: { formatter: val => formatCurrency(val) }
            },
            legend: { show: false }
        }).render();
    }

    // 6. BIỂU ĐỒ DOANH THU THEO DANH MỤC (DONUT)
    const categoryElement = document.getElementById('categoryRevenueChart');
    if (categoryElement) {
        const catNames = chartData.categories.map(item => item.name);
        const catRevenues = chartData.categories.map(item => item.revenue);

        if (catNames.length > 0) {
            new ApexCharts(categoryElement, {
                chart: {
                    type: 'donut',
                    height: 260,
                    fontFamily: 'DM Sans, sans-serif'
                },
                series: catRevenues,
                labels: catNames,
                legend: { position: 'bottom' },
                dataLabels: { enabled: true },
                tooltip: {
                    y: { formatter: val => formatCurrency(val) }
                }
            }).render();
        }
    }
});

