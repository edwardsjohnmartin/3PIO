window.onload = function() {
    $(window).keydown(function(event) {
        if(event.ctrlKey && event.keyCode == 13) { 
          console.log("Hey! Ctrl+Enter event captured!");
          document.getElementById("runButton").click();
          event.preventDefault(); 
        }
      });
 };