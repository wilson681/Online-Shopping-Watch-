const ctx = document.getElementById("salesChart").getContext("2d");
const salesChart = new Chart(ctx, {
  type: "line",
  data: {
    labels: window.chartDates,
    datasets: [{
      label: "Revenue (RM)",
      data: window.chartRevenues, 
      fill: true,
      borderColor: "#4CAF50",
      backgroundColor: "rgba(22, 76, 213, 0.1)",
      tension: 0.3,
      pointRadius: 5,
      pointHoverRadius: 7
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});
