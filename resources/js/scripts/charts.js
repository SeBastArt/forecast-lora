import { CreateShadowLineChart } from './charts-shadowLineChart.js';
import { CreateSimpleLineChart } from './charts-simpleLineChart.js';

import 'https://www.chartjs.org/dist/2.9.3/Chart.js';
//import 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js';
(function (window, document, $) {
$(window).on("load", function () {

   setTimeout(() => {
      $("div[id^='minichart']").each(function (i, el) {
         let nodeId = el.id.split("-")[1];
         setTimeout(() => {
            CreateMiniChart(nodeId);
         }, 250);
      });
   }, 200);
   

   $("canvas[id^='simpleLineChart']").each(function (i, el) {
      let nodeId = el.id.split("-")[1];
      setTimeout(() => {
         let simpleLineChart = CreateSimpleLineChart(el.id, nodeId);
         setTimeout(() => {
            UpdateChartJSData(simpleLineChart, nodeId);
         }, 250);
      }, 250);
   });

   setTimeout(() => {
      $("div[id^='shadowLineChart']").each(function (i, el) {
         let nodeId = el.id.split("-")[1];
         setTimeout(() => {
            let shadowLineChart = CreateShadowLineChart(el.id, nodeId);
            setTimeout(() => {
               UpdateChartistData(shadowLineChart, nodeId);
            }, 250);
         }, 250);
      });
   }, 200);

  
});


function CreateMiniChart(nodeId){
      $.ajax({
         url: window.location.origin + '/data/node',
         type: 'GET',
         data: {
            //'numberOfWords' : 10
            nodeId: nodeId
         },
         dataType: 'json',
         success: function (dataset) {
            let datablock = []
            function updateData(element, index, array) {
               datablock.push(element.y);
            }
            dataset[0].forEach(updateData);
            $(function () {
               // Line chart ( New Invoice)
               $("#minichart-" + nodeId).sparkline(datablock, {
                  type: "line",
                  width: "100%",
                  height: "25",
                  lineWidth: 2,
                  lineColor: "#E1D0FF",
                  fillColor: "rgba(255, 255, 255, 0.2)",
                  highlightSpotColor: "#E1D0FF",
                  highlightLineColor: "#E1D0FF",
                  minSpotColor: "#00bcd4",
                  maxSpotColor: "#4caf50",
                  spotColor: "#E1D0FF",
                  spotRadius: 4
               });
            })           
         },
         error: function (request, error) {
            console.log("Request: " + JSON.stringify(request));
         }
      });


}

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

})(window, document, jQuery);