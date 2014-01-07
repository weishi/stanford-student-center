<!--
\\
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


// letterGrade($n) returns the letter corresponding to the grade $n (out of 100)
// function letterGrade($n) {
//     if ($n > 95) {
//         return "A+";
//     }
//     elseif ($n > 90) {
//         return "A";
//     }
//     elseif ($n > 85) {
//         return "A-";
//     }
//     elseif ($n > 80) {
//         return "B+";
//     }
//     elseif ($n > 75) {
//         return "B";
//     }
//     elseif ($n > 70) {
//         return "B-";
//     }
//     elseif ($n > 65) {
//         return "C+";
//     }
//     elseif ($n > 60) {
//         return "C";
//     }
//     elseif ($n > 55) {
//         return "C-";
//     }
//     elseif ($n > 50) {
//         return "D+";
//     }
// }

// $students is the associative array with all the rows from the CSV file.
$students = csv_to_array($url);
$projects = csv_to_array($project_url);

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

$project = NULL;
foreach($projects as $proj) {
	if ($webAuthUser == $proj["sunetid1"] || $webAuthUser == $proj["sunetid2"] || $webAuthUser == $proj["sunetid3"]) {
		$project = $proj;
	} elseif ($proj["groupno"] == "0_class_avg") {
        $projAverageStats = $proj;
    } elseif  ($proj["groupno"] == "0_class_max") {
        $projMaxStats = $proj;
    } elseif ($proj["groupno"] == "0_class_sd") {
        $projStdevStats = $proj;
    } elseif ($proj["groupno"] == "0_class_median") {
        $projMedianStats = $proj;
    }
}

