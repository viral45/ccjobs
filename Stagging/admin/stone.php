<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
	die();
}

include("config.php"); 

$page = "schedule";

include("header.php"); 

?>

<div class="page-header">
    <h1>
      <span id="pageTitle">Stone Schedule</span>
      <button type="button" id="add-btn" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New</button>
    </h1>
</div>

<div id="alert"></div>

<div id='page-content'></div>
<div id='loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>

<br />

<div class="modal fade" tabindex="-1" role="dialog" id="schedule-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">
	  	<div id="modal-alert"></div>
	  	<div id='modal-content'></div>
	  	<div id='modal-loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type='text/javascript'>

	$('form').on('submit', function(e){
		e.preventDefault();
	});
	
	$(document).ready(function(){

		showWeek();
		
		var currentWeekStart = ""; 

		$('#add-btn').click(function(){
			addEditSchedule("add", 0);
		});

		function showWeek(weekStart){
			$('#loader-image').show();
			$('#add-btn').show();
			
			$('#page-content').hide();
			$('#alert').hide();
			
			$('#page-content').load('stone-calendar.php', { weekstart: weekStart }, function(){ 
				$('#loader-image').hide(); 
				$('#page-content').fadeIn('slow');

				$(".entry").sortable({
					connectWith: ".entry",
					containment: "#caltable",
					update: function(event, ui){

						var scheduleid = ui.item.attr('data-schedule-id');
						var userid = $(this).attr('data-user-id');
						var scheduledate = $(this).attr('data-date');
						
						if (ui.sender){
							$.post("stone-crud.php", {action: "move", stonescheduleid: scheduleid, userid: userid, scheduledate: scheduledate})
								.done(function(data) {
									//$('#schedule-modal').modal('hide');
									//showWeek(currentWeekStart);
								});
						}
						var sortorder = $(this).sortable("toArray", {attribute: "data-schedule-id"});
						
						$.post("stone-crud.php", {action: "sort", sortorder: sortorder})
							.done(function(data) {
								//$('#schedule-modal').modal('hide');
								//showWeek(currentWeekStart);
							});

					}
				}).disableSelection();
				
				$('.delete-btn').click(function(){
					deleteJob($(this).val());
				});

				$('.week-btn').click(function(){
					event.preventDefault();
					currentWeekStart = $(this).val();
					showWeek($(this).val());
				});

				$('.calendar-entry').dblclick(function () {
					var scheduleid = $(this).attr('data-schedule-id');
					addEditSchedule("edit", scheduleid);
				});

				$('.add-entry-btn').click(function(){
					
					var scheduledate = $(this).attr("data-schedule-date");
					var userid = $(this).val();

					addEditSchedule("add", 0, scheduledate, userid);

				});

			});
		}
		

		function addEditSchedule(action, scheduleid, scheduledate, userid){
			$('#modal-content').load('stone-add-edit.php', { action: action, stonescheduleid: scheduleid }, function(){ 
				
				if (action == "add")
					$("#schedule-modal").find('.modal-title').text('Add Stone Schedule Entry')
				else if (action == "edit")
					$("#schedule-modal").find('.modal-title').text('Edit Stone Schedule Entry')

				$('#modal-loader-image').hide(); 
				$('#modal-content').fadeIn('slow');
				
				$("#schedule-form").validate({
					rules: {
						inputJobID: {
							required: "#inputDescription:blank"
						},
						inputDescription: {
							required: "#inputJobID:blank"
						}
					}
				});

				$('.selectpicker').selectpicker({liveSearch: true});
				$('input[name="inputScheduleDate"]').daterangepicker({format: 'DD-MM-YYYY' , singleDatePicker: true,showDropdowns: true});
				
				$('#delete-btn').click(function(){
					deleteJob($(this).val());
				});

				$('#viewjob-btn').click(function(){
					//alert($("#inputJobID").val());
					if ($("#inputJobID").val() != "")
						window.open('../job.php?jobid='+$("#inputJobID").val()+'#installer', '_blank');

				});

				//$('#inputDescription').prop('disabled', false);


				$('#schedule-modal').modal('show');

				if ($('#inputJobID').val() != ''){
					$.post("stone-crud.php", { action: 'getbuilder', jobid: $('#inputJobID').val() }) 
						.done(function(data){
							$('#inputBuilder').val(data);
					});
				}

				if (userid)
					$("#inputUserID").val(userid);
				if (scheduledate)
					$("#inputScheduleDate").val(scheduledate);
				
			});
		}

		$(document).on('change', '#inputJobID', function(){ 
			if ($(this).val() != ''){
				$.post("stone-crud.php", { action: 'getbuilder', jobid: $(this).val() }) 
					.done(function(data){
						$('#inputBuilder').val(data);
				});
			}
			else	
				$('#inputBuilder').val('');

		//		$('#inputDescription').val('');
		//		$('#inputDescription').prop('disabled', true);
		//	}
		//	else{
		//		$('#inputDescription').prop('disabled', false);
		//	}	
		});

		$(document).on('submit', '#schedule-form', function() {
			$('#modal-content').hide();
			$('#modal-loader-image').show();
			 
			$.post("stone-crud.php", $(this).serialize())
				.done(function(data) {
					$('#schedule-modal').modal('hide');
					showWeek(currentWeekStart);
					
				});
					 
			return false;
		});
		
		
		function deleteJob(deleteid){
			$.confirm({
				text: "Are you sure you want to delete this schedule entry?",
				confirm: function() {			
					
					$.post("stone-crud.php", { action: 'delete', deleteid: deleteid }) 
						.done(function(data){
							$('#schedule-modal').modal('hide');
							showWeek(currentWeekStart);
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}


		$.validator.setDefaults({
			highlight: function(element) {
				$(element).closest('.form-group').addClass('has-error');
			},
			unhighlight: function(element) {
				$(element).closest('.form-group').removeClass('has-error');
			},
			errorElement: 'span',
			errorClass: 'help-block',
			errorPlacement: function(error, element) {
				if(element.parent('.input-group').length) {
					error.insertAfter(element.parent());
				} else {
					error.insertAfter(element);
				}
			}
		});

		$.validator.methods.date = function (value, element) {
			return this.optional(element) || moment(value, 'DD/MM/YYYY').isValid();
		};

    
	});
	
</script>

<?php include("footer.php"); ?>