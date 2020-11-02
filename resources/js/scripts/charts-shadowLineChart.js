// /*
// * Chartist - Chart
// 
// Line chart
// ------------------------------

function CreateShadowLineChart(_strId, _nodeId) {
  var unit = '';
  var primaryColor = '#fff';
  var secondaryColor = '#000';

  let TotalTransactionLine = new Chartist.Line(
    "#" + _strId,
    {
      series: [
        {
          data: []
        }
      ]
    },
    {
      chartPadding: 0,
      axisX: {
        showLabel: true,
        showGrid: false,
        type: Chartist.FixedScaleAxis,
        divisor: 5,
        labelInterpolationFnc: function (value) {
          return moment(value).format('MM-DD HH:mm:ss');
        }
      },
      axisY: {
        showLabel: true,
        showGrid: true,
        scaleMinSpace: 40
      },
      lineSmooth: Chartist.Interpolation.simple({
        divisor: 2
      }),
      plugins: [
        Chartist.plugins.tooltip({
          class: "total-transaction-tooltip",
          appendToBody: true,
          transformTooltipTextFnc: function (tooltip) {
            var xy = tooltip.split(",");
            return moment(xy[2]).format('DD.MMM HH:mm:ss') + '<br>' + xy[1] + unit;
          }
        })
      ],
      fullWidth: true
    }
  )

  $.ajax({
    url: window.location.origin + '/api/node/meta',
    type: 'GET',
    data: {
      //'numberOfWords' : 100
      nodeId: _nodeId
    },
    dataType: 'json',
    success: function (metaset) {
      unit = metaset.fields[0].unit;
      primaryColor = metaset.fields[0].primarycolor;
      secondaryColor = metaset.fields[0].secondarycolor;
      TotalTransactionLine.on("created", function (data) {
        let defs = data.svg.querySelector("defs") || data.svg.elem("defs")
        defs
          .elem("linearGradient", {
            id: "lineLinearStats",
            x1: 0,
            y1: 0,
            x2: 1,
            y2: 0
          })
          .elem("stop", {
            offset: "0%",
            "stop-color": primaryColor + '19'
          })
          .parent()
          .elem("stop", {
            offset: "10%",
            "stop-color": primaryColor + 'ff'
          })
          .parent()
          .elem("stop", {
            offset: "30%",
            "stop-color": primaryColor + 'ff'
          })
          .parent()
          .elem("stop", {
            offset: "95%",
            "stop-color": secondaryColor + 'ff'
          })
          .parent()
          .elem("stop", {
            offset: "100%",
            "stop-color": secondaryColor + '19'
          })
        return defs

      });
      UpdateChartistData(TotalTransactionLine, _nodeId)
    },
    error: function (request, error) {
      console.log("Request: " + JSON.stringify(request));
    }
  });
};

const UpdateChartistData = (_chart, nodeId) => {

  $.ajax({
    url: window.location.origin + '/api/node/data',
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

export { CreateShadowLineChart };