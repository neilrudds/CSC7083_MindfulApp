var moodLabel = [], countData = []

async function moodChart() {
  await getChartMoodData()

const ctx = document.getElementById('myChart').getContext('2d');

const chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'pie',

    // The data for our dataset
    data: {
        labels: moodLabel,
        datasets: [{
            label: 'Mood Count',
            backgroundColor: ["red", "blue", "green", "orange", "yellow", "blue"],
            data: countData
        }
      ]
    },

    // Configuration options go here
    options: {
      tooltips: {
        mode: 'index'
      }
    }
})}

moodChart()

async function getChartMoodData() {


  mySessionToken
    const moodLogUrl = `https://localhost:3000/api/v1/log/${myUserId}`
  
    const response = await fetch(moodLogUrl, {
      headers: {Authorization: `Bearer ${mySessionToken}`}
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

    // 
    var countsExtended = Object.keys(counts).map(k => {
        return {name: k, count: counts[k]}; });
      
    console.log(countsExtended);

    const labels = countsExtended.map((x) => x.name)
    const data = countsExtended.map((x) => x.count)
  
   moodLabel = labels
   countData = data
  }