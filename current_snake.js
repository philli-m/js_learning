 var context = document.getElementById('canvas').getContext('2d');
//start position of the dot 
var x = 100;
var y = 100;

//create grid to be mapped and labeled 
 grid = [];

 //mapping the canvas grid to track collision detection and labelling all usable coordinates in the grid as false
 for (i = 0; i < 200; ++i) {
     grid[i] = [];

     for (j = 0; j < 200; ++j) {
         grid[i][j] = false;
     }
 }

 //description of movement 
 function rect(e) {
     console.log(x);

     switch (e.keyCode) {

         case 38:
             (y = y - 5);
             break;

         case 40:
             (y = y + 5);
             break;

         case 39:
             (x = x + 5);
             break;

         case 37:
             (x = x - 5);
             break;
     }

     //confirming starting location of dot and size (see above) 
     context.fillRect(x, y, 10, 10);

     //if statment within movement function to check grid coordinate is new and then labelling  contacted coordinates as true 
     if (grid[x][y] === false) {
         grid[x][y] = true;

         //stopping timer and keydown behavior then resetting the canvas  	  
     } else {
         clearInterval(timer);
         //context.clearRect(0, 0, canvas.width, canvas.height);

         for (i = 0; i < 200; ++i) {
             grid[i] = [];

             for (j = 0; j < 200; ++j) {
                 grid[i][j] = false;
             }
         }
     }

     //random color generation 
     var colors = [];

     //loop describing 3 random number generations which are then pushes to empty array 
     for (var g = 0; g < 3; g++) {
         colors.push(Math.floor(Math.random() * 255));
     }

     //calling dot to the assign random numbers as rgb colors in css format 
     context.fillStyle = "rgb(" + colors[0] + "," + colors[1] + "," + colors[2] + ")";
 }

 //timer variable to allow continuous movement      
 var timer;

 //describes movement of the dot after keypress event by repeating past movement until key is pressed again 
 document.onkeydown = function (e) {
     clearInterval(timer);
     console.log(x, y);
     timer = setInterval(rect, 50, e);

 };