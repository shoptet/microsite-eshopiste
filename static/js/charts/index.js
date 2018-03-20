const humanifyBigNumber = $num => {
  if ($num >= 1000000) {
   return Math.round($num/1000000) + ' mil.';
  } else if ($num >= 1000) {
    return Math.round($num/1000) + ' tis.';
  } else {
    return $num;
  }
  
};

const createChart = (elementId, labels, data, yAxesPostfix) => {
  const ctx = document.getElementById(elementId).getContext('2d');
  const gradient = ctx.createLinearGradient(0, 0, 0, 120);
  gradient.addColorStop(0, 'rgba(20, 177, 239, .4)');
  gradient.addColorStop(1, 'rgba(20, 177, 239, 0)');
  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        data: data,
        borderColor: 'rgb(20, 177, 239)',
        backgroundColor: gradient,
      }],
    },
    options: {
      legend: {
        display: false,
      },
      scales: {
        yAxes: [{
          ticks: {
            fontColor: 'rgb(33, 37, 41)',
            callback: (value, index, values) => {
              return humanifyBigNumber(value) + ' ' + yAxesPostfix;
            }
          },
          gridLines: {
            color: 'rgba(0, 0, 0, .08)',
          },
        }],
        xAxes: [{
          ticks: {
            fontColor: 'rgb(33, 37, 41)',
          },
          gridLines: {
            color: 'rgba(0, 0, 0, .08)',
          },
        }],
      },
      tooltips: {
        footerFontStyle: 'normal',
        callbacks: {
          custom: tooltip => {
            if (!tooltip) return;
            tooltip.displayColors = false; // disable displaying the color box
          },
          label: () => { return }, // remove colored box with value
          footer: (item, data) => {
            return item[0].yLabel + ' ' + yAxesPostfix;
          },
        },
      },
    },
  });
};

const initCharts = () => {

  if (!window.chartData) {
    return;
  }

  const chartData = window.chartData;
  for (let i = 0, len = chartData.length; i < len; i++) {
    createChart(
      chartData[i].id,
      chartData[i].labels,
      chartData[i].data,
      chartData[i].yAxesPostfix,
    );
  }
};

export default initCharts;
