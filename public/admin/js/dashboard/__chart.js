const charts = JSON.parse(document.getElementById('chart_data').value);
//Active SubscriptionMiddleWare
const chartItem1 = document.getElementById('statisticsChart1');
if (chartItem1) {
    var context = chartItem1.getContext('2d');
    var gradientBGColor = context.createLinearGradient(0, 0, 0, 200);
    gradientBGColor.addColorStop(0.1, '#ff204e4d');
    gradientBGColor.addColorStop(0.4, '#ffffff30');
    // gradientBGColor.addColorStop(0.1, '#24D6AC60');
    // gradientBGColor.addColorStop(0.4, '#ffffff30');

    const chart1 = new Chart(chartItem1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Nov', 'Des'],
            datasets: [{
                label: 'Total COD',
                backgroundColor: gradientBGColor,
                borderColor: '#FF204E',
                // borderColor: '#24D6A5',
                data: charts.total_cod,
                fill: true
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false,
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            },
            elements: {
                line: {
                    borderWidth: 2,
                    tension: 0.4
                },
                point: {
                    radius: 0,
                    hitRadius: 10,
                    hoverRadius: 4
                }
            }
        }
    })
}
// End statisticsChart1




// Total Earning
const chartItem2 = document.getElementById('statisticsChart2');
if (chartItem2) {
    var context = chartItem2.getContext('2d');
    var gradientBGColor = context.createLinearGradient(0, 0, 0, 200);
    gradientBGColor.addColorStop(0.1, '#ffa60050');
    gradientBGColor.addColorStop(0.4, '#ffffff50');

    const chart2 = new Chart(chartItem2, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Nov', 'Des'],
            datasets: [{
                label: 'Total Earning',
                backgroundColor: gradientBGColor,
                borderColor: '#ffa600',
                data: charts.life_time_profit,
                fill: true
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false,
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            },
            elements: {
                line: {
                    borderWidth: 2,
                    tension: 0.4
                },
                point: {
                    radius: 0,
                    hitRadius: 10,
                    hoverRadius: 4
                }
            }
        }
    })
}



//Total Client
const chartItem3 = document.getElementById('statisticsChart3');
if (chartItem3) {
    var context = chartItem3.getContext('2d');
    var gradientBGColor = context.createLinearGradient(0, 0, 0, 200);
    gradientBGColor.addColorStop(0.1, '#ff563050');
    gradientBGColor.addColorStop(0.4, '#ffffff30');

    const chart3 = new Chart(chartItem3, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Nov', 'Des'],
            datasets: [{
                label: 'Total Merchant',
                backgroundColor: gradientBGColor,
                borderColor: '#ff5630',
                data: charts.merchant,
                fill: true
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false,
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            },
            elements: {
                line: {
                    borderWidth: 2,
                    tension: 0.4
                },
                point: {
                    radius: 0,
                    hitRadius: 10,
                    hoverRadius: 4
                }
            }
        }
    })
}
// End statisticsChart3



//Total Parcel
const chartItem4 = document.getElementById('statisticsChart4');
if (chartItem4) {
    var context = chartItem4.getContext('2d');
    var gradientBGColor = context.createLinearGradient(0, 0, 0, 200);
    gradientBGColor.addColorStop(0.1, '#3F52E350');
    gradientBGColor.addColorStop(0.4, '#ffffff30');

    const chart4 = new Chart(chartItem4, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Nov', 'Des'],
            datasets: [{
                label: 'Total Parcel',
                backgroundColor: gradientBGColor,
                borderColor: '#3F52E3',
                data: charts.parcel,
                fill: true
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false,
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            },
            elements: {
                line: {
                    borderWidth: 2,
                    tension: 0.4
                },
                point: {
                    radius: 0,
                    hitRadius: 10,
                    hoverRadius: 4
                }
            }
        }
    })
}
// End statisticsChart4


// Wait for the DOM to be fully loaded


