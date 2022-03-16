<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['is_maint'] <> 1){
	header("location:index.php");
	die();
}

include("config.php"); 

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.ico">

    <title>Challenge Cabinets Job Management System</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="css/jquery-ui.min.css">
	<link rel="stylesheet" href="css/bootstrap-select.min.css">
	<link rel="stylesheet" href="css/daterangepicker-bs3.css">
	<link rel="stylesheet" href="css/fullcalendar.min.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
	<?php
  	
  		if(isset($_SESSION['logged_in'])){
  			include("header.php");
  		} 
  	?>
    <div class="container">
		<div class="row">
			<div class="col-xs-6 col-sm-4 col-md-3">
				<img src="img/logo_large.png" class="img-responsive" alt="Challenge Cabinets">
				<br>
			</div>
			<div class="col-sm-8 col-md-9">
				<ul class="nav nav-pills pull-right">
					<li role="presentation"><a href="index.php">Home</a></li>
					<li role="presentation"><a href="maint-jobs.php">Jobs</a></li>
					<li role="presentation"><a href="maintschedule.php">Schedule</a></li>
				</ul>
				
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="well">				
						Current User: <strong><?php echo $_SESSION['full_name'] ?></strong>
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading">
						<span>Maintenance Schedule</span>
						
					</div>
					<div class="panel-body">
						<div class="form-inline">
							<button type="button" id="add-btn" class="btn btn-primary"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New</button>
							<a href="maint-jobs.php" class="btn btn-success"><span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> Jobs</a>

							<div class="form-group">
								<label for="maintuserid">&nbsp;Schedule</label>
								<select class="form-control" id="maintuserid" name="maintuserid">
									<?php
										$query = "SELECT UserID, FullName FROM tblUser WHERE IsMaint <> 0 AND Active <> 0 ORDER BY FullName";
										$result = $mysqli->query($query);			

										while($row = $result->fetch_array())
										{										
											$selected = ($row['UserID'] == $_SESSION['user_id']) ? " SELECTED" : ""; 	
											echo "<option value=" . $row['UserID'] . " $selected>" . $row['FullName'] . "</option>";
										}
									?>
								</select>
							</div>
						</div>
						<div class="clearfix">
						
							<div id="alert"></div>

							<div id='page-content'><br><div id='calendar'></div></div>
							<div id='loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>

							<br />

							<div class="modal fade" tabindex="-1" role="dialog" id="maintschedule-modal">
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
							</div>

					</div>
				</div>

			</div>
		</div>
    </div> <!-- /container -->

    <script src="js/jquery-1.11.3.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/additional-methods.min.js"></script>
    <script src="js/jquery.confirm.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/moment.min.js"></script>
	<script src="js/daterangepicker.js"></script>
	<script src="js/fullcalendar.min.js"></script>

	<script type='text/javascript'>

		$('form').on('submit', function(e){
			e.preventDefault();
		});
		
		$(document).ready(function(){

			var source = 'maintschedule-crud.php?action=list&userid='+$('#maintuserid').val();

			showWeek();
			
			var currentWeekStart = ""; 

			$('#add-btn').click(function(){
				addEditMaintSchedule("add", 0);
			});

			$('#maintuserid').change(function(){ 
				newsource = 'maintschedule-crud.php?action=list&userid='+$('#maintuserid').val(),
				$('#calendar').fullCalendar('removeEventSource', source)
				$("#calendar").fullCalendar('addEventSource', newsource);
				source = newsource;
			});

			function showWeek(weekStart){
				$('#loader-image').hide();
				$('#add-btn').show();
				
				$('#calendar').fullCalendar({
					defaultView: 'agendaWeek',
					header: {
						left: 'prev,next today',
						center: 'title',
						right: ''
						//right: 'month,agendaWeek,agendaDay'
					},
					navLinks: false, // can click day/week names to navigate views
					editable: true,
					eventLimit: false, // allow "more" link when too many events
					minTime: "06:00:00",
					maxTime: "16:00:00",
					slotDuration: '00:15',
					slotLabelInterval: '00:30',
					hiddenDays: [ 0, 6 ],
					height: 'auto',
					allDaySlot: false,
					columnFormat: 'ddd D/M',

					//events: 'maintschedule-crud.php?action=list',
					events: source,

					dayClick: function(date, jsEvent, view) {
						var maintscheduledate = date.format("DD-MM-YYYY h:mm A");
					 	addEditMaintSchedule("add", 0, maintscheduledate);
					},
					eventClick: function(calEvent, jsEvent, view) {
						var maintscheduleid = calEvent.id;
						addEditMaintSchedule("edit", maintscheduleid);
					},
					eventDrop: function(event, delta, revertFunc) {					
						updateEvent(event.id, event.start.format("DD-MM-YYYY h:mm A"), event.end.format("DD-MM-YYYY h:mm A"));
					},
					eventResize: function(event, delta, revertFunc) {
						updateEvent(event.id, event.start.format("DD-MM-YYYY h:mm A"), event.end.format("DD-MM-YYYY h:mm A"));
					}
				});

				//$('#page-content').hide();
				//$('#alert').hide();
				
				// $('#page-content').load('maintschedule-calendar.php', { weekstart: weekStart }, function(){ 
				// 	$('#loader-image').hide(); 
				// 	$('#page-content').fadeIn('slow');

				// 	$(".entry").sortable({
				// 		connectWith: ".entry",
				// 		containment: "#caltable",
				// 		update: function(event, ui){

				// 			var maintscheduleid = ui.item.attr('data-maintschedule-id');
				// 			var maintscheduledate = $(this).attr('data-date');
							
				// 			if (ui.sender){
				// 				$.post("maintschedule-crud.php", {action: "move", maintscheduleid: maintscheduleid, maintscheduledate: maintscheduledate})
				// 					.done(function(data) {
				// 						//$('#maintschedule-modal').modal('hide');
				// 						//showWeek(currentWeekStart);
				// 					});
				// 			}
				// 			var sortorder = $(this).sortable("toArray", {attribute: "data-maintschedule-id"});
							
				// 			$.post("maintschedule-crud.php", {action: "sort", sortorder: sortorder})
				// 				.done(function(data) {
				// 					//$('#maintschedule-modal').modal('hide');
				// 					//showWeek(currentWeekStart);
				// 				});

				// 		}
				// 	}).disableSelection();
					
				// 	$(".calendar-entry").resizable({
				// 		handles: 's',
				// 		minHeight: 50,
				// 		grid: [50,50]
				// 	});
					
					// $('.delete-btn').click(function(){
					// 	deleteJob($(this).val());
					// });

					// $('.week-btn').click(function(){
					// 	event.preventDefault();
					// 	currentWeekStart = $(this).val();
					// 	showWeek($(this).val());
					// });

					// $('.calendar-entry').dblclick(function () {
					// 	var maintscheduleid = $(this).attr('data-maintschedule-id');
					// 	addEditMaintSchedule("edit", maintscheduleid);
					// });

					// $('.add-entry-btn').click(function(){
						
					// 	var maintscheduledate = $(this).attr("data-maintschedule-date");

					// 	addEditMaintSchedule("add", 0, maintscheduledate);

					// });

				//});
			}
			

			function addEditMaintSchedule(action, maintscheduleid, maintscheduledate){
				$('#modal-content').load('maintschedule-add-edit.php', { action: action, maintscheduleid: maintscheduleid }, function(){ 
					
					if (action == "add")
						$("#maintschedule-modal").find('.modal-title').text('Add Maintenance Schedule Entry')
					else if (action == "edit")
						$("#maintschedule-modal").find('.modal-title').text('Edit Maintenance Schedule Entry')

					$('#modal-loader-image').hide(); 
					$('#modal-content').fadeIn('slow');
					
					$("#maintschedule-form").validate({
						rules: {
							inputJob: {
								required: "#inputDescription:blank"
							},
							inputDescription: {
								required: "#inputJob:blank"
							},
							inputMaintScheduleDateEnd: { 
								greaterThan: "#inputMaintScheduleDate" 
							}
						},
						messages: {
							inputMaintScheduleDateEnd: {
								greaterThan: "End must be greater than Start"
							}
						}

					});

					if (maintscheduledate){
						$("#inputMaintScheduleDate").val(maintscheduledate);
						$("#inputMaintScheduleDateEnd").val(moment(maintscheduledate, 'DD-MM-YYYY h:mm A').add(1, 'hours').format('DD-MM-YYYY h:mm A'));
					}
						

					$('.selectpicker').selectpicker({liveSearch: true});
					$('input[name="inputMaintScheduleDate"]').daterangepicker({format: 'DD-MM-YYYY h:mm A' , singleDatePicker: true, showDropdowns: true, timePicker: true, timePickerIncrement: 15 });
					$('input[name="inputMaintScheduleDateEnd"]').daterangepicker({format: 'DD-MM-YYYY h:mm A' , singleDatePicker: true, showDropdowns: true, timePicker: true, timePickerIncrement: 15 });
					
					$('input[name="inputMaintScheduleDate"]').on('apply.daterangepicker', function(ev, picker) {
						if ($('#inputMaintScheduleDateEnd').val() == ""){
							$('#inputMaintScheduleDateEnd').val($('#inputMaintScheduleDate').val());
						}
					});

					$('#delete-btn').click(function(){
						deleteJob($(this).val());
					});

					$('#viewjob-btn').click(function(){
						if ($("#inputJob").val() != ""){
						
							if ($("#inputJob").val().slice(0,1)!='M')
								window.open('job.php?jobid='+$("#inputJob").val()+'#maintenance', '_blank');
							else
								window.open('maintjob.php?action=edit&maintjobid='+$("#inputJob").val().replace('M',''), '_blank');
						}					

					});

					//$('#inputDescription').prop('disabled', false);

					$('#maintschedule-modal').modal('show');

					if ($('#inputJob').val() != ''){
						$.post("maintschedule-crud.php", { action: 'getbuilder', jobid: $('#inputJob').val() }) 
							.done(function(data){
								$('#inputBuilder').val(data);
						});
					}
				
					
				});
			}



			function updateEvent(maintscheduleid, maintscheduledate, maintscheduledateend){
				$.post("maintschedule-crud.php", {action: "move", maintscheduleid: maintscheduleid, maintscheduledate: maintscheduledate, maintscheduledateend: maintscheduledateend})
					.done(function(data) {


					});
			}

			$(document).on('change', '#inputJob', function(){ 
				if ($(this).val() != ''){
					$.post("maintschedule-crud.php", { action: 'getbuilder', jobid: $(this).val() }) 
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

			$(document).on('submit', '#maintschedule-form', function() {
				$('#modal-content').hide();
				$('#modal-loader-image').show();
				
				$.post("maintschedule-crud.php", $(this).serialize() + "&userid=" + $('#maintuserid').val())
					.done(function(data) {
						$('#maintschedule-modal').modal('hide');
						//showWeek(currentWeekStart);
						$('#calendar').fullCalendar( 'refetchEvents' );
					});
						
				return false;
			});
			
			
			function deleteJob(deleteid){
				$.confirm({
					text: "Are you sure you want to delete this maintenance schedule entry?",
					confirm: function() {			
						
						$.post("maintschedule-crud.php", { action: 'delete', deleteid: deleteid }) 
							.done(function(data){
								$('#maintschedule-modal').modal('hide');
								//showWeek(currentWeekStart);
								$('#calendar').fullCalendar( 'refetchEvents' );
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

			$.validator.addMethod("greaterThan", 
			function(value, element, params) {

				if (!/Invalid|NaN/.test(new Date(value))) {
					return new Date(value) > new Date($(params).val());
				}

				return isNaN(value) && isNaN($(params).val()) 
					|| (Number(value) > Number($(params).val())); 
			},'Must be greater than {0}.');

		
		});
		
	</script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>

