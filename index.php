<!--
\\
\\ CS 246 Student Center
\\ SŽbastien Robaszkiewicz
\\ 2013
\\
-->

<?php

// Gets the SUNetID.
$webAuthUser = $_SERVER['REMOTE_USER'];

// Fetches the Google Spreadsheet as a CSV file.
// To change the Google Spreadsheet, use the key corresponding to your new document.
$key = "0AjrnWBpw0TGqdHo3YjVxMVVFcmZwWFZaSzAxeTl0enc";
$url = "https://docs.google.com/spreadsheet/pub?key=".$key."&single=true&gid=2&output=csv";

// csv_to_array($filename, $delimiter) converts a CSV file to an associative array
//   - Takes the first line of the CSV as the header (key)
//   - Creates a row in the associative array for each new line of the CSV file (value)
//
// @@ For instance, if the CSV file is:
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
    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
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

// Finds the row corresponding to the logged in student and assign it to $student.
// If that SUNetID is not in the Google Spreadsheet, assign $student = NULL.
$student = NULL;
foreach($students as $stud) {
    $sunetid = $stud["sunetid"];
    if ($sunetid == $webAuthUser) {
        $student = $stud;
    }
    if ($stud["stanford_id"] == "average") {
        $averageStats = $stud;
    }
    if ($stud["stanford_id"] == "max") {
        $maxStats = $stud;
    }
    if ($stud["stanford_id"] == "stdev") {
        $stdevStats = $stud;
    }
    if ($stud["stanford_id"] == "median") {
        $medianStats = $stud;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CS246 Student Center</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="../assets/js/html5shiv.js"></script>
        <![endif]-->

        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
      <link rel="shortcut icon" href="ico/favicon.png">
    </head>

    <body>

        <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
            <a class="brand" href="#">CS246 Student Center</a>
        </div>
    </div>
</div>
</div>

<?php if(!isset($student)) { ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="hero-unit">
                <h1>Sorry <?php echo $webAuthUser; ?>, we couldn't find you in our database.</h1>
                <p><a href="mailto:cs246-win1213-staff@lists.stanford.edu?Subject=[CS246 Student Center] Can't find &quot;<?php echo $webAuthUser; ?>&quot; in database">
Please contact us</a> if you think this is a mistake.</p>
            </div>
        </div>
    </div>
</div>

<?php } else { ?>

<div class="container-fluid">

    <div class="row-fluid">
        <div class="span12">
            <div class="hero-unit">
                <h1>Hello, <?php echo $student["first_name"] . " " . $student["last_name"] . "!"; ?></h1>
                <p>Welcome to the student area, you can see all your grades for the Homeworks, Gradiance Quizzes and Final Exam in the tables below.</p>
            </div>
        </div>
    </div>

    <?php if ($student["total_grade"]) {?>
    <!-- Final grade commented out
    <div class="row-fluid">
        <div class="span12">
            <div class="hero-unit">
                <h2>Your final grade for the class is <?php echo $student["total_grade"] ?>/100. You get <?php echo letterGrade($student["total_grade"]) ?>!</h2>
                <p>Here is how we computed the final grade:</p>
                <ul>
                    <li><strong>Homeworks</strong>
                        <ul>
                            <li>HW0 counts for 2% of your final grade</li>
                            <li>HW1, which was out of 95 points, has been scaled to 100 points</li>
                            <li>After this scaling, each homework (1, 2, 3, 4) counts for 9.5% of your final grade</li>
                            <li>So, in total, the homeworks count for <strong>40%</strong> of the final grade</li>
                        </ul>
                    </li>
                    <li><strong>Gradiance quizzes</strong>
                        <ul>
                            <li>All the quizzes have been scaled to 20 points</li>
                            <li>We removed the lowest grade of the 9 Gradiance quizzes (after scaling)</li>
                            <li>We averaged the 8 remaining quizzes (without weight)</li>
                            <li>So, in total, the Gradiance quizzes count for <strong>20%</strong> of your final grade</li>
                        </ul>
                    </li>
                    <li><strong>Final exam</strong>
                        <ul>
                            <li>We scaled it to 40 points</li>
                            <li>So, in total, the Final exam counts for <strong>40%</strong> of your final grade</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    -->
    <?php } ?>

    <div class="row-fluid">
        <div class="span12">
            <?php
            $late_days = $student["late_days"] + $student["hw3_late_days"];
            if ($late_days == 0) {$alertType = "alert-success"; $alertMessage = "Yey!";}
            else if ($late_days == 1) {$alertType = ""; $alertMessage = "Heads-up!";}
            else if ($late_days == 2) {$alertType = $alertType = "alert-error"; $alertMessage = "Warning!";}
            ?>
            <div class="alert <?php echo $alertType; ?>" >
                <strong><?php echo $alertMessage ?></strong> You have used <strong><?php echo $late_days ?></strong> out of your 2 allowed late days.
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="hero-unit">
                <h2>Homeworks</h2>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th></th>
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
                        <?php if ($student["hw0"] != "") { ?>
                        <tr>
                            <td><strong>HW0</strong></td>
                            <td><strong>--</strong></td>
                            <td><strong>--</strong></td>
                            <td><strong>--</strong></td>
                            <td><strong>--</strong></td>
                            <td class="total"><strong><?php echo $student["hw0"]*100; ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat">--</td>
                            <td class="stat">--</td>
                            <td class="stat">--</td>
                            <td class="stat">--</td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["hw1_total"] != "") { ?>
                        <tr>
                            <td><strong>HW1</strong></td>
                            <td><strong><?php echo $student["hw1q1"]; ?></strong>/20</td>
                            <td><strong><?php echo $student["hw1q2"]; ?></strong>/30</td>
                            <td><strong><?php echo $student["hw1q3"]; ?></strong>/15</td>
                            <td><strong><?php echo $student["hw1q4"]; ?></strong>/30</td>
                            <td class="total"><strong><?php echo $student["hw1_total"]; ?></strong>/95</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["hw1_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["hw1_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["hw1_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["hw1_total"],2); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["hw2_total"] != "") { ?>
                        <tr>
                            <td><strong>HW2</strong></td>
                            <td><strong><?php echo $student["hw2q1"]; ?></strong>/25</td>
                            <td><strong><?php echo $student["hw2q2"]; ?></strong>/30</td>
                            <td><strong><?php echo $student["hw2q3"]; ?></strong>/20</td>
                            <td><strong><?php echo $student["hw2q4"]; ?></strong>/25</td>
                            <td class="total"><strong><?php echo $student["hw2_total"]; ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["hw2_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["hw2_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["hw2_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["hw2_total"],2); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["hw3_total"] != "") { ?>
                        <tr>
                            <td><strong>HW3</strong></td>
                            <td><strong><?php echo $student["hw3q1"]; ?></strong>/25</td>
                            <td><strong><?php echo $student["hw3q2"]; ?></strong>/30</td>
                            <td><strong><?php echo $student["hw3q3"]; ?></strong>/20</td>
                            <td><strong><?php echo $student["hw3q4"]; ?></strong>/25</td>
                            <td class="total"><strong><?php echo $student["hw3_total"]; ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["hw3_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["hw3_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["hw3_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["hw3_total"],2); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($student["hw4_total"] != "") { ?>
                        <tr>
                            <td><strong>HW4</strong></td>
                            <td><strong><?php echo $student["hw4q1"]; ?></strong>/35</td>
                            <td><strong><?php echo $student["hw4q2"]; ?></strong>/25</td>
                            <td><strong><?php echo $student["hw4q3"]; ?></strong>/20</td>
                            <td><strong><?php echo $student["hw4q4"]; ?></strong>/20</td>
                            <td class="total"><strong><?php echo $student["hw4_total"]; ?></strong>/100</td>
                            <td class="break"></td>
                            <td class="stat"><?php echo number_format($averageStats["hw4_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($maxStats["hw4_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($stdevStats["hw4_total"],2); ?></td>
                            <td class="stat"><?php echo number_format($medianStats["hw4_total"],2); ?></td>
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

</div>

<?php } ?>

<footer class="footer">
    <p>CS246 Winter 2013</p>
</footer>

</body>
</html>