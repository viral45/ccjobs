<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
	die();
}

include("config.php"); 

$page = "jobs";

include("header.php"); 

?>

<div class="page-header">
    <h1>
      <span id="pageTitle">Jobs</span>
      <a type="button" href="projects.php" id="add-btn-project" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Project</a>
      <br>
      <button type="button" id="add-btn" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New</button>
      
    </h1>
</div>

<div id="searchPanel" class="panel panel-default">
    <div class="panel-heading">
    	<h3 class="panel-title">Search</h3>
    </div>
    <div class="panel-body">
        <form id="searchForm" class="form-horizontal">

            <div class="form-group">
            	
            	<div class="col-sm-3">
                	<label for="searchJobNo">Job No.</label>
                	<input type="text" class="form-control" id="searchJobNo" name="searchJobNo" placeholder="Job No">
                </div>
                
            	<div class="col-sm-9">
                	<label for="searchJobNo">Address</label>
                	<input type="text" class="form-control" id="searchAddress" name="searchAddress" placeholder="Address">
                </div>                
            </div>
			
            <button type="submit" id="search-btn" class="btn btn-info">Search</button>
            <button type="reset" id="reset-btn" class="btn btn-warning">Reset</button>
            
        </form>
    </div>
</div>

<div id="alert"></div>

<div id='page-content'></div>
<div id='loader-image'><img src='img/ajax-loader.gif' /> &nbsp;LOADING</div>

<br />

