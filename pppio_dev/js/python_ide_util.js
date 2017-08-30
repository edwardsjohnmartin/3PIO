function outf(text) { 
	if (text && typeof(text) != "undefined") {
		text = text.replace("<", "&lt;").replace(">", "&gt;");
		// console.log(text);
		var mypre = document.getElementById("output"); 
		// console.log(mypre);
		mypre.innerHTML = mypre.innerHTML + text;
	}
} 
function inf(prompt) {
	// Must copy the prompt string for some reason
  return window.prompt(String(prompt));
}
