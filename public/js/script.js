$( document ).ready(function(){
  if($("#cornerLines").length>0){
    
  var canvas = $("#cornerLines").get(0);
  var cs = getComputedStyle($("main").get(0));
  prepWidth=parseInt(cs.getPropertyValue('width'), 10);
  prepHeight=parseInt(cs.getPropertyValue('height'), 10);
  canvas.width = prepWidth;
  canvas.height = prepHeight;
  ctxW1pc=parseInt(canvas.width)/100;
  ctxH1pc=parseInt(canvas.height)/100;
  var ctx = canvas.getContext("2d");
ctx.strokeStyle = "#fff";
ctx.beginPath();
ctx.moveTo(0, Math.floor(ctxH1pc*20));
ctx.lineTo(0, 0);
ctx.lineTo(Math.floor(ctxW1pc*15), 0);
ctx.stroke();

ctx.beginPath();
ctx.moveTo(Math.floor(ctxW1pc*100), Math.floor(ctxH1pc*80));
ctx.lineTo(Math.floor(ctxW1pc*100), Math.floor(ctxH1pc*100));
ctx.lineTo(Math.floor(ctxW1pc*85), Math.floor(ctxH1pc*100));
ctx.stroke();
//prvá hrana
  }
});
/*$data={
  labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
  series: [
    [12, 9, 7, 8, 5],
    [2, 1, 3.5, 7, 3],
    [1, 3, 4, 5, 6]
  ]
}, {
  fullWidth: true,
  chartPadding: {
    right: 40
  }
};
new Chartist.Line('#chart1', $data)
new Chartist.Line('#chart2', $data)
*/

