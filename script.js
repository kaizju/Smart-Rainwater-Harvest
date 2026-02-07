const chart = new Chart(document.getElementById('bar-chart'), {
  type: 'bar',
  data: {
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    datasets: [{
      label: 'RainWater Collection',
      data: [0, 0, 0, 0, 0, 0, 0],
      backgroundColor: '#007bff'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: { beginAtZero: true }
    }
  }
});