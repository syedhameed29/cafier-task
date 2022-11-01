@extends('master')
@section('pageheader')
<h2>DashBoard</h2>
@stop
@section('maincontent')
<style type="text/css">
    body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
}

#chartdiv {
  width: 100%;
  height: 500px;
}

</style>
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/dataviz.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
<div id="chartdiv"></div>
<div id="piechart1" class="asset"></div>
<div id="piechart2" class="asset"></div>
<div id="piechart3" class="asset"></div>

<script type="text/javascript">
    /**
 * ---------------------------------------
 * This demo was created using amCharts 4.
 * 
 * For more information visit:
 * https://www.amcharts.com/
 * 
 * Documentation is available at:
 * https://www.amcharts.com/docs/v4/
 * ---------------------------------------
 */

// Themes begin
am4core.useTheme(am4themes_dataviz);
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.createFromConfig({

  "data": [{
    "country": "Lithuania",
    "units": 500,
    "pie": [{
      "value": 250,
      "title": "Cat #1"
    }, {
      "value": 150,
      "title": "Cat #2"
    }, {
      "value": 100,
      "title": "Cat #3"
    }]
  }, {
    "country": "Czech Republic",
    "units": 300,
    "pie": [{
      "value": 80,
      "title": "Cat #1"
    }, {
      "value": 130,
      "title": "Cat #2"
    }, {
      "value": 90,
      "title": "Cat #3"
    }]
  }, {
    "country": "Ireland",
    "units": 200,
    "pie": [{
      "value": 75,
      "title": "Cat #1"
    }, {
      "value": 55,
      "title": "Cat #2"
    }, {
      "value": 70,
      "title": "Cat #3"
    }]
  }],

  "hiddenState": {
    "properties": {
      "opacity": 0
    }
  },

  "xAxes": [{
    "type": "CategoryAxis",
    "dataFields": {
      "category": "country"
    },
    "renderer": {
      "grid": {
        "disabled": true
      }
    }
  }],

  "yAxes": [{
    "type": "ValueAxis",
    "title": {
      "text": "Units sold (M)",
    },
    "min": 0,
    "renderer": {
      "baseGrid": {
        "disabled": true
      },
      "grid": {
        "strokeOpacity": 0.07
      }
    }
  }],

  "series": [{
    "type": "ColumnSeries",
    "dataFields": {
      "valueY": "units",
      "categoryX": "country"
    },
    "tooltip": {
      "pointerOrientation": "vertical"
    },
    "columns": {
      "column": {
        "tooltipText": "Series: {name}\nCategory: {categoryX}\nValue: {valueY}",
        "tooltipY": 0,
        "cornerRadiusTopLeft": 20,
        "cornerRadiusTopRight": 20
      },
      "strokeOpacity": 0,
      "adapter": {
        "fill": function(fill, target) {
          var chart = target.dataItem.component.chart;
          var color = chart.colors.getIndex(target.dataItem.index * 3);
          return color;
        }
      },

      // pie
      "children": [{
        "type": "PieChart",
        "forceCreate": true,
        "width": "80%",
        "height": "80%",
        "align": "center",
        "valign": "middle",
        "dataFields": {
          "data": "pie"
        },
        "series": [{
          "type": "PieSeries",
          "dataFields": {
            "value": "value",
            "category": "title"
          },
          "labels": {
            "disabled": true
          },
          "ticks": {
            "disabled": true
          },
          "slices": {
            "stroke": "#ffffff",
            "strokeWidth": 1,
            "strokeOpacity": 0,
            "adapter": {
              "fill": function(fill, target) {
                return am4core.color("#ffffff");
              },
              "fillOpacity": function(fillOpacity, target) {
                return (target.dataItem.index + 1) * 0.2;
              }
            }
          },
          "hiddenState": {
            "properties": {
              "startAngle": -90,
              "endAngle": 270
            }
          }
        }]
      }]
    }
  }],

}, "chartdiv", "XYChart");

</script>
@stop