<script type='text/javascript'>

	$('form').on('submit', function(e){
		e.preventDefault();
	});
	
	function changePageTitle(title){
		$('#pageTitle').html(title);
	}
	
	$(document).ready(function(){

		showJobs(1);
		var currentPage = 1;
		var currentJobID = 0;
		
		$('#add-btn').click(function(){
			addEditJob("add", 0);
			$('#alert').hide();
		});
		
		$('#reset-btn').click(function(){
			$('#alert').hide();
			$('#searchForm')[0].reset();
			showJobs(1);
		});
			
		$('#search-btn').click(function(){
			showJobs(1);
			$('#alert').hide();
		});
		

		function showJobs(page){
			$('#loader-image').show();
			changePageTitle("Jobs");
			$('#add-btn').show();
			$('#searchPanel').show();
			$('#page-content').hide();
			$('#alert').hide();
			
			$('#page-content').load('job-list.php', $('#searchForm').serialize() + "&page=" + page, function(){ 
				$('#loader-image').hide(); 
				$('#page-content').fadeIn('slow');
				$('[data-toggle="tooltip"]').tooltip();
			});
		}
		
		function showJobHistory(jobid, tab){
			
			changePageTitle("Job History");
			$('#add-btn').hide();

			currentJobID = jobid;
					
			$('#searchPanel').hide();
			$('#page-content').hide();
			$('#loader-image').show();
			
			$('#page-content').load('job-history.php', { jobid: jobid }, function(){ 
			
				$('#loader-image').hide(); 
				$('#page-content').fadeIn('slow');
			
				$('.start-reset-btn').click(function(){
					resetJobStart(currentJobID, $(this).val());
				});
				$('.ready-reset-btn').click(function(){
					resetJobReady(currentJobID, $(this).val());
				});
				$('.checked-reset-btn').click(function(){
					resetJobChecked(currentJobID, $(this).val());
				});
				$('.complete-reset-btn').click(function(){
					resetJobComplete(currentJobID, $(this).val());
				});

				$('.checkedinstall-reset-btn').click(function(){
					resetJobCheckedInstall(currentJobID, $(this).val());
				});
				$('.completeinstall-reset-btn').click(function(){
					resetJobCompleteInstall(currentJobID, $(this).val());
				});

							
				$('.startdraft-reset-btn').click(function(){
					resetJobStartDraft(currentJobID, $(this).val());
				});
				$('.drawndraft-reset-btn').click(function(){
					resetJobDrawnDraft(currentJobID, $(this).val());
				});
				$('.completedraft-reset-btn').click(function(){
					resetJobCompleteDraft(currentJobID, $(this).val());
				});

				$('.completecnc-reset-btn').click(function(){
					resetJobCompleteCNC(currentJobID, $(this).val());
				});
				$('.completeedging-reset-btn').click(function(){
					resetJobCompleteEdging(currentJobID, $(this).val());
				});
				$('.material-delete-btn').click(function(){
					resetJobMaterial(currentJobID, $(this).val());
				});

				$('.startmaint-reset-btn').click(function(){
					resetJobStartMaint(currentJobID, $(this).val());
				});
				$('.completemaint-reset-btn').click(function(){
					resetJobCompleteMaint(currentJobID, $(this).val());
				});

				$('#return-btn').click(function(){
					$('#page-content').hide();
					showJobs(currentPage);
				});

				if (tab)
					$('#myTab a[href="' + tab + '"]').tab('show');
				
			});
		}

		function addEditJob(action, jobid){

			if (action == "add"){
				$('#add-btn').hide();
				changePageTitle("Add Job");
			}
			else{
				changePageTitle("Edit Job #"+jobid);
				$('#add-btn').show();
			}
			currentJobID = jobid;
					
			$('#searchPanel').hide();
			$('#page-content').hide();
			$('#loader-image').show();
			
			$('#page-content').load('job-add-edit.php', { action: action, jobid: jobid }, function(){ 
				
				$('#loader-image').hide(); 
				$('#page-content').fadeIn('slow');

				$("#job-form").validate();

				$('input[name="inputDateMeasure"]').daterangepicker({format: 'DD-MM-YYYY', singleDatePicker: true,showDropdowns: true});

				$('#delete-btn').click(function(){
					deleteJob($(this).val());
				});
				
				$('#return-btn').click(function(){
					$('#page-content').hide();
					showJobs(currentPage);
				});

			});
		}

		$(document).on('submit', '#job-form', function() {
			$('#page-content').hide();
			$('#loader-image').show();
			 
			$.post("job-crud.php", $(this).serialize())
				.done(function(data) {
					var response = jQuery.parseJSON(data);
					$('#alert').html(response.msg);
					$('#alert').show();
					if (response.action == "add"){
						$('#loader-image').hide();
						$('#page-content').show();
					}
					else
						addEditJob(response.action, response.last_insert_id)
				});
					 
			return false;
		});
		

		$(document).on('click', '.page-link', function(){ 
			event.preventDefault();
			var clickedPage = $(this).attr('href');
			var pageind = clickedPage.indexOf('page=');
			clickedPage = clickedPage.substring((pageind+5));
			
			currentPage = clickedPage;
			showJobs(clickedPage);
		});
		
		
		$(document).on('click', '.edit-btn', function(){ 
			$('#alert').hide();
			addEditJob("edit", $(this).val());
		});
		
		$(document).on('click', '.history-btn', function(){ 
			$('#alert').hide();
			showJobHistory($(this).val());
		});

		function deleteJob(deleteid){
			$.confirm({
				text: "Are you sure you want to delete this job?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'delete', deleteid: deleteid }) 
						.done(function(data){
							showJobs(currentPage);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobStart(jobid, taskid){
			$.confirm({
				text: "This job task will be reset. All information including checks will be removed. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-start', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobReady(jobid, taskid){
			$.confirm({
				text: "This job task will be unmarked as ready to check. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-ready', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobChecked(jobid, taskid){
			$.confirm({
				text: "All checks and missing items will be cleared for this job task. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-checked', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobComplete(jobid, taskid){
			$.confirm({
				text: "This job task will be unmarked as complete. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-complete', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobCheckedInstall(jobid, taskid){
			$.confirm({
				text: "This job task will be reset. All information including checks will be removed. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-checkedinstall', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobCompleteInstall(jobid, taskid){
			$.confirm({
				text: "This job task will be unmarked as complete. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-completeinstall', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
				
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobStartDraft(jobid, taskid){
			$.confirm({
				text: "This job task will be reset. All information including checks will be removed. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-startdraft', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobDrawnDraft(jobid, taskid){
			$.confirm({
				text: "All checks and missing items will be cleared for this job task. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-drawndraft', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobCompleteDraft(jobid, taskid){
			$.confirm({
				text: "This job task will be unmarked as complete. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-completedraft', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobCompleteCNC(jobid, jobmaterialid){
			$.confirm({
				text: "This it will be unmarked as complete. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-completecnc', jobid: jobid, jobmaterialid: jobmaterialid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobCompleteEdging(jobid, jobmaterialid){
			$.confirm({
				text: "This it will be unmarked as complete. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-completeedging', jobid: jobid, jobmaterialid: jobmaterialid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobMaterial(jobid, jobmaterialid){
			$.confirm({
				text: "This material item will be deleted. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'delete-jobmaterial', jobid: jobid, jobmaterialid: jobmaterialid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobStartMaint(jobid, taskid){
			$.confirm({
				text: "This job task will be reset. All information including checks will be removed. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-startmaint', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		}

		function resetJobCompleteMaint(jobid, taskid){
			$.confirm({
				text: "This job task will be unmarked as complete. Would you like to proceed?",
				confirm: function() {
					$('#page-content').hide();
					$('#loader-image').show();
					
					$.post("job-crud.php", { action: 'reset-completemaint', jobid: jobid, taskid: taskid }) 
						.done(function(data){
							var currentTab = $('#myTab .active > a').attr('href');
							showJobHistory(jobid, currentTab);
				
							$('#alert').html(data);
							$('#alert').show();
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