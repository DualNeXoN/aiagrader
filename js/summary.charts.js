const chartProjectComplexityElement = document.getElementById('chart-project-complexity');
let chartProjectComplexity;

function getCheckedProjects() {
    var checkedCheckboxes = document.querySelectorAll('input[type="checkbox"][id^="summaryItemCheckbox-"]:checked');
    var projectNames = [];

    checkedCheckboxes.forEach(function (checkbox) {
        var projectName = checkbox.getAttribute('project-name');
        if (projectName) {
            projectNames.push(projectName);
        }
    });

    return projectNames;
}

function fetchData(url, request) {
    return $.ajax({
        type: 'POST',
        url: url,
        data: {
            json: JSON.stringify({
                request
            })
        },
        dataType: 'json'
    });
}

function initChart_ProjectComplexity(dataComponents, dataBlocks) {
    chartProjectComplexity = new Chart(chartProjectComplexityElement, {
        type: 'bar',
        data: {
            labels: Object.keys(dataComponents),
            datasets: [{
                label: 'Components',
                data: Object.values(dataComponents),
                borderWidth: 1
            },
            {
                label: 'Blocks',
                data: Object.values(dataBlocks),
                borderWidth: 1
            }
            ],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: 'white'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: 'white'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: 'white'
                    }
                },
                title: {
                    display: true,
                    text: 'Project Complexity',
                    color: 'white',
                    font: {
                        size: 18
                    },
                    padding: {
                        top: 10,
                        bottom: 30
                    }
                }
            }
        }
    });
}

function fetchDataAndUpdateChart_ProjectComplexity() {
    fetchData("api/projectcomplexity.php", getCheckedProjects())
        .done(function (data) {

            let dataComponents = {};
            let dataBlocks = {};
            Object.keys(data).forEach(key => {
                dataBlocks[key] = data[key][0];
                dataComponents[key] = data[key][1];
            });

            if (chartProjectComplexity) {
                chartProjectComplexity.data.labels = Object.keys(data);
                chartProjectComplexity.data.datasets[0].label = "Components";
                chartProjectComplexity.data.datasets[0].data = Object.values(dataComponents);
                chartProjectComplexity.data.datasets[1].label = "Blocks";
                chartProjectComplexity.data.datasets[1].data = Object.values(dataBlocks);
                chartProjectComplexity.update();
            } else {
                initChart_ProjectComplexity(dataComponents, dataBlocks);
            }
        });
}

function fetchDataAndUpdateCharts() {
    fetchDataAndUpdateChart_ProjectComplexity();
}

fetchDataAndUpdateCharts();