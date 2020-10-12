import { CreateShadowLineChart } from './charts-shadowLineChart.js';
import { CreateSimpleLineChart } from './charts-simpleLineChart.js';

import 'https://www.chartjs.org/dist/2.9.3/Chart.js';
//import 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js';

$(window).on("load", function () {
   $("canvas[id^='simpleLineChart']").each(function (i, el) {
      let nodeId = el.id.split("-")[1];
      setTimeout(() => {
         let simpleLineChart = CreateSimpleLineChart(el.id, nodeId);
         setTimeout(() => { 
            UpdateChartJSData(simpleLineChart, nodeId); 
         }, 500);
      }, 500);

   });

   $("div[id^='shadowLineChart']").each(function (i, el) {
      let nodeId = el.id.split("-")[1];
      setTimeout(() => { let shadowLineChart = CreateShadowLineChart(el.id, nodeId); 
         setTimeout(() => { 
            UpdateChartistData(shadowLineChart, nodeId); 
         }, 500);
      }, 500);
   });
});


function UpdateChartJSData(_chart, nodeId) {

   $.ajax({
      url: window.location.origin + '/data/node',
      type: 'GET',
      data: {
         //'numberOfWords' : 10
         nodeId: nodeId
      },
      dataType: 'json',
      success: function (dataset) {
         function updateData(element, index, array) {
            _chart.config.options.title.display = false;
            // console.log(element);
            _chart.config.data.datasets[index].data = element;
         }

         dataset.forEach(updateData);
         _chart.update();
      },
      error: function (request, error) {
         console.log("Request: " + JSON.stringify(request));
      }
   });
}


function UpdateChartistData(_chart, nodeId) {
   $.ajax({
      url: window.location.origin + '/data/node',
      type: 'GET',
      data: {
         //'numberOfWords' : 10
         nodeId: nodeId
      },
      dataType: 'json',
      success: function (dataset) {

         function updateData(element, index, array) {
            dataset[0][index].x = new Date(element.x);
         }
         if (dataset[0] != null) {
            dataset[0].forEach(updateData);
            _chart.data.series[0].data = dataset[0];
            _chart.update();
         }
      },
      error: function (request, error) {
         console.log("Request: " + JSON.stringify(request));
      }
   });
}
