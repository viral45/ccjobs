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
      <span id="pageTitle">Reports</span>
    </h1>
</div>

<div id="searchPanel" class="panel panel-default">
    <div class="panel-heading">
    	<h3 class="panel-title">Search</h3>
    </div>
    <div class="panel-body">
        <form id="searchForm" class="form-horizontal">
			
            <div class="form-group">
            	<div class="col-sm-12">
                    <label for="searchReportType">Report Type</label>
                    <select id="searchReportType" name="searchReportType" class="form-control">
                        <option value="joblist">Job List</option>
						<option value="jobstarted">Jobs (started)</option>
						<option value="jobcheck">Jobs (check required)</option>
						<option value="jobmissing">Jobs (missing items)</option>
						<option value="jobcompleted">Jobs (completed)</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group" id="searchfilters">
				<div class="col-sm-6">
                    <label for="searchDate">Date Range</label>
                    <input type="text" class="form-control" id="searchDate" name="searchDate" placeholder="Date Range" autocomplete="off">
                </div>
				<div class="col-sm-6">
					<label for="searchStartedBy">Started By</label>
                    <select id="searchStartedBy" name="searchStartedBy" class="form-control">
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
			$('#searchDate').val('');
			$('#searchStartedBy').val('');

			if ($(this).val() == "jobcheck" || $(this).val() == "jobmissing" || $(this).val() == "jobstarted"){
				$("#searchfilters").hide();
			}
			else	
				$("#searchfilters").show();
		});
						
		function showReport(){
			$('#loader-image').show();

			$('#searchPanel').show();
			$('#page-content').hide();
			
			var reportpage = "report-" + $("#searchReportType").val() + ".php";
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