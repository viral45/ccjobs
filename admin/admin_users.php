<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
	die();
}

include("config.php"); 

$page = "adminusers";

include("header.php"); 

?>

<div class="page-header">
    <h1>
      <span id="pageTitle">Users</span>
      <button type="button" id="add-btn" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New</button>
    </h1>
</div>

<div id="alert"></div>

<div id='page-content'></div>
<div id='loader-image'><img src='../img/ajax-loader.gif' /> &nbsp;LOADING</div>

<br />

<script type='text/javascript'>

	function changePageTitle(title){
		$('#pageTitle').html(title);
	}
	
	$(document).ready(function(){
		 
		showUsers(1);
		var currentPage = 1;
		
		$('#add-btn').click(function(){
			addEditUser("add", 0);
			$('#alert').hide();
		});
		
		
		function showUsers(page){
						
			$('#loader-image').show();
			changePageTitle("Admin Users");
			$('#add-btn').show();
			$('#searchPanel').show();
			$('#page-content').hide();
			$('#alert').hide();
			
			$('#page-content').load('admin_user-list.php', $('#searchForm').serialize() + "&page=" + page, function(){ 
				$('#loader-image').hide(); 
				$('#page-content').fadeIn('slow');
				$('[data-toggle="tooltip"]').tooltip();
			});
		}
		
		function addEditUser(action, userid){
			if (action == "add"){
				$('#add-btn').hide();
				changePageTitle("Add Admin User");
			}
			else{
				changePageTitle("Edit Admin User");
				$('#add-btn').show();
			}
						
			$('#searchPanel').hide();
			$('#page-content').hide();
			
			$('#loader-image').show();
			$('#page-content').load('admin_user-add-edit.php', { action: action, userid: userid }, function(){ 
				
				$('#loader-image').hide(); 
				$('#page-content').fadeIn('slow');
				$("#user-form").validate();
				
				$('#return-btn').click(function(){
					$('#page-content').hide();
					showUsers(currentPage);
				});
			});
		}
		
		$(document).on('click', '.page-link', function(){ 
			event.preventDefault();
			var clickedPage = $(this).attr('href');
			var pageind = clickedPage.indexOf('page=');
			clickedPage = clickedPage.substring((pageind+5));
			
			currentPage = clickedPage;
			showUsers(clickedPage);
		});

		$(document).on('submit', '#user-form', function() {
			$('#page-content').hide();
			$('#loader-image').show();
			 
			$.post("admin_user-crud.php", $(this).serialize())
				.done(function(data) {
					var response = jQuery.parseJSON(data);
					$('#alert').html(response.msg);
					$('#alert').show();
					if (response.action == "add"){
						$('#loader-image').hide();
						$('#page-content').show();
					}
					else
						addEditUser(response.action, response.last_insert_id)
				});
					 
			return false;
		});
				
		$(document).on('click', '.edit-btn', function(){ 
			addEditUser("edit", $(this).val());
		});
		
		$(document).on('click', '.delete-btn', function(){ 
			var deleteid = $(this).val();
			$.confirm({
				text: "Are you sure you want to delete this admin user?",
				confirm: function() {
					$.post("admin_user-crud.php", { action: 'delete', deleteid: deleteid }) 
						.done(function(data){
							showUsers(currentPage);
							$('#alert').html(data);
							$('#alert').show();
					});
				},
				cancel: function() {
					// nothing to do
				}
			});
		});
		
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
    
	});
	
</script>

<?php include("footer.php"); ?>