
$(document).ready(function() {
	
	$('#wait_1').hide();
	$('#select_1').change(function(){
		$('#wait_1').show();
		$('#result_1').hide();
		$.get("func.php", {
			func: "select_1",
			selection: $('#select_1').val()
		}, 
		function(response){
			$('#result_1').fadeOut();
			setTimeout("finishAjax('result_1', '"+escape(response)+"')", 400);
      });
    	return false;
	});
})

function finishAjax(id, response) {
  $('#wait_1').hide();
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn();
}
function finishAjax_tier_three(id, response) {
  $('#wait_2').hide();
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn();
}

$(document).ready(function() {
/*needs to go inside a document/window ready function*/
$("#plot").click(function(){

var mission = $("#select_1").val(); /*or document.getElementById("select_1").value;*/
var nodeId = $("#select_2").val();
var tlmId = $("#select_3").val();
var canBus = $('input[name=canbus]:checked').val();
var dateTimeFrom = $("#from").val();
var dateTimeTo = $("#to").val();

var queryInfo = {}; /* may be change it to =Array[];
  search how to create an AJAX or JSON array*/
queryInfo["mission"] = mission;
queryInfo["nodeId"] = nodeId;
queryInfo["tlmId"] = tlmId;
queryInfo["canBus"] = canBus;
queryInfo["dateTimeFrom"] = dateTimeFrom;
queryInfo["dateTimeTo"] = dateTimeTo;

 var test="works";
 
/*try the get or the post directly*/

$.get("func.php", {
func:"submit",
queryInfoArray: queryInfo
},
function(data){
	alert("Data loaded: " + data)
});

/*
$.ajax({
	url: "getData.php",
	type:"POST",
	data: {"test": "test"} /*PHP: <? $myArray = $_REQUEST['queryInfoArray']; ?>*/
	/*});

	/*var queryResult = <?php echo json_encode($result); ?>; Doesn't work*/
	/*alert(JSON.stringify(queryInfo));


	

/* Trying method above
var xhr;

 if (window.XMLHttpRequest) { // Mozilla, Safari, ...  
    xhr = new XMLHttpRequest();  
} else if (window.ActiveXObject) { // IE 8 and older  
    xhr = new ActiveXObject("Microsoft.XMLHTTP");  
}  

var data = "mission_name=" + mission ....//not completed how I can add more variables instead of only 1

     xhr.open("POST", "book-suggestion.php", true);   
     xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");                    
     xhr.send(data);  
     xhr.onreadystatechange = display_data;  
    function display_data() {  
     if (xhr.readyState == 4) {  
      if (xhr.status == 200) {  
       //alert(xhr.responseText);        
      document.getElementById("suggestion").innerHTML = xhr.responseText;  
      } else {  
        alert('There was a problem with the request.');  
      }  
     }  
    }  
	*/
})
})

  

$(function() {
		
		 $( "#from" ).datetimepicker({
		format: 'Y-m-d H:i:s',
	 
      onClose: function( selectedDate ) {
        $( "#to" ).datetimepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datetimepicker({
      format: 'Y-m-d H:i:s',
	  defaultDate: "+1w",
      changeMonth: false,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#from" ).datetimepicker( "option", "maxDate", selectedDate );
      }
    });
	
	
	$('#image_button').click(function(){
		('#from').datetimepicker('show');
	});
	

  $('#datetimepicker').datetimepicker();
	
	});
	
	
$(function() {
    $( ".radioButton" ).buttonset();
  });