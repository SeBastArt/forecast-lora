import { CreateShadowLineChart } from './charts-shadowLineChart.js';
import { CreateSimpleLineChart } from './charts-simpleLineChart.js';

import 'https://www.chartjs.org/dist/2.9.3/Chart.js';

(function (window, document, $) {

   $(window).on("load", function () {
      $("div[id^='minichart']").each(function (i, el) {
         let nodeId = el.id.split("-")[1];
         CreateMiniChart(nodeId);
      });

      $("canvas[id^='simpleLineChart']").each(function (i, el) {
         let nodeId = el.id.split("-")[1];
         $(function () {
            let simpleLineChart = CreateSimpleLineChart(el.id, nodeId);
            UpdateChartJSData(simpleLineChart, nodeId);
         })
      });

      $("div[id^='shadowLineChart']").each(function (i, el) {
         let nodeId = el.id.split("-")[1];
         $(function () {
            let shadowLineChart = CreateShadowLineChart(el.id, nodeId);
            UpdateChartistData(shadowLineChart, nodeId);
         })
      });
   });

   const CreateMiniChart = async (nodeId) => {
      try {
         const dataset = await getData(window.location.origin + '/data/node', nodeId);
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
      } catch (err) {
         console.log(err);
      }
   }

   const UpdateChartJSData = async (_chart, nodeId) => {

      try {
         const dataset = await getData(window.location.origin + '/data/node', nodeId);
         function updateData(element, index, array) {
            _chart.config.options.title.display = false;
            // console.log(element);
            _chart.config.data.datasets[index].data = element;
         }

         dataset.forEach(updateData);
         _chart.update();
         
      } catch (err) {
         console.log(err);
      }
   }

   const UpdateChartistData = async (_chart, nodeId) => {
      try {
         const dataset = await getData(window.location.origin + '/data/node', nodeId);
         function updateData(element, index, array) {
            dataset[0][index].x = new Date(element.x);
         }
         if (dataset[0] != null) {
            dataset[0].forEach(updateData);
            _chart.data.series[0].data = dataset[0];
            _chart.update();
         }
         
      } catch (err) {
         console.log(err);
      }
   }

   function getData(ajaxurl, nodeId) {
      return $.ajax({
         url: ajaxurl,
         type: 'GET',
         data: {
            //'numberOfWords' : 10
            nodeId: nodeId
         },
         dataType: 'json',
      });
   };

})(window, document, jQuery);