var moodLabel = [],
    countData = [],
    colourHtml = [],
    dayLabel = []

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
      scales: {
          yAxes: [{
              ticks: {
                  beginAtZero: true
              }
          }]
      }
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

function getExtractMonth(obj) {
  // iterate over each element in the array
  for (var i = 0; i < obj.length; i++){
    const d = new Date(obj[i].entry_timestamp);
    let name = d.getMonth();
    console.log(name);
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

  // Test group by month, then sub-group by mood_description
  function GroupByMonth(array, property) {
    return array.reduce((acc, obj) => {
      let key = new Date(obj[property]).getMonth();
      acc[key] = acc[key] || [];
      acc[key].push(obj);
      return acc;
    }, {});
  }

  var map = {}; barChartData.forEach(function(val){
    const d = new Date(val.entry_timestamp);
    let day = d.getMonth();
    map[day] = map[day] || {};
    map[day][val.mood_description] = map[day][val.mood_description] || 0;
    map[day][val.mood_description]++;
  });

  var output = Object.keys(map).map(function(key){
    var tmpArr = [];
    for(var mood_description in map[key])
    {
       tmpArr.push( [ mood_description, map[key][mood_description] ] )
    }
    return { day : key, mood: tmpArr  };
  })

  console.log(output);

  const labels_day = output.map((x) => x.day)
  dayLabel = labels_day
  console.log(labels_day);

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