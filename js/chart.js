var moodLabel = [],
    countData = [],
    colourHtml = []

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
      }
    }
  })
}
moodPieChart()

async function moodBarChart() {
  await getChartMoodData()

  const ctx = document.getElementById('myBarChart').getContext('2d');

  const chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'bar',

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
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      },
    }
  })
}
moodBarChart()

function getColourByMoodname(obj, mood_description) {
  // iterate over each element in the array
  for (var i = 0; i < obj.length; i++){
    // look for the entry with a matching `mood_name` value
    if (obj[i].mood_description == mood_description){
      return obj[i].html_colour
    }
  } 
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

  console.log(counts)

  // Reformat the grouped data and add in the relevant html colours
  var countsExtended = Object.keys(counts).map(k => {
    return {
      name: k,
      count: counts[k],
      colour: '#' + getColourByMoodname(barChartData, k) + '80'
    };
  });

  console.log(countsExtended);

  // Map to arrays for chart.js
  const labels = countsExtended.map((x) => x.name)
  const data = countsExtended.map((x) => x.count)
  const colour = countsExtended.map((x) => x.colour)

  moodLabel = labels
  countData = data
  colourHtml = colour
}