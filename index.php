<!--
\\
\\ Wei Shi <weishi@cs.stanford.edu>
\\ CS244B Spring 2014
\\ https://github.com/weishi/stanford-student-center
\\ 
\\ Adapted from
\\ Student Center
\\ SŽbastien Robaszkiewicz & Justin Cheng
\\ 2013
\\
-->

<?php

require_once("constants.php");

// Gets the SUNetID.
$webAuthUser = $_SERVER['REMOTE_USER'];

// Log Access in SQLite3 Database
$file_db = new PDO('sqlite:log.sqlite3');
$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$insert = "INSERT INTO accesses (sunetid, time) VALUES (:sunetid, datetime('now'))";
$stmt = $file_db->prepare($insert);
$stmt->bindParam(':sunetid', $sunetid);
$sunetid = $webAuthUser;
$stmt->execute();
$file_db = null;
$error = "";

// Fetches the Google Spreadsheet as a CSV file.
// To change the Google Spreadsheet, use the key corresponding to your new document.
$url = "https://docs.google.com/spreadsheet/pub?key=".$key."&output=csv";
$project_url = "https://docs.google.com/spreadsheet/pub?key=".$project_key."&output=csv";

// csv_to_array($filename, $delimiter) converts a CSV file to an associative array
//   - Takes the first line of the CSV as the header (key)
//   - Creates a row in the associative array for each new line of the CSV file (value)
// Put differently, the keys are the column headers of the Google Spreadsheet.
//
// @@ For instance, if the CSV file gotten from the Google Spreadsheet is:
// @@
// @@ sunetid, hw1, hw2
// @@ jure, 98, 99
// @@ robinio, 95, 100
// @@
// @@ and we call $students = csv_to_array($filename) on it,
// @@ then $student[0]["sunetid"] would be "jure",
// @@ and $student[1]["hw2"] would be "100".
//
function csv_to_array($filename='', $delimiter=',') {
	global $error;
    $header = NULL;
    $data = array();
    if (($handle = @fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    } else {
		$error .= "We're experiencing connectivity issues right now... :(";
	}
    return $data;
}


// $students is the associative array with all the rows from the CSV file.
$students = csv_to_array($url);

// Finds the row corresponding to the logged in student and assign it to $student.
// If that SUNetID is not in the Google Spreadsheet, assign $student = NULL.
$student = NULL;
foreach($students as $stud) {
    $sunetid = $stud["sunetid"];
    if ($sunetid == $webAuthUser) {
        $student = $stud;
    }
    if ($stud["sunetid"] == "0_class_avg") {
        $averageStats = $stud;
    }
    if ($stud["sunetid"] == "0_class_max") {
        $maxStats = $stud;
    }
    if ($stud["sunetid"] == "0_class_sd") {
        $stdevStats = $stud;
    }
    if ($stud["sunetid"] == "0_class_median") {
        $medianStats = $stud;
    }
}

function lateDisplay($lateVal) {
	if($lateVal === "0") {
		return "<span class=\"label label-success\">Received</span>";
	} elseif ($lateVal === "") {
		return "<span class=\"label label-info\">Not Received</span>";
	} else {
		return "<span class=\"label label-warning\">Turned in Late</span>";
	}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $className." ".$termName; ?> Student Center</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <![endif]-->
    </head>

    <body>

<div style="background-color: #ddd; border-bottom: 1px solid #444; margin-bottom: 15px;">
<div class="container">
<nav class="navbar navbar-default" role="navigation" style="background: 0; border: 0; margin-bottom: 0;">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="#" style="padding-left: 0;">
        <strong><?php echo $className." ".$termName; ?></strong> Student Center
    </a>
  </div>

</nav>
</div>
</div>

<div class="container">

<?php if (strlen($error) > 0) { ?>
<div class="alert alert-danger"><strong><?php echo $error; ?></strong></div>
<?php } ?>

<?php if(!isset($student)) { ?>

    <div class="row-fluid">
        <div class="span12">
            <div class="hero-unit">
                <h1>Sorry <?php echo $webAuthUser; ?>, we couldn't find you in our database.</h1>
                <h2><a href="mailto:<?php echo $staffEmail; ?>?Subject=[<?php echo $className; ?> Student Center] Can't find &quot;<?php echo $webAuthUser; ?>&quot; in database">
Please contact us</a> if you think this is a mistake.</h2>
            </div>
        </div>
    </div>

<?php } else { ?>

    <div class="row-fluid">
        <div class="span12">
			<h2>Hi, <strong><?php echo $student["first_name"] . " " . $student["last_name"]; ?></strong>!</h2>
<?php if (!($student["Final Grade"] != "" && $student["still_in_class"] == "1")) { ?>
Check out your grades, as well as late periods used. If there are any discrepancies between your actual and recorded grades, 
    <a href="mailto:<?php echo $staffEmail; ?>?Subject=[<?php echo $termName; ?>]Grade discrepancy for <?php echo $webAuthUser; ?>">contact us</a>!
<?php } ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
		<h3>Late days</h3>
            <?php
            $late_days = $student["late_days"] + $student["hw3_late_days"];
            if ($late_days == 0) {$alertType = "alert-success"; $alertMessage = "Yay!";}
            else if ($late_days == 1) {$alertType = "alert-info"; $alertMessage = "Heads-up!";}
            else if ($late_days == 2) {$alertType = "alert-warning"; $alertMessage = "Warning!";}
            ?>
            <div class="alert <?php echo $alertType; ?>" >
                <strong><?php echo $alertMessage ?></strong> You have used <strong><?php echo $late_days ?></strong> out of your 3 allowed late periods.
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="hero-unit">
                <h3>Assignment</h3>
                <table class="table table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Design</th>
                            <th>Implementation</th>
                            <th>Report</th>
                            <th>Late day (Milestone)</th>
                            <th>Late day (Final submission)</th>
                            <th class="total">Total</th>
                            <th class="break"></th>
                            <th class="stat">Avg.</th>
                            <th class="stat">Max</th>
                            <th class="stat">StDev.</th>
                            <th class="stat">Med.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($student["hw1_total"] != "") { ?>
                        <tr>
                            <td><strong>Mazewar</strong></td>
                            <td><strong><?php echo $student["hw1_design"]; ?></strong>/40</td>
                            <td><strong><?php echo $student["hw1_impl"]; ?></strong>/40</td>
                            <td><strong><?php echo $student["hw1_report"]; ?></strong>/20</td>
                            <td><?php echo $student["hw1_lateday_milestone"]; ?></td>
                            <td><?php echo $student["hw1_lateday_final"]; ?></td>
                            <td class="total"><strong><?php echo $student["hw1_total"]; ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["hw1_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["hw1_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["hw1_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["hw1_total"],0); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["hw2_total"] != "") { ?>
                        <tr>
                            <td><strong>ReplFS</strong></td>
                            <td><strong><?php echo $student["hw2_design"]; ?></strong>/15</td>
                            <td><strong><?php echo $student["hw2_impl"]; ?></strong>/70</td>
                            <td><strong><?php echo $student["hw2_report"]; ?></strong>/15</td>
                            <td><?php echo $student["hw2_lateday_milestone"]; ?></td>
                            <td><?php echo $student["hw2_lateday_final"]; ?></td>
                            <td class="total"><strong><?php echo $student["hw2_total"]; ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["hw2_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["hw2_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["hw2_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["hw2_total"],0); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } ?>

<hr />
<footer class="footer">
	<p><small>
	Summary statistics are rounded to the nearest integer, and are calculated based on students who have handed in their work. 
	Naturally, they are subject to change. If you exceeded your number of late days, your assignments may be penalized 10% per day.</small></p>
    <p><small><a href="<?php echo $classWebsite; ?>">Back to <?php echo $className." ".$termName; ?></a></small></p>
</footer>

</div>

</body>
</html>
