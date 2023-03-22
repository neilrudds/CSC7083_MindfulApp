const day = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
const month = ["January", "Feburary", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

var moodLabel = [],
  countData = [],
  colourHtml = [],
  dayDataset = [],
  mthDataset = [];

async function moodPieChart() {
  await getChartMoodData()

  const ctx = document.getElementById('myPieChart').getContext('2d');

  const chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'pie',

    // The data for our dataset
    data: {
      labels: moodLabel,
      datasets: [{
        label: 'Mood Count',
        backgroundColor: colourHtml,
        data: countData
      }]
    },

    // Configuration options go here
    options: {
      tooltips: {
        mode: 'index'
      },
      legend: {
        position: 'left'
      },
    }
  })
}
moodPieChart()

async function dailyMoodBarChart() {
  await getChartMoodData()

  const ctx = document.getElementById('dayBarChart').getContext('2d');

  const chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'bar',
    data: {
      labels: day,
      datasets: dayDataset,
    },
    options: {
      tooltips: {
        displayColors: true,
        callbacks: {
          mode: 'x',
        },
      },
      scales: {
        xAxes: [{
          stacked: true,
          gridLines: {
            display: false,
          }
        }],
        yAxes: [{
          stacked: true,
          ticks: {
            beginAtZero: true,
          },
          type: 'linear',
        }]
      },
      responsive: true,
      legend: {
        position: 'bottom'
      },
    }
  })
}
dailyMoodBarChart()

async function monthlyMoodBarChart() {
  await getChartMoodData()

  const ctx = document.getElementById('mthBarChart').getContext('2d');

  const chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'bar',
    data: {
      labels: month,
      datasets: mthDataset,
    },
    options: {
      tooltips: {
        displayColors: true,
        callbacks: {
          mode: 'x',
        },
      },
      scales: {
        xAxes: [{
          stacked: true,
          gridLines: {
            display: false,
          }
        }],
        yAxes: [{
          stacked: true,
          ticks: {
            beginAtZero: true,
          },
          type: 'linear',
        }]
      },
      responsive: true,
      legend: {
        position: 'bottom'
      },
    }
  })
}
monthlyMoodBarChart()

function getColourByMoodname(obj, mood_description) {
  // iterate over each element in the array
  for (var i = 0; i < obj.length; i++) {
    // look for the entry with a matching `mood_name` value
    if (obj[i].mood_description == mood_description) {
      return obj[i].html_colour
    }
  }
}

function checkLabel(array, label) {
  return array.some(function (obj) {
    return obj.label === label;
  });
}

async function getChartMoodData() {

  mySessionToken
  const moodLogUrl = `https://localhost:3000/api/v1/log/${myUserId}`

  const response = await fetch(moodLogUrl, {
    headers: {
      Authorization: `Bearer ${mySessionToken}`
    }
  });
  const barChartData = await response.json()

  // Group moods of the same type and count occurances
  var counts = barChartData.reduce((p, c) => {
    var name = c.mood_description;
    if (!p.hasOwnProperty(name)) {
      p[name] = 0;
    }
    p[name]++;
    return p;
  }, {});

  // day data
  var d_map = {};
  barChartData.forEach(function (val) {
    const d = new Date(val.entry_timestamp);
    let day = d.getDay();
    d_map[day] = d_map[day] || {};
    d_map[day][val.mood_description] = d_map[day][val.mood_description] || 0;
    d_map[day][val.mood_description]++;
  });

  var dayArr = [];
  const daily = Object.keys(d_map).map(k => {
    for (var mood_description in d_map[k]) {
      if (!checkLabel(dayArr, mood_description)) {
        dayArr.push({
          label: mood_description,
          backgroundColor: '#' + getColourByMoodname(barChartData, mood_description) + '80',
          data: Array(7).fill(0)
        })
      }
      //Find index of specific object using findIndex method.    
      objIndex = dayArr.findIndex((obj => obj.label == mood_description))
      dayArr[objIndex].data[k] = d_map[k][mood_description];
    }
  });

  // month data
  var m_map = {};
  barChartData.forEach(function (val) {
    const m = new Date(val.entry_timestamp);
    let mth = m.getMonth();
    m_map[mth] = d_map[mth] || {};
    m_map[mth][val.mood_description] = m_map[mth][val.mood_description] || 0;
    m_map[mth][val.mood_description]++;
  });

  var mthArr = [];
  const mthly = Object.keys(m_map).map(k => {
    for (var mood_description in m_map[k]) {
      if (!checkLabel(mthArr, mood_description)) {
        mthArr.push({
          label: mood_description,
          backgroundColor: '#' + getColourByMoodname(barChartData, mood_description) + '80',
          data: Array(12).fill(0)
        })
      }
      //Find index of specific object using findIndex method.    
      objIndex = mthArr.findIndex((obj => obj.label == mood_description))
      mthArr[objIndex].data[k] = m_map[k][mood_description];
    }
  });

  // Reformat the grouped data and add in the relevant html colours
  var countsExtended = Object.keys(counts).map(k => {
    return {
      name: k,
      count: counts[k],
      colour: '#' + getColourByMoodname(barChartData, k) + '80'
    };
  });

  // Map to arrays for chart.js
  const labels = countsExtended.map((x) => x.name)
  const data = countsExtended.map((x) => x.count)
  const colour = countsExtended.map((x) => x.colour)

  moodLabel = labels
  countData = data
  colourHtml = colour
  dayDataset = dayArr
  mthDataset = mthArr
}