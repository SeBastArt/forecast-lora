import { CreateShadowLineChart } from './charts-shadowLineChart.js';
import { CreateChartJs } from './charts-simpleLineChart.js';

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
            CreateChartJs(el.id, nodeId);
         })
      });

      $("div[id^='shadowLineChart']").each(function (i, el) {
         let nodeId = el.id.split("-")[1];
         $(function () {
            CreateShadowLineChart(el.id, nodeId);
         })
      });
   });

   const CreateMiniChart = async (nodeId) => {
      try {
         const dataset = await getData(window.location.origin + '/api/node/data', nodeId);
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
               height: "45",
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

   function getData(ajaxurl, nodeId) {
      return $.ajax({
         url: ajaxurl,
         type: 'GET',
         xhrFields: {
            withCredentials: true
         },
         data: {
            //'numberOfWords' : 10
            nodeId: nodeId
         },
         dataType: 'json',
      });
   };

})(window, document, jQuery);