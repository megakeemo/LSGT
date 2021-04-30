//Toggles the Control box
$(document).ready(function()
{
  $(".hide").click(function(){
    $("#control-col,#address-box, #coords-panel, #results-panel").fadeToggle();
  });
  
 $(".clear").click(function(){
	  $("#latlng1, #latlng2, #address1, #address2, #results-area, #coords-area").val("");
	  $("#results-panel, #results-area").css("display", "none");
	  deleteOverlays();
  })
  
  $("#results-panel").css("display","none");
});