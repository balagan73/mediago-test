$( document ).ready(function() {
	$("#show").click(function(){
    	$("#register").toggle(300);
    	if ($("#show").text() == "▼") {
    		$("#show").text("▲");
    	}
    	else $("#show").text("▼");
	});
});