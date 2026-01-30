<div class="bg-white shadow p-6 rounded-lg">
    <h2 class="text-xl font-semibold mb-4">Temperature / Humidity Chart</h2>

    <canvas id="climateChart" height="120"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
async function loadClimate() {
    const res = await fetch('/api/device/climate'); 
    const data = await res.json();

    const labels = data.map(item => item.time);
    const values = data.map(item => item.value);

    new Chart(document.getElementById('climateChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: "Climate Sensor",
                data: values,
                borderColor: "rgb(75, 192, 192)",
                borderWidth: 2,
                fill: false
            }]
        }
    });
}

loadClimate();
</script>