function lateDisplayHW0($lateVal) {
	if($lateVal === "1") {
		return "<span class=\"label label-success\">Received</span>";
	} else {
		return "<span class=\"label label-info\">Not Received</span>";
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

function coverDisplay($coverVal) {
	if($coverVal === "-2") {
		return "<span class=\"label label-warning\">None!</span>";
	} else {
		return "<span class=\"label label-success\">Present!</span>";
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
    <a class="navbar-brand" href="#" style="padding-left: 0;"><strong><?php echo $className." ".$termName; ?></strong> Student Center</a>
  </div>

  <!--<div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav">
      <li><a href="#">Homework</a></li>
      <li><a href="#">Final</a></li>
    </ul>
  </div>-->
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
Check out your grades, as well as late periods used. If there are any discrepancies between your actual and recorded grades, <a href="mailto:<?php echo $staffEmail; ?>?Subject=[CS244W Student Center] Grade discrepancy for <?php echo $webAuthUser; ?>">contact us</a>!
<?php } ?>
        </div>
    </div>

<?php if ($student["Final Grade"] != "" && $student["still_in_class"] == "1") { ?>

	<h1>Your Final Grade</h1>
	<h1 id="final_grade"><?php echo $student["Final Grade"] ?></h1>
	<?php if ($student["Final Grade"] == "A+" || $student["Final Grade"] == "A") { ?>
	<h1>Congratulations ;)</h1>
	<?php } ?>

<?php } else { ?>

    <div class="row-fluid">
        <div class="span12">
		<h3>Late Periods</h3>
            <?php
            $late_days = $student["late_days"] + $student["hw3_late_days"];
            if ($late_days == 0) {$alertType = "alert-success"; $alertMessage = "Yay!";}
            else if ($late_days == 1) {$alertType = "alert-info"; $alertMessage = "Heads-up!";}
            else if ($late_days == 2) {$alertType = "alert-warning"; $alertMessage = "Warning!";}
            ?>
            <div class="alert <?php echo $alertType; ?>" >
                <strong><?php echo $alertMessage ?></strong> You have used <strong><?php echo $late_days ?></strong> out of your 2 allowed late periods.
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="hero-unit">
                <h3>Homework</h3>
                <table class="table table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Status</th>
                            <th>Cover?</th>
                            <th>Q1</th>
                            <th>Q2</th>
                            <th>Q3</th>
                            <th>Q4</th>
                            <th class="total">Total</th>
                            <th class="break"></th>
                            <th class="stat">Avg.</th>
                            <th class="stat">Max</th>
                            <th class="stat">StDev.</th>
                            <th class="stat">Med.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($student["hw0_total"] != "") { ?>
                        <tr>
                            <td><strong>HW0</strong></td>
                            <td><strong><?php echo lateDisplayHW0($student["hw0_total"]); ?></strong></td>
                            <td><strong>--</strong></td>
                            <td><strong>--</strong></td>
                            <td><strong>--</strong></td>
                            <td><strong>--</strong></td>
                            <td><strong>--</strong></td>
                            <td class="total"><strong><?php echo intval($student["hw0_total"]) * 100; ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["hw0_total"] * 100,0); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["hw0_total"] * 100,0); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["hw0_total"] * 100,0); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["hw0_total"] * 100,0); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["hw1_total"] != "") { ?>
                        <tr>
                            <td><strong>HW1</strong></td>
                            <td><?php echo lateDisplay($student["hw1_latedays"]); ?></td>
                            <td><?php echo coverDisplay($student["hw1_nocover"]); ?></td>
                            <td><strong><?php echo $student["hw1_q1"]; ?></strong>/20</td>
                            <td><strong><?php echo $student["hw1_q2"]; ?></strong>/35</td>
                            <td><strong><?php echo $student["hw1_q3"]; ?></strong>/45</td>
                            <td><strong>--</strong></td>
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
                            <td><strong>HW2</strong></td>
                            <td><?php echo lateDisplay($student["hw2_latedays"]); ?></td>
                            <td><?php echo coverDisplay($student["hw2_nocover"]); ?></td>
                            <td><strong><?php echo $student["hw2_q1"]; ?></strong>/20</td>
                            <td><strong><?php echo $student["hw2_q2"]; ?></strong>/30</td>
                            <td><strong><?php echo $student["hw2_q3"]; ?></strong>/25</td>
                            <td><strong><?php echo $student["hw2_q4"]; ?></strong>/25</td>
                            <td class="total"><strong><?php echo $student["hw2_total"]; ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["hw2_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["hw2_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["hw2_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["hw2_total"],0); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["hw3_total"] != "") { ?>
                        <tr>
                            <td><strong>HW3</strong></td>
                            <td><?php echo lateDisplay($student["hw3_latedays"]); ?></td>
                            <td><?php echo coverDisplay($student["hw3_nocover"]); ?></td>
                            <td><strong><?php echo $student["hw3_q1"]; ?></strong>/25</td>
                            <td><strong><?php echo $student["hw3_q2"]; ?></strong>/25</td>
                            <td><strong><?php echo $student["hw3_q3"]; ?></strong>/25</td>
                            <td><strong><?php echo $student["hw3_q4"]; ?></strong>/25</td>
                            <td class="total"><strong><?php echo $student["hw3_total"]; ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["hw3_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["hw3_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["hw3_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["hw3_total"],0); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["hw4_total"] != "") { ?>
                        <tr>
                            <td><strong>HW4</strong></td>
                            <td><?php echo lateDisplay($student["hw4_latedays"]); ?></td>
                            <td><?php echo coverDisplay($student["hw4_nocover"]); ?></td>
                            <td><strong><?php echo $student["hw4_q1"]; ?></strong>/25</td>
                            <td><strong><?php echo $student["hw4_q2"]; ?></strong>/20</td>
                            <td><strong><?php echo $student["hw4_q3"]; ?></strong>/20</td>
                            <td><strong><?php echo $student["hw4_q4"]; ?></strong>/35</td>
                            <td class="total"><strong><?php echo $student["hw4_total"]; ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["hw4_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["hw4_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["hw4_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["hw4_total"],0); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="row-fluid">
        <div class="span12">
            <div class="hero-unit">
                <h3>Project (Group #<?php echo $project["groupno"]; ?>)</h3>
                <table class="table table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Status</th>
                            <th class="total">Total</th>
                            <th class="break"></th>
                            <th class="stat">Avg.</th>
                            <th class="stat">Max</th>
                            <th class="stat">StDev.</th>
                            <th class="stat">Med.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($project["proposal_total"] != "") { ?>
                        <tr>
                            <td><strong>Proposal</strong></td>
                            <td><?php echo lateDisplay($student["proposal_latedays"]); ?></td>
                            <td class="total"><strong><?php echo (intval($project["proposal_total"]) + intval($student["proposal_adjustment"])); ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($projAverageStats["proposal_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($projMaxStats["proposal_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($projStdevStats["proposal_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($projMedianStats["proposal_total"],0); ?></td>
                        </tr>
                        <?php } ?>
					   <?php if ($project["milestone_total"] != "") { ?>
                        <tr>
                            <td><strong>Milestone</strong></td>
                            <td>--</td>
                            <td class="total"><strong><?php echo (intval($project["milestone_total"]) + intval($student["milestone_adjustment"])); ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($projAverageStats["milestone_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($projMaxStats["milestone_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($projStdevStats["milestone_total"],0); ?></td>
                            <td class="stat"><?php echo number_format($projMedianStats["milestone_total"],0); ?></td>
                        </tr>
                        <?php } ?>
					   <?php if ($project["report_total"] != "") { ?>
                        <tr>
                            <td><strong>Report</strong></td>
                            <td>--</td>
                            <td class="total"><strong><?php echo (intval($project["report_total"]) + intval($student["milestone_adjustment"])); ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format(floatval($projAverageStats["report_total"]),0); ?></td>
                            <td class="stat"><?php echo number_format($projMaxStats["report_total"],0); ?></td>
                            <td class="stat"><?php echo number_format(floatval($projStdevStats["report_total"]),0); ?></td>
                            <td class="stat"><?php echo number_format($projMedianStats["report_total"],0); ?></td>
                        </tr>
                        <?php } ?>
					   <?php if ($project["poster_total"] != "") { ?>
                        <tr>
                            <td><strong>Poster</strong></td>
                            <td>--</td>
                            <td class="total"><strong><?php echo (intval($project["poster_total"]) + intval($student["milestone_adjustment"])); ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format(floatval($projAverageStats["poster_total"]),0); ?></td>
                            <td class="stat"><?php echo number_format($projMaxStats["poster_total"],0); ?></td>
                            <td class="stat"><?php echo number_format(floatval($projStdevStats["poster_total"]),0); ?></td>
                            <td class="stat"><?php echo number_format($projMedianStats["poster_total"],0); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($student["final_grade"] != "" && $student["still_in_class"] == "1") { ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="hero-unit">
                <h3>Final Score</h3>
                <table class="table table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Components</th>
                            <th>Score</th>
                            <th class="break"></th>
                            <th class="stat">Avg.</th>
                            <th class="stat">Max</th>
                            <th class="stat">StDev.</th>
                            <th class="stat">Med.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Homework (1, 2, 3, 4)</td>
                            <td>12% &sdot; <b><?php echo $student['hw1_total'] ?></b> + 12% &sdot; <b><?php echo $student['hw2_total'] ?></b> + 12% &sdot; <b><?php echo $student['hw3_total'] ?></b> + 12% &sdot; <b><?php echo $student['hw4_total'] ?></b></td>
                            <td class="break"></td>
                            <td class="stat">-</td><td class="stat">-</td><td class="stat">-</td><td class="stat">-</td>
                        </tr>

                        <tr>
                            <td>Project (Proposal, Milestone, Report, Poster)</td>
                            <td>10% &sdot; <b><?php echo (intval($project["proposal_total"]) + intval($student["proposal_adjustment"])); ?></b> + 10% &sdot; <b><?php echo (intval($project["milestone_total"]) + intval($student["milestone_adjustment"])); ?></b> + 25% &sdot; <b><?php echo (intval($project["report_total"]) + intval($student["report_adjustment"])); ?></b> + 5% &sdot; <b><?php echo (intval($project["poster_total"]) + intval($student["poster_adjustment"])); ?></b></td>
                            <td class="break"></td>
                            <td class="stat">-</td><td class="stat">-</td><td class="stat">-</td><td class="stat">-</td>
                        </tr>

                        <tr>
                            <td>Others (HW0, Extra Participation)</td>
                            <td>2% &sdot; <b><?php echo $student['hw0_total'] * 100 ?></b> + Bonus 1% &sdot; <b><?php echo intval($student['participation_credit']) ?></b></td>
                            <td class="break"></td>
                            <td class="stat">-</td><td class="stat">-</td><td class="stat">-</td><td class="stat">-</td>
                        </tr>

                        <tr>
                            <td><b>Total</b></td>
                            <td class="total" style="font-size: larger;"><strong><?php echo $student["final_grade"]; ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["final_grade"],0); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["final_grade"],0); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["final_grade"],0); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["final_grade"],0); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
	<?php } ?>

<?php } ?>


<?php if ($hasGradiance) { ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="hero-unit">
                <h2>Gradiance Quizzes</h2>
                <!-- Note: exporting the quizz results from Gradiance turned out to be chaotic since it was reshuffling the results each time a new quiz was released. In the following code, you'll have to reorder the $student["gradiance1"], $student["gradiance2"], etc., accordingly. -->
                <table class="table table-hover table-bordered">
                    <tbody>
                        <?php if ($student["gradiance7"] != "") { ?>
                        <tr>
                            <td><strong>MapReduce</strong></td>
                            <td class="total"><strong><?php echo $student["gradiance7"]; ?></strong>/12</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["gradiance1"] != "") { ?>
                        <tr>
                            <td><strong>Association Rules</strong></td>
                            <td class="total"><strong><?php echo $student["gradiance1"]; ?></strong>/15</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["gradiance8"] != "") { ?>
                        <tr>
                            <td><strong>LSH: Locality Sensitive Hashing</strong></td>
                            <td class="total"><strong><?php echo $student["gradiance8"]; ?></strong>/15</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["gradiance2"] != "") { ?>
                        <tr>
                            <td><strong>Dimensionality Reduction and Clustering</strong></td>
                            <td class="total"><strong><?php echo $student["gradiance2"]; ?></strong>/18</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["gradiance3"] != "") { ?>
                        <tr>
                            <td><strong>Recommendation Systems</strong></td>
                            <td class="total"><strong><?php echo $student["gradiance3"]; ?></strong>/12</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["gradiance4"] != "") { ?>
                        <tr>
                            <td><strong>PageRank</strong></td>
                            <td class="total"><strong><?php echo $student["gradiance4"]; ?></strong>/18</td>
                        </tr>
                        <?php } ?>
                        
                        <?php if ($student["gradiance9"] != "") { ?>
                        <tr>
                            <td><strong>Machine Learning</strong></td>
                            <td class="total"><strong><?php echo $student["gradiance9"]; ?></strong>/15</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["gradiance5"] != "") { ?>
                        <tr>
                            <td><strong>Data Streams</strong></td>
                            <td class="total"><strong><?php echo $student["gradiance5"]; ?></strong>/15</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["gradiance6"] != "") { ?>
                        <tr>
                            <td><strong>Advertising</strong></td>
                            <td class="total"><strong><?php echo $student["gradiance6"]; ?></strong>/12</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } ?>

<?php if ($student["total_grade"]) {?>
    <div class="row-fluid">
        <div class="span12">
            <div class="hero-unit">
                <h2>Final Exam</h2>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="total">Total</th>
                            <th class="break"></th>
                            <th class="stat">Avg.</th>
                            <th class="stat">Max</th>
                            <th class="stat">StDev.</th>
                            <th class="stat">Med.</th>
                        </tr>
                    </thead>
                    <tbody>
<!-- 
                        <?php if ($student["finalq1"] != "") { ?>
                        <tr>
                            <td><strong>1. MapReduce</strong></td>
                            <td class="total"><strong><?php echo $student["finalq1"]; ?></strong>/20</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq2"] != "") { ?>
                        <tr>
                            <td><strong>2. Distance Measures</strong></td>
                            <td class="total"><strong><?php echo $student["finalq2"]; ?></strong>/8</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq3"] != "") { ?>
                        <tr>
                            <td><strong>3. Shingling</strong></td>
                            <td class="total"><strong><?php echo $student["finalq3"]; ?></strong>/15</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq4"] != "") { ?>
                        <tr>
                            <td><strong>4. Minhashing</strong></td>
                            <td class="total"><strong><?php echo $student["finalq4"]; ?></strong>/10</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq5"] != "") { ?>
                        <tr>
                            <td><strong>5. Random Hyperplanes</strong></td>
                            <td class="total"><strong><?php echo $student["finalq5"]; ?></strong>/6</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq6"] != "") { ?>
                        <tr>
                            <td><strong>6. Market Baskets</strong></td>
                            <td class="total"><strong><?php echo $student["finalq6"]; ?></strong>/10</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq7"] != "") { ?>
                        <tr>
                            <td><strong>7. Counting Pairs of Items</strong></td>
                            <td class="total"><strong><?php echo $student["finalq7"]; ?></strong>/10</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq8"] != "") { ?>
                        <tr>
                            <td><strong>8. Clustering</strong></td>
                            <td class="total"><strong><?php echo $student["finalq8"]; ?></strong>/5</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq9"] != "") { ?>
                        <tr>
                            <td><strong>9. Singular Value Decomposition</strong></td>
                            <td class="total"><strong><?php echo $student["finalq9"]; ?></strong>/16</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq10"] != "") { ?>
                        <tr>
                            <td><strong>10. Recommender Systems</strong></td>
                            <td class="total"><strong><?php echo $student["finalq10"]; ?></strong>/15</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq11"] != "") { ?>
                        <tr>
                            <td><strong>11. PageRank</strong></td>
                            <td class="total"><strong><?php echo $student["finalq11"]; ?></strong>/15</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq12"] != "") { ?>
                        <tr>
                            <td><strong>12. Machine Learning</strong></td>
                            <td class="total"><strong><?php echo $student["finalq12"]; ?></strong>/15</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq13"] != "") { ?>
                        <tr>
                            <td><strong>13. AMS 3rd Moment Calculation</strong></td>
                            <td class="total"><strong><?php echo $student["finalq13"]; ?></strong>/10</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq14"] != "") { ?>
                        <tr>
                            <td><strong>14. Streams: DGIM</strong></td>
                            <td class="total"><strong><?php echo $student["finalq14"]; ?></strong>/10</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["finalq15"] != "") { ?>
                        <tr>
                            <td><strong>15. Streams: Finding the Majority Element</strong></td>
                            <td class="total"><strong><?php echo $student["finalq15"]; ?></strong>/15</td>
                        </tr>
                        <?php } ?>
                         -->
                        <?php if ($student["final_total"] != "") { ?>
                        <tr>
                            <td style="font-size: 26px"><strong>Final Exam</strong></td>
                            <td class="total"><strong><?php echo $student["final_total"]; ?></strong>/180</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["final_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["final_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["final_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["final_total"],2); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } ?>

<?php } ?>

<hr />
<footer class="footer">
<?php if (!($student["Final Grade"] != "" && $student["still_in_class"] == "1")) { ?>
	<p><small>Summary statistics are rounded to the nearest integer, and are calculated based on students who have handed in their work. Naturally, they are subject to change. If you didn't submit your assignment with a cover sheet, 2 points will be deducted from the total score. If you exceeded your number of late days, your assignments may be penalized 50%.</small></p>
<?php } ?>
    <p><small><a href="<?php echo $classWebsite; ?>">Back to <?php echo $className." ".$termName; ?></a></small></p>
</footer>

</div>

</body>
</html>