/*
todo: n of axis should be one of arguments
*/
var drawRadarChart = function(dataset, label, targetId)
{
    var w = 200,
        h = 200,
        margin = 40,
        fontPropotion = 0.1;

    var target = d3.select('#'+targetId)
                .append('svg')
                .attr('width', w+margin)
                .attr('height', h+margin);
    var grid = [
          [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
          [2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2],
          [3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3],
          [4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4],
          [5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5]
        ];

    var axis = [5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5];

    var line = d3.svg.line()
                 .x(function(d, i){ return w/10 * d * Math.cos(2 * Math.PI / grid[0].length * i - (Math.PI / 2)) + ((w+margin)/2); })
                 .y(function(d, i){ return h/10 * d * Math.sin(2 * Math.PI / grid[0].length * i - (Math.PI / 2)) + ((h+margin)/2); })
                 .interpolate('linear');
                 
    target.selectAll('path')
       .data(dataset)
       .enter()
       .append('path')
       .attr('d', function(d){
         return line(d)+"z";
       })
       .attr("stroke", "black")
       .attr("stroke-width", 2)
       .attr('fill', 'none');
    target.selectAll("path.grid")
       .data(grid)
       .enter()
       .append("path")
       .attr("d", function(d,i){
         return line(d)+"z";
       })
       .attr("stroke", "black")
       .attr("stroke-dasharray", "2")
       .attr('fill', 'none');
    target.selectAll("path.axis")
       .data(axis)
       .enter()
       .append("line")
       .attr("x1", ((w+margin)/2))
       .attr("y1", ((h+margin)/2))
       .attr("x2", function(d,i){
         return w/10 * d * Math.cos(2 * Math.PI / grid[0].length * i - (Math.PI / 2)) + ((w+margin)/2);
       })
       .attr("y2", function(d,i){
         return h/10 * d * Math.sin(2 * Math.PI / grid[0].length * i - (Math.PI / 2)) + ((h+margin)/2);
       })
       .attr("stroke", "black")
       .attr("stroke-dasharray", "2")
       .attr('fill', 'none');
    target.selectAll("path.label")
       .data(label)
       .enter()
       .append("text")
       .text(function(d,i){
          return d;
       })

       .attr("x", function(d,i){
         return w/10 * (5+0.8*fontPropotion) * Math.cos(2 * Math.PI / grid[0].length * i - (Math.PI / 2)) + ((w+margin)/2);
       })
       .attr("y", function(d,i){
         return h/10 * (5+0.8*fontPropotion) * Math.sin(2 * Math.PI / grid[0].length * i - (Math.PI / 2)) + ((h+margin)/2);
       })
       .attr("font-size", (w*fontPropotion)+"px");

    return;
};


var adjustDataset = function(dataset)
{
  var adjustedDataset = [];
  for(var i=0; i<dataset[0].length; i++)
  {
    adjustedDataset.push(dataset[0][i]/20);
  }
  return [adjustedDataset];
};



var dataset = [
    [48,56,8,32,10,37,26,42,97,58,82,16,55,73,69,61,67,85,94,74,64]
];//21

var label = [
  '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21'
];

dataset = adjustDataset(dataset);

drawRadarChart(dataset, label, "memberp");




