<?php
// Simulated tank data (replace with actual DB query)
$tank = [
    'percent_full' => 0,
    'max_capacity' => 5000,
    'current' => 0,
];

// Simulated water quality (replace with actual DB query)
$quality = [
    'ph_level' => 0,
    'turbidity' => 0,
    'quality_status' => 'N/A',
    'recorded_at' => date('Y-m-d H:i:s'),
];

// Simulated water usage last 7 days (replace with actual DB query)
$usageData = [0, 0, 0, 0, 0, 0, 0];
$usageLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

// Progress bar color
$pct = $tank['percent_full'];
if ($pct < 20) $progColor = '#ef4444';
elseif ($pct < 50) $progColor = '#f59e0b';
else $progColor = '#3b82f6';

// Time since last quality update
$lastUpdated = '0s ago';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EcoRain — Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
  <style>
    :root {
      --sidebar-bg: #0F172A;
      --sidebar-text: #94a3b8;
      --sidebar-active: #ffffff;
      --body-bg: #f1f5f9;
      --card-bg: #ffffff;
      --tank-card-bg: #96b3f8;
      --accent: #3b82f6;
      --text-primary: #1e293b;
      --text-muted: #94a3b8;
      --border: #e2e8f0;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--body-bg);
      color: var(--text-primary);
      min-height: 100vh;
    }

    .layout {
      display: flex;
      min-height: 100vh;
    }

    /* ── Sidebar ── */
    .sidebar {
      width: 220px;
      background: var(--sidebar-bg);
      display: flex;
      flex-direction: column;
      padding: 1.5rem 1rem;
      position: fixed;
      top: 0; left: 0; bottom: 0;
      z-index: 100;
    }

    .sidebar-brand {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      color: #fff;
      text-decoration: none;
      margin-bottom: 2.5rem;
      padding: 0 0.5rem;
    }

    .sidebar-brand .brand-icon {
      width: 32px; height: 32px;
      background: var(--accent);
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem;
    }

    .sidebar-brand span {
      font-size: 1.25rem;
      font-weight: 700;
      letter-spacing: -0.3px;
    }

    .nav-label {
      font-size: 0.65rem;
      font-weight: 600;
      letter-spacing: 1.2px;
      color: #475569;
      text-transform: uppercase;
      padding: 0 0.75rem;
      margin-bottom: 0.5rem;
    }

    .nav-link-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.65rem 0.75rem;
      border-radius: 8px;
      color: var(--sidebar-text);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: background 0.18s, color 0.18s;
      margin-bottom: 0.2rem;
    }

    .nav-link-item:hover {
      background: rgba(255,255,255,0.06);
      color: #fff;
    }

    .nav-link-item.active {
      background: rgba(59,130,246,0.18);
      color: #fff;
    }

    .nav-link-item i {
      font-size: 1.1rem;
      width: 20px;
      text-align: center;
    }

    /* ── Main Content ── */
    .main {
      margin-left: 220px;
      flex: 1;
      padding: 2rem 2rem 2rem 2rem;
    }

    .page-header {
      margin-bottom: 1.75rem;
    }

    .page-header h1 {
      font-size: 1.85rem;
      font-weight: 700;
      letter-spacing: -0.5px;
      color: var(--text-primary);
    }

    .page-header p {
      color: var(--text-muted);
      font-size: 0.9rem;
      margin-top: 0.2rem;
    }

    /* ── Top Row Cards ── */
    .top-row {
      display: grid;
      grid-template-columns: 280px 1fr 1fr;
      gap: 1.25rem;
      margin-bottom: 1.25rem;
    }

    /* Tank Card */
    .tank-card {
      background: var(--tank-card-bg);
      border-radius: 16px;
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      min-height: 340px;
      position: relative;
      overflow: hidden;
    }

    .tank-card::before {
      content: '';
      position: absolute;
      top: -40px; right: -40px;
      width: 140px; height: 140px;
      border-radius: 50%;
      background: rgba(255,255,255,0.15);
    }

    .tank-card-header {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.85rem;
      font-weight: 600;
      color: rgba(15,23,42,0.75);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .tank-pct {
      font-family: 'Space Mono', monospace;
      font-size: 4.5rem;
      font-weight: 700;
      color: var(--sidebar-bg);
      line-height: 1;
      margin: auto 0;
    }

    .tank-footer {
      margin-top: auto;
    }

    .tank-capacity {
      font-size: 1.2rem;
      font-weight: 600;
      color: rgba(15,23,42,0.7);
      margin-bottom: 0.6rem;
    }

    .tank-progress {
      width: 100%;
      height: 8px;
      background: rgba(255,255,255,0.45);
      border-radius: 99px;
      overflow: hidden;
      margin-bottom: 0.6rem;
    }

    .tank-progress-fill {
      height: 100%;
      border-radius: 99px;
      background: <?php echo $progColor; ?>;
      width: <?php echo $pct; ?>%;
      transition: width 0.6s ease;
    }

    .tank-collected {
      font-size: 0.85rem;
      color: rgba(15,23,42,0.65);
    }

    /* Quality Card */
    .quality-card {
      background: var(--card-bg);
      border-radius: 16px;
      padding: 1.5rem;
      border: 1px solid var(--border);
    }

    .quality-card .card-title {
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: var(--text-muted);
      margin-bottom: 0.5rem;
    }

    .quality-badge {
      display: inline-block;
      background: #e2e8f0;
      color: #64748b;
      font-size: 0.8rem;
      font-weight: 600;
      padding: 2px 10px;
      border-radius: 4px;
      margin-bottom: 0.25rem;
    }

    .quality-updated {
      font-size: 0.8rem;
      color: var(--text-muted);
      margin-bottom: 1rem;
    }

    .metric-cards {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .metric-item {
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 0.9rem 1rem;
    }

    .metric-label {
      font-size: 0.8rem;
      font-weight: 500;
      color: var(--text-muted);
      margin-bottom: 0.25rem;
    }

    .metric-value {
      font-family: 'Space Mono', monospace;
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--text-primary);
      line-height: 1;
    }

    .metric-status {
      font-size: 0.8rem;
      font-weight: 700;
      color: #22c55e;
      margin-top: 0.25rem;
    }

    /* Chart Card */
    .chart-card {
      background: var(--card-bg);
      border-radius: 16px;
      padding: 1.5rem;
      border: 1px solid var(--border);
      display: flex;
      flex-direction: column;
    }

    .chart-card-title {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 1rem;
    }

    /* Forecast Card */
    .forecast-card {
      background: var(--card-bg);
      border-radius: 16px;
      padding: 1.5rem;
      border: 1px solid var(--border);
      max-width: 600px;
    }

    .forecast-card-title {
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 1rem;
    }

    .forecast-inner {
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 0.5rem 1rem;
    }

    .forecast-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 0.85rem 0;
      border-bottom: 1px solid var(--border);
    }

    .forecast-item:last-child {
      border-bottom: none;
    }

    .forecast-icon { font-size: 1.8rem; }

    .forecast-day {
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .forecast-chance {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    /* Loading spinner */
    .forecast-loading {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      color: var(--text-muted);
      gap: 0.5rem;
    }

    .spinner {
      width: 18px; height: 18px;
      border: 2px solid var(--border);
      border-top-color: var(--accent);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 1100px) {
      .top-row { grid-template-columns: 260px 1fr; }
      .top-row > :last-child { grid-column: 1 / -1; }
    }

    @media (max-width: 768px) {
      .sidebar { width: 64px; }
      .sidebar-brand span, .nav-link-item span { display: none; }
      .main { margin-left: 64px; padding: 1rem; }
      .top-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
<div class="layout">

  <!-- Sidebar -->
  <nav class="sidebar">
    <a href="#" class="sidebar-brand">
      <div class="brand-icon">💧</div>
      <span>EcoRain</span>
    </a>

    <div class="nav-label">Main</div>
    <a href="#" class="nav-link-item active">
      <i class="fi fi-rr-analytics"></i>
      <span>Dashboard</span>
    </a>
    <a href="#" class="nav-link-item">
      <i class="fi fi-rr-chart-histogram"></i>
      <span>Usage Stats</span>
    </a>
    <a href="#" class="nav-link-item">
      <i class="fi fi-rr-cloud-sun"></i>
      <span>Weather</span>
    </a>
    <a href="#" class="nav-link-item">
      <i class="fi fi-rr-settings"></i>
      <span>Settings</span>
    </a>
  </nav>

  <!-- Main -->
  <main class="main">
    <div class="page-header">
      <h1>DashBoard</h1>
      <p>Welcome to EcoRain</p>
    </div>

    <div class="top-row">

      <!-- Tank Level Card -->
      <div class="tank-card">
        <div class="tank-card-header">
          💧 Main Tank Level
        </div>
        <div class="tank-pct" id="tankPct"><?php echo $pct; ?>%</div>
        <div class="tank-footer">
          <div class="tank-capacity" id="tankCapacity"><?php echo number_format($tank['max_capacity']); ?>L</div>
          <div class="tank-progress">
            <div class="tank-progress-fill" id="tankFill"></div>
          </div>
          <div class="tank-collected" id="tankCollected"><?php echo number_format($tank['current']); ?>L collected today</div>
        </div>
      </div>

      <!-- Water Quality Card -->
      <div class="quality-card">
        <div class="card-title">Water Quality</div>
        <div class="quality-badge" id="qualityStatus"><?php echo htmlspecialchars($quality['quality_status']); ?></div>
        <div class="quality-updated" id="qualityUpdated">updated <?php echo $lastUpdated; ?></div>
        <div class="metric-cards">
          <div class="metric-item">
            <div class="metric-label">🫀 pH Level</div>
            <div class="metric-value" id="phLevel"><?php echo $quality['ph_level']; ?></div>
            <div class="metric-status">None</div>
          </div>
          <div class="metric-item">
            <div class="metric-label">💧 Turbidity</div>
            <div class="metric-value" id="turbidity"><?php echo $quality['turbidity']; ?></div>
            <div class="metric-status">None</div>
          </div>
        </div>
      </div>

      <!-- Bar Chart Card -->
      <div class="chart-card">
        <div class="chart-card-title">Water Usage — Last 7 Days</div>
        <div style="flex:1; position:relative; min-height:200px;">
          <canvas id="bar-chart"></canvas>
        </div>
      </div>

    </div>

    <!-- Rainfall Forecast -->
    <div class="forecast-card">
      <div class="forecast-card-title" id="locationName">Rainfall Forecast</div>
      <div class="forecast-inner">
        <div id="forecastLoading" class="forecast-loading">
          <div class="spinner"></div> Loading forecast…
        </div>
        <div id="rainfallForecast" style="display:none;"></div>
        <div id="forecastError" style="display:none; color:#ef4444; padding:1rem; font-size:0.85rem;"></div>
      </div>
    </div>
  </main>
</div>

<script>
// ── Bar Chart ──────────────────────────────────────────
const usageData = <?php echo json_encode($usageData); ?>;
const usageLabels = <?php echo json_encode($usageLabels); ?>;

new Chart(document.getElementById('bar-chart'), {
  type: 'bar',
  data: {
    labels: usageLabels,
    datasets: [{
      label: 'RainWater Collection',
      data: usageData,
      backgroundColor: '#3b82f6',
      borderRadius: 6,
      borderSkipped: false,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { labels: { font: { family: 'DM Sans' }, boxWidth: 14 } } },
    scales: {
      y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { family: 'DM Sans' } } },
      x: { grid: { display: false }, ticks: { font: { family: 'DM Sans' } } }
    }
  }
});

// ── Weather Forecast ────────────────────────────────────
const CONFIG = {
  apiKey: 'a5712e740541248ce7883f0af8581be4',
  latitude: 8.360015,
  longitude: 124.868419,
  units: 'metric'
};

function getWeatherIcon(description, rainAmount) {
  if (rainAmount > 5) return '🌧️';
  if (rainAmount > 0) return '🌦️';
  if (description.includes('cloud')) return '☁️';
  if (description.includes('clear') || description.includes('sun')) return '☀️';
  return '🌤️';
}

function calculateRainChance(item) {
  const hasRain = item.rain && item.rain['3h'] > 0;
  const humidity = item.main.humidity;
  const clouds = item.clouds.all;
  if (hasRain) return Math.min(Math.round(humidity * 0.7 + clouds * 0.3), 95);
  if (humidity > 80 && clouds > 70) return Math.round((humidity + clouds) / 2 * 0.5);
  if (humidity > 70) return Math.round(humidity * 0.3);
  return Math.round(clouds * 0.2);
}

async function fetchWeatherData() {
  const url = `https://api.openweathermap.org/data/2.5/forecast?lat=${CONFIG.latitude}&lon=${CONFIG.longitude}&appid=${CONFIG.apiKey}&units=${CONFIG.units}`;
  const response = await fetch(url);
  if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
  return response.json();
}

function processForecastData(data) {
  const dailyData = {};
  data.list.forEach(item => {
    const date = new Date(item.dt * 1000);
    const day = date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
    const dayShort = date.toLocaleDateString('en-US', { weekday: 'long' });
    if (!dailyData[day]) {
      dailyData[day] = { dayShort, rainfall: [], rainChances: [], weather: item.weather[0].description };
    }
    dailyData[day].rainfall.push(item.rain ? (item.rain['3h'] || 0) : 0);
    dailyData[day].rainChances.push(calculateRainChance(item));
  });
  return Object.keys(dailyData).slice(0, 3).map((day, index) => {
    const totalRain = dailyData[day].rainfall.reduce((a, b) => a + b, 0);
    const avgRainChance = Math.round(dailyData[day].rainChances.reduce((a, b) => a + b, 0) / dailyData[day].rainChances.length);
    const dayLabel = index === 0 ? 'Today' : index === 1 ? 'Tomorrow' : dailyData[day].dayShort.substring(0, 3);
    return { day: dayLabel, chance: avgRainChance, amount: totalRain, icon: getWeatherIcon(dailyData[day].weather, totalRain) };
  });
}

async function initWeather() {
  try {
    const data = await fetchWeatherData();
    document.getElementById('locationName').textContent = `Rainfall Forecast — ${data.city.name}, ${data.city.country}`;
    const forecasts = processForecastData(data);
    const html = forecasts.map(f => `
      <div class="forecast-item">
        <div class="forecast-icon">${f.icon}</div>
        <div>
          <div class="forecast-day">${f.day}</div>
          <div class="forecast-chance">${f.chance}% chance</div>
        </div>
      </div>
    `).join('');
    document.getElementById('rainfallForecast').innerHTML = html;
    document.getElementById('rainfallForecast').style.display = 'block';
    document.getElementById('forecastLoading').style.display = 'none';
  } catch (e) {
    document.getElementById('forecastLoading').style.display = 'none';
    document.getElementById('forecastError').style.display = 'block';
    document.getElementById('forecastError').textContent = `Error loading weather data: ${e.message}`;
  }
}

// ── Tank Data (AJAX polling) ────────────────────────────
async function fetchTankData() {
  try {
    const res = await fetch('../Dashboard/tank.php');
    const data = await res.json();
    if (data.error) return;
    const pct = data.percent_full;
    document.getElementById('tankPct').textContent = pct + '%';
    document.getElementById('tankCapacity').textContent = data.max_capacity.toLocaleString() + 'L';
    document.getElementById('tankCollected').textContent = data.current.toLocaleString() + 'L collected today';
    const fill = document.getElementById('tankFill');
    fill.style.width = pct + '%';
    fill.style.background = pct < 20 ? '#ef4444' : pct < 50 ? '#f59e0b' : '#3b82f6';
  } catch (e) {}
}

fetchTankData();
setInterval(fetchTankData, 10000);
initWeather();
</script>
</body>
</html>