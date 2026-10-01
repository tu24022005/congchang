/* admin/reports/index.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
    if (!window.ApexCharts) return;

    const formatCurrency = value => new Intl.NumberFormat('vi-VN').format(value) + ' Ä‘';

    // 1. BIá»‚U Äá»’ 12 THÃNG NÄ‚M (DOANH THU & ÄÆ N HÃ€NG)
    const monthlyElement = document.getElementById('monthlyRevenueAndOrdersChart');
    if (monthlyElement) {
        const monthlyCategories = [];
        const monthlyRevenues = [];
        const monthlyOrders = [];

        new ApexCharts(monthlyElement, {
            chart: {
                height: 350,
                type: 'line',
                toolbar: { show: false },
                fontFamily: 'DM Sans, sans-serif'
            },
            series: [
                {
                    name: 'Doanh thu (VNÄ)',
                    type: 'column',
                    data: monthlyRevenues
                },
                {
                    name: 'Sá»‘ Ä‘Æ¡n thÃ nh cÃ´ng',
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
                    title: { text: 'Doanh thu (VNÄ)', style: { color: '#117c83' } },
                    labels: {
                        formatter: val => formatCurrency(val),
                        style: { colors: '#64748b' }
                    }
                },
                {
                    opposite: true,
                    title: { text: 'Sá»‘ Ä‘Æ¡n thÃ nh cÃ´ng', style: { color: '#ea580c' } },
                    labels: {
                        formatter: val => Math.round(val) + ' Ä‘Æ¡n',
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
                        return Math.round(y) + ' Ä‘Æ¡n hÃ ng';
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

    // 2. BIá»‚U Äá»’ BIáº¾N Äá»˜NG DOANH THU THEO NGÃ€Y TRONG THÃNG (Náº¾U ÄANG CHá»ŒN 1 THÃNG)
    const dailyElement = document.getElementById('dailyRevenueChart');
    if (dailyElement) {
        const dailyData = null;
        if (dailyData && dailyData.length > 0) {
            new ApexCharts(dailyElement, {
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'DM Sans, sans-serif'
                },
                series: [{
                    name: 'Doanh thu ngÃ y',
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

    // 3. BIá»‚U Äá»’ PHÆ¯Æ NG THá»¨C THANH TOÃN (DONUT)
    const paymentElement = document.getElementById('paymentMethodChart');
    if (paymentElement) {
        new ApexCharts(paymentElement, {
            chart: {
                type: 'donut',
                height: 220,
                fontFamily: 'DM Sans, sans-serif'
            },
            series: [null, null],
            labels: ['Tiá»n máº·t (COD)', 'Chuyá»ƒn khoáº£n (PayOS)'],
            colors: ['#16a34a', '#2563eb'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
            tooltip: {
                y: { formatter: val => formatCurrency(val) }
            }
        }).render();
    }

    // 4. BIá»‚U Äá»’ PHÃ‚N Bá»” TRáº NG THÃI ÄÆ N HÃ€NG (DONUT)
    const statusElement = document.getElementById('orderStatusChart');
    if (statusElement) {
        new ApexCharts(statusElement, {
            chart: {
                type: 'donut',
                height: 220,
                fontFamily: 'DM Sans, sans-serif'
            },
            series: [
                null,
                null,
                null
            ],
            labels: ['ThÃ nh cÃ´ng', 'Äang xá»­ lÃ½', 'ÄÃ£ há»§y'],
            colors: ['#16a34a', '#ea580c', '#dc2626'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
            tooltip: {
                y: { formatter: val => val + ' Ä‘Æ¡n hÃ ng' }
            }
        }).render();
    }

    // 5. BIá»‚U Äá»’ CÃ‚N Äá»I DÃ’NG TIá»€N (BAR CHART)
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
                name: 'GiÃ¡ trá»‹',
                data: [null, null, null]
            }],
            xaxis: {
                categories: ['Doanh thu thá»±c thu', 'Tá»•n tháº¥t Ä‘Æ¡n há»§y', 'Tá»•ng GMV Ä‘áº·t hÃ ng'],
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

    // 6. BIá»‚U Äá»’ DOANH THU THEO DANH Má»¤C (DONUT)
    const categoryElement = document.getElementById('categoryRevenueChart');
    if (categoryElement) {
        const catNames = null;
        const catRevenues = [];

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



