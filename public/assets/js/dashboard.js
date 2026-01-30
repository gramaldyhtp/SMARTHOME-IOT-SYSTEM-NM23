new Chart(document.getElementById('monitorChart'), {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [
            {
                label: "Temperature (°C)",
                borderColor: "#6366f1",
                data: [1200, 900, 1100, 1250, 1300, 1000, 950, 1100, 1200, 1250, 1150]
            },
            {
                label: "Power (W)",
                borderColor: "#22c55e",
                data: [0,0,0,0,0,0,0,0,0,0,0]
            }
        ]
    }
});