document.addEventListener("DOMContentLoaded", function() {
    var statisticsItem = document.getElementById("conversation_statistic");

    if (statisticsItem) {
        var currentDate = new Date();
        var currentYear = currentDate.getFullYear();
        var currentMonth = currentDate.getMonth() + 1;

        var startMonth = (currentMonth + 1) % 12 || 12;
        var startYear = currentYear - 1;

        var labels = [];
        var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        for (var i = 0; i < 12; i++) {
            var monthIndex = (startMonth + i - 1) % 12;
            var year = startYear + Math.floor((startMonth + i - 1) / 12);
            labels.push(months[monthIndex] + ' ' + year);
        }

        var totalContactsData = [];
        var newContactsData = [];
        for (var j = 0; j < 12; j++) {
            var monthIndex = (startMonth + j - 1) % 12;
            totalContactsData.push(charts.total_contacts[monthIndex]);
            newContactsData.push(charts.new_contacts[monthIndex]);
        }

        var data = {
            labels: labels,
            datasets: [{
                label: 'WhatsApp',
                backgroundColor: '#25d366',
                data: totalContactsData
            }, {
                label: 'Telegram',
                backgroundColor: '#0088cc',
                data: newContactsData
            }]
        };

        var options = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };
        var earningChartBar = new Chart(statisticsItem, {
            type: 'bar',
            data: data,
            options: options
        });
    }
});




//Total Income
const chartItem5 = document.getElementById('statisticsChart5');
if (chartItem5) {
    const chart5 = new Chart(chartItem5, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [
                {
                    label: 'Total Income',
                    backgroundColor: 'transparent',
                    borderColor: '#3f52e3',
                    data: charts.life_time_income,
                    fill: false
                }
            ]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false,
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            },
            elements: {
                line: {
                    borderWidth: 2,
                    tension: 0.4
                },
                point: {
                    radius: 0,
                    hitRadius: 10,
                    hoverRadius: 4
                }
            }
        }
    })
}
// End statisticsChart5




//Total Expense
const chartItem6 = document.getElementById('statisticsChart6');
if (chartItem6) {
    const chart6 = new Chart(chartItem6, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [
                {
                    label: 'Total Expense',
                    backgroundColor: 'transparent',
                    borderColor: '#ff5630',
                    data: charts.life_time_expense,
                    fill: false
                }
            ]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false,
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            },
            elements: {
                line: {
                    borderWidth: 2,
                    tension: 0.4
                },
                point: {
                    radius: 0,
                    hitRadius: 10,
                    hoverRadius: 4
                }
            }
        }
    })
}
// End statisticsChart6


//Total Deliveryman
const chartItem7 = document.getElementById('statisticsChart7');
if (chartItem7) {
    const chart7 = new Chart(chartItem7, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [
                {
                    label: 'Total Delivery Man',
                    backgroundColor: 'transparent',
                    // borderColor: '#24D6A5',
                    borderColor: '#FF204E',
                    data: charts.delivery_man_list,
                    fill: false
                }
            ]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false,
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            },
            elements: {
                line: {
                    borderWidth: 2,
                    tension: 0.4
                },
                point: {
                    radius: 0,
                    hitRadius: 10,
                    hoverRadius: 4
                }
            }
        }
    })
}
// End statisticsChart5




//Total Branch
const chartItem8 = document.getElementById('statisticsChart8');
if (chartItem8) {
    const chart8 = new Chart(chartItem8, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [
                {
                    label: 'Total Branch',
                    backgroundColor: 'transparent',
                    borderColor: '#ff5630',
                    data: charts.branch_list,
                    fill: false
                }
            ]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false,
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            },
            elements: {
                line: {
                    borderWidth: 2,
                    tension: 0.4
                },
                point: {
                    radius: 0,
                    hitRadius: 10,
                    hoverRadius: 4
                }
            }
        }
    })
}
// End statisticsChart6


