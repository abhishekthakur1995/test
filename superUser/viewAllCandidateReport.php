<?php

session_start();
 
if($_SESSION['user_role'] == 'SUPERADMIN') {
	require('../config.php');

	if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
	    header("location: ../index.php");
	    exit;
	} else {
	    if (time()-$_SESSION['timestamp'] > IDLE_TIME) {
	        header("location: ../logout.php");
	    }   else{
	        $_SESSION['timestamp']=time();
	    }
	}
} else {
	header("location: ../error.php");
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enteries by ULB</title>
    <style type="text/css">
        table tr td:last-child a{
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <?php include '../header.php';?>
    <div class="wrapper">
    	<div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix margin-bottom-4x">
                        <h2 class="pull-left padding-top-4x">Enteries by ULB</h2>
                        <button onclick="Export()" class="btn btn-success pull-right fs4">
                        	<span class="fa fa-download fs4"></span>
                        	Export to CSV File
                        </button>
                    </div>
                    <?php
                    // Attempt select query execution
                    $sql = "SELECT ulbRegion, COUNT(*) AS total,
							COUNT(CASE WHEN gender='m' AND status = 0 THEN 1 END) AS male,
							COUNT(CASE WHEN gender='f' AND status = 0 THEN 1 END) AS female, 
							COUNT(CASE WHEN gender='f' AND status = 0 AND maritialStatus='WIDOW' THEN 1 END ) AS 'Female Widow',
							COUNT(CASE WHEN gender='f' AND status = 0 AND maritialStatus='DIVORCEE' THEN 1 END ) AS 'Female Divorcee',
							COUNT(CASE WHEN gender='f' AND status = 0 AND maritialStatus='MARRIED' THEN 1 END ) AS 'Female Married',
							COUNT(CASE WHEN gender='f' AND status = 0 AND maritialStatus='UNMARRIED' THEN 1 END ) AS 'Female Unmarried'
							FROM candidate_list WHERE status = 0 AND userFormValid = 1 GROUP BY ulbRegion ORDER BY total DESC";


                    if($result = mysqli_query($link, $sql)){
                        $count = mysqli_num_rows($result);
                        if($count > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>#</th>";
                                        echo "<th>ULB</th>";
                                        echo "<th>Male Candidates</th>";
                                        echo "<th>Female Candidates</th>";
                                        echo "<th>Female Widow Candidates</th>";
                                        echo "<th>Female Divorcee Candidates</th>";
                                        echo "<th>Female Married Candidates</th>";
                                        echo "<th>Female Unmarried Candidates</th>";
                                        echo "<th>Total Candidates</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                $sno=0;
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . ++$sno . "</td>";
                                        echo "<td>" . $row['ulbRegion'] . "</td>";
                                        echo "<td>" . $row['male'] . "</td>";
                                        echo "<td>" . $row['female'] . "</td>";
                                        echo "<td>" . $row['Female Widow'] . "</td>";
                                        echo "<td>" . $row['Female Divorcee'] . "</td>";
                                        echo "<td>" . $row['Female Married'] . "</td>";
                                        echo "<td>" . $row['Female Unmarried'] . "</td>";
                                        echo "<td>" . $row['total'] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "Error processing query";
                    }
 
                    // Close connection
                    mysqli_close($link);
                    ?>
                </div>
            </div>       
        </div>
	</div>
</body>
</html>

<script>
    function Export() {
        var conf = confirm("Export users to CSV?");
        if(conf == true) {
            window.open("export.php?view=all", '_blank');
        }
    }
</script>