
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
});

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
	
	/*$(function() {*/
    $( "#radioset" ).buttonset();
  /*});*/
  
  $('#datetimepicker').datetimepicker();
	
	});