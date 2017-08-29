window.onload = function() {
  if(document.getElementsByName("name")[0] != undefined){
    document.getElementsByName("name")[0].focus();
  } else {
    document.getElementsByTagName("textArea")[1].focus();
  }
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