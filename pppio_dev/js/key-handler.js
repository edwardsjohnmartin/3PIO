window.onload = function() {
    document.getElementsByTagName('body')[0].onkeyup = function(e) { 
       var ev = e || event;
       //ctrl + enter
       if(event.getModifierState("Control") && ev.keyCode == 13) {
          document.getElementById("runButton").click();
       //ctrl + /
       } 
    //    else if(event.getModifierState("Control") && ev.keyCode == 191) {
    //       document.getElementById("runButton").click();
    //    }
    }

 };
