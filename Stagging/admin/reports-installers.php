<?php 
include("functions.php");

sec_session_start();
if(!isset($_SESSION['logged_in'])){
	header("location:login.php");
	die();
}

include("config.php"); 

$page = "reports";

include("header.php"); 

?>

<div class="page-header">
    <h1>
      <span id="pageTitle">Reports (Installers)</span>

		<div class="dropdown pull-right">
			<button class="btn btn-default dropdown-toggle" type="button" id="reportmenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				More Reports
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" aria-labelledby="reportmenu">
				<li><a href="reports-draftsmen.php">Draftsmen</a></li>
				<li><a href="reports-cnc.php">CNC</a></li>
				<li><a href="reports-edging.php">Edging</a></li>
				<li><a href="reports-assemblers.php">Assemblers</a></li>
				<li><a href="reports-installers.php">Installers</a></li>
			</ul>
		</div>
    </h1>
</div>

<div id="searchPanel" class="panel panel-default">
    <div class="panel-heading">
    	<h3 class="panel-title">Search</h3>
    </div>
    <div class="panel-body">
        <form id="searchForm" class="form-horizontal">
			
            <div class="form-group">
            	<div class="col-sm-9">
                    <label for="searchReportType">Report Type</label>
                    <select id="searchReportType" name="searchReportType" class="form-control">
						<option value="list">Job List</option>
						<option value="missing">Jobs (missing items)</option>
						<option value="completed">Jobs (completed)</option>
                    </select>
                </div>
				<div class="col-sm-3">
					<label for="sortorder">Sort</label>
					<select id="sortorder" name="sortorder" class="form-control">
						<option value="tblJob.JobID">Job No.</option>
						<option value="tblJobTaskInstall.DateChecked">Date Checked (Oldest to Newest)</option>
						<option value="tblJobTaskInstall.DateChecked DESC">Date Checked (Newest to Oldest)</option>
						<option value="tblUser.FullName, tblJobTaskInstall.DateChecked">Installer</option>
                    </select>
                </div>				
            </div>
            
            <div class="form-group" id="searchfilters">
				<div class="col-sm-4">
                    <label for="searchKeyword">Job No. / Address</label>
                    <input type="text" class="form-control" id="searchKeyword" name="searchKeyword" placeholder="Job No. / Address" autocomplete="off">
                </div>			
				<div class="col-sm-4">
                    <label for="searchDate">Date Range</label>
                    <input type="text" class="form-control" id="searchDate" name="searchDate" placeholder="Date Range" autocomplete="off">
                </div>
				<div class="col-sm-4">
					<label for="searchCheckedBy">Checked By</label>
                    <select id="searchCheckedBy" name="searchCheckedBy" class="form-control">
                        <option value="">All</option>
                        <?php
                            $query = "SELECT UserID, FullName FROM tblUser ORDER BY FullName";
                            $result = $mysqli->query($query);
                            
                            while($row = $result->fetch_array())
                            {	
                                echo "<option value=" . $row['UserID'] . ">" . $row['FullName'] . "</option>";
                            }
                        ?>
                    </select>
				</div>
			</div>
                      
            <button type="submit" id="search-btn" class="btn btn-info">Generate</button>
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
	
	$(document).ready(function(){
		
		$('#loader-image').hide();
		
		$('#reset-btn').click(function(){
			$('#searchForm')[0].reset();
			$('#page-content').hide();
			$("#searchfilters").show();
		});
			
		$('#search-btn').click(function(){
			showReport();
		});
				
		$('input[name="searchDate"]').daterangepicker(
		{
			format: 'DD-MM-YYYY',
			ranges: {
			   'Today': [moment(), moment()],
			   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			   'This Month': [moment().startOf('month'), moment().endOf('month')],
			   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			}
		});

		$('#searchReportType').change(function(){
			$('#page-content').hide();
			$('#searchKeyword').val('');
			$('#searchDate').val('');
			$('#searchCheckedBy').val('');

			// if ($(this).val() == "missing"){
			// 	$("#searchfilters").hide();
			// }
			// else	
			// 	$("#searchfilters").show();
		});
						
		function showReport(){
			$('#loader-image').show();

			$('#searchPanel').show();
			$('#page-content').hide();
			
			var reportpage = "reports/installers-" + $("#searchReportType").val() + ".php";
			$('#page-content').load(reportpage, $('#searchForm').serialize(), function(){ 
				$('#loader-image').hide(); 
				$('#page-content').fadeIn('slow');
				
				$('#print-btn').click(function(){
					$("#print-content").print({
						globalStyles: true,
						mediaPrint: true,
						stylesheet: null,
						noPrintSelector: ".no-print",
					});
				});
			});
		}
		
	});
	
</script>

<?php include("footer.php"); ?>