window.onload = function() {
    $(window).keydown(function(event) {
        if(event.ctrlKey && event.keyCode == 13) { 
          document.getElementById("runButton").click();
          event.preventDefault(); 
        } 
        if(event.ctrlKey && event.keyCode == 191) {
          var x = document.getElementById("infoAlert");
          if(x != null){
            if(x.children[0].href != undefined){
              window.location.href = x.children[0].href;
            }
          }
        }
      });
 };