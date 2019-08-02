<?php
/**
 * Calculate the Car Upgrades using the tCarUpgrades table from a given PR
 *
 * PHP Version: 7.2.9 (Version 5.3+ probably)
 *
 * @category PHP
 * @package  Wiki
 * @author   Michael Pritchard <twitter: @MikeAPritch>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     CarUpgrades.php
 * File:    CarUpgrades.php
 * Date:    2018-10-18
 *
 * Requires:
 * MySQL table `tupgrades` containing all the upgrades for Wiki cars
 *
 * This script SELECTs the Car Upgrades from carupgrades table using MySQL PDO
 * Calculates all upgrade trees with tree structure, PR, R$, GC and GC only upgrades
 * Outputs the calculated upgrade trees to a table
 *
 * v1.7 Added tCarDashboard
 * v2.0 Re-created with the `tupgrades` table
 * v2.1 Added $_GET for carID from CarDashboardv1.1.php
 * v2.2 Created one big statement and created once,
 *      statement was too large for some upgrade trees
 * v2.3 Prepare and execute without bind, still takes a long time.
 *      (Reverted to v2.2 for v2.4)
 * v2.4 Updated v2.2 with INSERT every 1000 statements (quick and reliable!)
 * v3.0 Test creating the upgrade data on the fly (no tUpgradeTree)
 * v3.1 Port to an online version with different.
 * v3.2 Convert to integers for upgrades and different way to sort.
 * ==============================================================================
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Real Racing 3 Car Upgrade Table</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet"
  href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js">
  </script>
  <script
  src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js">
  </script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js">
  </script>
</head>
<body>

    <div class="container">

<?php
//place this before any script you want to calculate time
// runtime function
// require 'includes/fu_runtime.php';

 // Check if a message has been posted.
if (isset($_GET['message'])) {
    $message = Strip_Bad_chars($_GET['message']);
    ?>
    <div class="alert alert-info alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Displaying: </strong> <?php echo $message; ?>
    </div>
    <?php
}
$errorCheck = false;
// Check if the carID has been passed to the script.
if (isset($_POST['carID'])) {
    $carID = Strip_Bad_chars($_POST['carID']);
    //echo "<h1><b>$ID</b></h1>";
} else {
    // If not sent display to go back to dashboard and stop the script
    ?>
      <div class="alert alert-danger">
        <strong>No car specified!</strong> return to car
        <a href="index.php" class="alert-link">dashboard</a>.
      </div>
    <?php
    $errorCheck = true;
}


// Check if the car has been passed to the script.
if (isset($_POST['car'])) {
    $car = Strip_Bad_chars($_POST['car']);
    // echo "<h2><b>$car</b></h2>";
} else {
    // If not sent display to go back to dashboard and stop the script
    ?>
      <div class="alert alert-danger">
        <strong>No car specified!</strong> return to car
        <a href="index.php" class="alert-link">dashboard</a>.
      </div>
    <?php
    $errorCheck = true;
}


if (isset($_POST['TreeFU'])) {
    $fuTree = Strip_Bad_chars($_POST['TreeFU']);
    // echo "<p>Car FU Tree: $fuTree</p>";
} else {
    // If not sent display to go back to dashboard and stop the script
    ?>
      <div class="alert alert-danger">
        <strong>No fully upgraded car tree specified!</strong> return to car
        <a href="index.php" class="alert-link">dashboard</a>.
      </div>
    <?php
    $errorCheck = true;
}

if (isset($_POST['basePR'])) {
    $basePR = (int)(Strip_Bad_chars($_POST['basePR']) * 10);
    // echo "<p>basePR: $basePR</p>";
} else {
    // If not sent display to go back to dashboard and stop the script
    ?>
      <div class="alert alert-danger">
        <strong>Fully upgraded car tree specified!</strong> return to car
        <a href="index.php" class="alert-link">dashboard</a>.
      </div>
    <?php
    $errorCheck = true;
}
// echo "BasePR: $basePR<br/><br/>";

/*
if (isset($_POST['rdPR'])) {
    $rdPR = (int)(Strip_Bad_chars($_POST['rdPR']) * 10);
    // echo "<p>rdPR: $rdPR</p>";
} else {
    // If not sent display to go back to dashboard and stop the script
    ?>
      <div class="alert alert-danger">
        <strong>Fully upgraded car tree specified!</strong> return to car
        <a href="index.php" class="alert-link">dashboard</a>.
      </div>
    <?php
    $errorCheck = true;
}


if (isset($_POST['fuPR'])) {
    $fuPR = (int)(Strip_Bad_chars($_POST['fuPR']) * 10);
    // echo "<p>max PR: $fuPR</p>";
} else {
    // If not sent display to go back to dashboard and stop the script
    ?>
      <div class="alert alert-danger">
        <strong>No Fully upgraded PR specified!</strong> return to car
        <a href="index.php" class="alert-link">dashboard</a>.
      </div>
    <?php
    $errorCheck = true;
}
*/

if (isset($_POST['TreeRD'])) {
    $rdTree = Strip_Bad_chars($_POST['TreeRD']);
    // echo "<p>Max with R$ only Tree: $rdTree</p>";
} else {
    // If not sent display to go back to dashboard and stop the script
    ?>
      <div class="alert alert-danger">
        <strong>No Tree for R$ specified!</strong> return to car
        <a href="index.php" class="alert-link">dashboard</a>.
      </div>
    <?php
    $errorCheck = true;
}


if (isset($_POST['searchPR'])) {
    // Search from -0.1 of the selected search value
    $searchPR = (int)((Strip_Bad_chars($_POST['searchPR'])-0.1) * 10);
    // echo "<p>Search for PR: $searchPR</p>";
} else {
    // If not sent display to go back to dashboard and stop the script
    ?>
      <div class="alert alert-danger">
        <strong>No search PR specified!</strong> return to car
        <a href="index.php" class="alert-link">dashboard</a>.
      </div>
    <?php
    $errorCheck = true;
}

// If there has been an error display the button to home
if ($errorCheck) {
    //Button link to return to the dashboard
    echo '<a href="index.php" class="btn btn-danger" role="button">
    Return to car table</a>';

    exit();
}

// MySQL connection variables (PDO used)
// ($host, $dbname, $user, $charset, $password, $options)
require '../htconfig/dbConfigwiki.php';

//try  -- used for testing only.
{ // PDO Connection string
$pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=$charset",
    $user, $password, $options
);
}
/*  -- used for testing only.
catch(PDOException $e) {
    echo "Error unable to connect: <br/>" . $e->getMessage();
    echo "<br/>";
}
*/

// Create Select statement for car upgrades
$carupgrades_SQLselect = "SELECT ";
$carupgrades_SQLselect .= "Car, Seq, Upgrade, PR, RD, GC ";
$carupgrades_SQLselect .= "FROM ";
$carupgrades_SQLselect .= "`tUpgrades` ";
$carupgrades_SQLselect .= "WHERE ";
$carupgrades_SQLselect .= "Car = '".$car."' ";

// Initialise the $rows array.
$rows = array();

// Run the select statement and store the resulting array in $rows
//try
{   // PDO Connection string
    $stmt = $pdo->prepare($carupgrades_SQLselect);
    $stmt->execute();
    //$stmt->setFetchMode(PDO::FETCH_ASSOC);
    $rows = $stmt->fetchAll(); //Fetch all upgrades for the car
}
/*
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
*/
// Close MySQL PDO connection
$pdo = null;

// Check there are upgrades for the car
if (count($rows) == 0) {
    ?>
      <div class="alert alert-danger">
        <strong>No upgrades found for the car!</strong> return to car
        <a href="index.php" class="alert-link">dashboard</a>.
      </div>
      <a href="index.php" class="btn btn-danger" role="button">
    Return to car table</a>
    <?php
    exit();
}

/*
List of the upgrade items:
    1. Engine
    2. Drivetrain
    3. Body
    4. Suspension
    5. Exhaust
    6. Brakes
    7. Tires & Wheels
*/

// Configure no upgrades array [0] = no upgrades
// Useful when the car has no upgrade tree (e.g. no Exhaust on Electric cars)
$engine[0] = array('UpgName'=>'Engine',
    'UpgDesc'=>'None');
$drivetrain[0] = array('UpgName'=>'Drivetrain',
    'UpgDesc'=>'None');
$body[0] = array('UpgName'=>'Body',
    'UpgDesc'=>'None');
$suspension[0] = array('UpgName'=>'Suspension',
    'UpgDesc'=>'None');
$exhaust[0] = array('UpgName'=>'Exhaust',
    'UpgDesc'=>'None');
$brakes[0] = array('UpgName'=>'Brakes',
    'UpgDesc'=>'None');
$tiresandWheels[0] = array('UpgName'=>'Tires & Wheels',
    'UpgDesc'=>'None');

/*
* Configure cumulative array stating with no upgrades (all 0)
* for each upgrade item, ready to split the raw upgrade data
* into different arrays.
* Again 0 is useful for item trees with no upgrades.
*/
$cumEn[0] = array(
    'PR'=>0,
    'RD'=>0,
    'GC'=>0,
    'GC only'=>0
    );
$cumDr[0] = array(
    'PR'=>0,
    'RD'=>0,
    'GC'=>0,
    'GC only'=>0
    );
$cumBo[0] = array(
    'PR'=>0,
    'RD'=>0,
    'GC'=>0,
    'GC only'=>0
    );
$cumSu[0] = array(
    'PR'=>0,
    'RD'=>0,
    'GC'=>0,
    'GC only'=>0
    );
$cumEx[0] = array(
    'PR'=>0,
    'RD'=>0,
    'GC'=>0,
    'GC only'=>0
    );
$cumBr[0] = array(
    'PR'=>0,
    'RD'=>0,
    'GC'=>0,
    'GC only'=>0
    );
$cumTW[0] = array(
    'PR'=>0,
    'RD'=>0,
    'GC'=>0,
    'GC only'=>0
    );

// Set start sequence for variables. Start at 1 as 0 is no upgrades.
$enSeq = 1;
$drSeq = 1;
$boSeq = 1;
$suSeq = 1;
$exSeq = 1;
$brSeq = 1;
$twSeq = 1;

//Variable to search for the biggest PR increment
$biggestPR = 0;

// Run though the SQL upgrade data, create new array for each tree item
foreach ($rows as $row) {
    // Find the biggest PR increment
    // echo "<br/>PR this upgrade: ". $row['PR'];
    if (($row['PR']*10) > $biggestPR) {
        $biggestPR = ($row['PR'] * 10);

    }
    switch ($row['Upgrade']) {
    case 'Engine':
        // For each case (1 = Engine) add the SQL $row data to the item array
        $engine[$enSeq] = $row;
        // If the R$ upgrade is 0, update GC only upgrade
        if ($engine[$enSeq]['RD'] == 0) {
            $gcOnly = $engine[$enSeq]['GC'];
        } else {
            $gcOnly = 0;
        }
        // Create an accumulative array of costs
        $cumEn[$enSeq] = array(
            'PR'=>      (int)(($engine[$enSeq]['PR'] * 10) + $cumEn[$enSeq-1]['PR']), // Convert PR to integer
            'RD'=>      ($engine[$enSeq]['RD'] + $cumEn[$enSeq-1]['RD']),
            'GC'=>      ($engine[$enSeq]['GC'] + $cumEn[$enSeq-1]['GC']),
            'GC only'=> ($gcOnly + $cumEn[$enSeq-1]['GC only'])
        );
        // Increase the sequence for each item
        $enSeq ++;
        break;
    case 'Drivetrain': // 2 = Drivetrain
        $drivetrain[$drSeq] = $row;
        if ($drivetrain[$drSeq]['RD'] == 0) {
            $gcOnly = $drivetrain[$drSeq]['GC'];
        } else {
            $gcOnly = 0;
        }
        $cumDr[$drSeq] = array(
            'PR'=>      (int)(($drivetrain[$drSeq]['PR'] * 10) + $cumDr[$drSeq-1]['PR']), // Convert PR to integer
            'RD'=>      ($drivetrain[$drSeq]['RD'] + $cumDr[$drSeq-1]['RD']),
            'GC'=>      ($drivetrain[$drSeq]['GC'] + $cumDr[$drSeq-1]['GC']),
            'GC only'=> ($gcOnly + $cumDr[$drSeq-1]['GC only'])
        );
        $drSeq ++;
        break;
    case 'Body':  // 3 = Body
        $body[$boSeq] = $row;
        if ($body[$boSeq]['RD'] == 0) {
            $gcOnly = $body[$boSeq]['GC'];
        } else {
            $gcOnly = 0;
        }
        $cumBo[$boSeq] = array(
            'PR'=>      (int)(($body[$boSeq]['PR'] * 10) + $cumBo[$boSeq-1]['PR']), // Convert PR to integer
            'RD'=>      ($body[$boSeq]['RD'] + $cumBo[$boSeq-1]['RD']),
            'GC'=>      ($body[$boSeq]['GC'] + $cumBo[$boSeq-1]['GC']),
            'GC only'=> ($gcOnly + $cumBo[$boSeq-1]['GC only'])
        );
        $boSeq ++;
        break;
    case 'Suspension':  // 4 = Suspension
        $suspension[$suSeq] = $row;
        if ($suspension[$suSeq]['RD'] == 0) {
            $gcOnly = $suspension[$suSeq]['GC'];
        } else {
            $gcOnly = 0;
        }
        $cumSu[$suSeq] = array(
            'PR'=>      (int)(($suspension[$suSeq]['PR'] * 10) + $cumSu[$suSeq-1]['PR']), // Convert PR to integer
            'RD'=>      ($suspension[$suSeq]['RD'] + $cumSu[$suSeq-1]['RD']),
            'GC'=>      ($suspension[$suSeq]['GC'] + $cumSu[$suSeq-1]['GC']),
            'GC only'=> ($gcOnly + $cumSu[$suSeq-1]['GC only'])
        );
        $suSeq ++;
        break;
    case 'Exhaust':  // 5 = Exhaust
        $exhaust[$exSeq] = $row;
        if ($exhaust[$exSeq]['RD'] == 0) {
            $gcOnly = $exhaust[$exSeq]['GC'];
        } else {
            $gcOnly = 0;
        }
        $cumEx[$exSeq] = array(
            'PR'=>      (int)(($exhaust[$exSeq]['PR'] * 10) + $cumEx[$exSeq-1]['PR']), // Convert PR to integer
            'RD'=>      ($exhaust[$exSeq]['RD'] + $cumEx[$exSeq-1]['RD']),
            'GC'=>      ($exhaust[$exSeq]['GC'] + $cumEx[$exSeq-1]['GC']),
            'GC only'=> ($gcOnly + $cumEx[$exSeq-1]['GC only'])
        );
        $exSeq ++;
        break;
    case 'Brakes':  // 6 = Brakes
        $brakes[$brSeq] = $row;
        if ($brakes[$brSeq]['RD'] == 0) {
            $gcOnly = $brakes[$brSeq]['GC'];
        } else {
            $gcOnly = 0;
        }
        $cumBr[$brSeq] = array(
            'PR'=>      (int)(($brakes[$brSeq]['PR'] * 10) + $cumBr[$brSeq-1]['PR']), // Convert PR to integer
            'RD'=>      ($brakes[$brSeq]['RD'] + $cumBr[$brSeq-1]['RD']),
            'GC'=>      ($brakes[$brSeq]['GC'] + $cumBr[$brSeq-1]['GC']),
            'GC only'=> ($gcOnly + $cumBr[$brSeq-1]['GC only'])
        );
        $brSeq ++;
        break;
    case 'Tires & Wheels':  // 7 = Tires & Wheels
        $tiresandWheels[$twSeq] = $row;
        if ($tiresandWheels[$twSeq]['RD'] == 0) {
            $gcOnly = $tiresandWheels[$twSeq]['GC'];
        } else {
            $gcOnly = 0;
        }
        $cumTW[$twSeq] = array(
            'PR'=>      (int)(($tiresandWheels[$twSeq]['PR'] * 10) + $cumTW[$twSeq-1]['PR']), // Convert PR to integer
            'RD'=>      ($tiresandWheels[$twSeq]['RD'] + $cumTW[$twSeq-1]['RD']),
            'GC'=>      ($tiresandWheels[$twSeq]['GC'] + $cumTW[$twSeq-1]['GC']),
            'GC only'=> ($gcOnly + $cumTW[$twSeq-1]['GC only'])
        );
        $twSeq ++;
        break;
    }
}
/*
print_r($cumEn);
echo "<br/>";
print_r($cumDr);
echo "<br/>";
print_r($cumBo);
echo "<br/>";
print_r($cumSu);
echo "<br/>";
print_r($cumEx);
echo "<br/>";
print_r($cumBr);
echo "<br/>";

print_r($cumTW);
echo "<br/>";
*/
// Calculate the maximum PR based on the search PR + the max. increment +1 to allow for R$ upgrades
$searchMax = $searchPR+$biggestPR +1;
// echo "Search searchMax = $searchMax<br/>SearchPR = $searchPR<br/>biggestPR = $biggestPR<br/>";
// Roll back the sequence by one
// Any tree with no upgrades will be 0
$enSeq --;
$drSeq --;
$boSeq --;
$suSeq --;
$exSeq --;
$brSeq --;
$twSeq --;

// maxupgrade should be the same as $fuTree
$maxUpgrade = $enSeq . $drSeq .$boSeq . $suSeq . $exSeq . $brSeq . $twSeq ;

// Initialise the upgrades array, incase there are no upgrades.
$upgrades = array();
// loop though each upgrade item to calculate the tree
$seq = 0;
$enSeq = 0;
foreach ($engine as $eng) {
    $drSeq = 0;
    foreach ($drivetrain as $drive) {
        $boSeq = 0;
        foreach ($body as $bod) {
            $suSeq = 0;
            foreach ($suspension as $sus) {
                $exSeq = 0;
                foreach ($exhaust as $exh) {
                    $brSeq = 0;
                    foreach ($brakes as $bra) {
                        $twSeq = 0;
                        foreach ($tiresandWheels as $taw) {
                            // Create an upgrades array with:
                            // Tree, PR, RD, GC and GC only
                            // Add all the items together to get the values

                            $pr = $cumEn[$enSeq]['PR'] +
                            $cumDr[$drSeq]['PR'] +
                            $cumBo[$boSeq]['PR'] +
                            $cumSu[$suSeq]['PR'] +
                            $cumEx[$exSeq]['PR'] +
                            $cumBr[$brSeq]['PR'] +
                            $cumTW[$twSeq]['PR'] +
                            $basePR;

                            // Add extra test for PR Tree between min & max PR
                            // if....
                            if (($pr >= $searchPR)
                                && ($pr <= $searchMax)
                            ) {
                                //if (($pr >= ($searchPR-1))) {
                                $upgrades[$seq] = array(
                                'Tree' => (
                                    $enSeq .
                                    $drSeq .
                                    $boSeq .
                                    $suSeq .
                                    $exSeq .
                                    $brSeq .
                                    $twSeq
                                ),
                                'PR' => (
                                    $cumEn[$enSeq]['PR'] +
                                    $cumDr[$drSeq]['PR'] +
                                    $cumBo[$boSeq]['PR'] +
                                    $cumSu[$suSeq]['PR'] +
                                    $cumEx[$exSeq]['PR'] +
                                    $cumBr[$brSeq]['PR'] +
                                    $cumTW[$twSeq]['PR'] +
                                    $basePR

                                ),
                                'RD' => (
                                    $cumEn[$enSeq]['RD'] +
                                    $cumDr[$drSeq]['RD'] +
                                    $cumBo[$boSeq]['RD'] +
                                    $cumSu[$suSeq]['RD'] +
                                    $cumEx[$exSeq]['RD'] +
                                    $cumBr[$brSeq]['RD'] +
                                    $cumTW[$twSeq]['RD']
                                ),
                                'GC' => (
                                    $cumEn[$enSeq]['GC'] +
                                    $cumDr[$drSeq]['GC'] +
                                    $cumBo[$boSeq]['GC'] +
                                    $cumSu[$suSeq]['GC'] +
                                    $cumEx[$exSeq]['GC'] +
                                    $cumBr[$brSeq]['GC'] +
                                    $cumTW[$twSeq]['GC']
                                ),
                                'GC only' => (
                                    $cumEn[$enSeq]['GC only'] +
                                    $cumDr[$drSeq]['GC only'] +
                                    $cumBo[$boSeq]['GC only'] +
                                    $cumSu[$suSeq]['GC only'] +
                                    $cumEx[$exSeq]['GC only'] +
                                    $cumBr[$brSeq]['GC only'] +
                                    $cumTW[$twSeq]['GC only']
                                )
                                );
                                // Only +1 the sequence for recorded upgrades
                                $seq ++;
                            }

                            $twSeq ++;
                        }
                        $brSeq ++;
                    }
                    $exSeq ++;
                }
                $suSeq ++;
            }
            $boSeq ++;
        }
        $drSeq ++;
    }
    $enSeq ++;
}

// If no upgrades have been calculated display an error
if (count($upgrades) == 0) {
    ?>
    <div class="alert alert-danger">
      <strong>No upgrades found!</strong> return to car
      <a href="index.php" class="alert-link">dashboard</a>.
    </div>
    <br/>
    <a href="index.php" class="btn btn-info" role="button">Return to car table</a>
    <?php
    die();
}
// echo "<br/><br/>Upgrades<br/>";
// foreach ($upgrades as $upgrade) {
//     print_r($upgrade);
//     echo "<br/>";
//}

//Errors caused by running out of memory (128MB),
//try to unset some arrays and variables before sorting:
unset(
    $upgrade, $rows, $row,
    $engine, $drivetrain, $body, $suspension, $exhaust, $brakes, $tiresandWheels,
    $cumEn, $cumDr, $cumBo, $cumSu, $cumEx, $cumBr, $cumTW,
    $enSeq, $drSeq, $boSeq, $suSeq, $exSeq, $brSeq, $twSeq,
    $biggestPR
);

// find the 20 cheapest GC upgrades
$sort = array();
$i = 0;
foreach ($upgrades as $k=>$v) {
    $sort[$i] = $v['GC only'];
    $i ++;
}
/*
echo "Upgrades:<br/>";
print_r($upgrades);

echo "<br /><hr /><br />";

print_r($sort);
*/

sort($sort);

/*
echo "<br /><hr /><br />";

print_r($sort);
*/

// Find the top 20 upgrades in GC.
if ($i > 20) {
    $maxGC = $sort[20];
} else {
    $maxGC = $sort[$i-1];
}

//echo "<br /><hr /><br />Max GC = $maxGC ";
if ($maxGC != 0) {
    // Only keep the upgrades that are less then the maxGC
    for ($i=0; $i < $seq; $i++) {
        //echo "$i: <br/>";
        //print_r($upgrades[$i]);
        if ($upgrades[$i]['GC only'] > $maxGC) {
            unset($upgrades[$i]);
        }
    }
} else {
    // GC is 0, so we need to top 20 R$ instead
    // Repaeat the above process except on R$ (this is less likely)
    unset($sort);
    $sort = array();
    $i = 0;
    foreach ($upgrades as $k=>$v) {
        $sort[$i] = $v['RD'];
        $i ++;
    }
    sort($sort);
    if ($i > 20) {
        $maxRD = $sort[20];
    } else {
        $maxRD = $sort[$i-1];
    }
    for ($i=0; $i < $seq; $i++) {
        //echo "$i: <br/>";
        //print_r($upgrades[$i]);
        if ($upgrades[$i]['RD'] > $maxRD) {
            unset($upgrades[$i]);
        }
    }
}

unset($sort, $maxGC, $maxRD, $i);

$sort = array();
// Prep the sort keys
foreach ($upgrades as $k=>$v) {
    $sort['GC only'][$k] = $v['GC only'];
    $sort['RD'][$k] = $v['RD'];
    $sort['PR'][$k] = $v['PR'];
}

// Sort the array based on the GC smallest first, then min R$ then PR Max, should only be 20 max
array_multisort(
    $sort['GC only'], SORT_ASC, SORT_NUMERIC,
    $sort['RD'], SORT_ASC, SORT_NUMERIC,
    $sort['PR'], SORT_DESC, SORT_NUMERIC,
    $upgrades
);


// Convert the upgrade trees to an array, this makes it easier to compare
$upgradeFU      = treetoarray($fuTree);
$upgradeMaxRD   = treetoarray($rdTree);

// Display the HTML introduction and table title header using bootstrap
?>
<div class="container">
  <h2><?php echo "$car "; ?></h2>
  <h4><?php echo "Upgrades between " . number_format(($searchPR / 10), 1) . " and " . number_format(($searchMax / 10), 1); ?></h4>
  <p>
      The upgrades are shown in order of cheapest Gold upgrade,
      followed by R$ upgrade and PR in descending order.
  </p>
  <div class="alert alert-danger">
    <strong>Warning!</strong> Due to rounding the displayed upgrades may not be the actual value in game. Please cloud save before upgrading, if the PR isn't reached restore and try the next strategy.
  </div>
  <div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Tree</th>
            <th>PR</th>
            <th>R$</th>
            <th>Gold</th>
            <th>Tree description<br/>from Max R$ Tree</th>
            <th>Tree description<br/>from Fully Upgraded Tree</th>
        </tr>
        </thead>
        <tbody>

<?php

$indx = 0;

// Loop through the upgrades array, which is now sorted, display the top 10
// over the search PR, due to rounding the table will display -0.1 of the search
foreach ($upgrades as $row) {
    $upgradeTree     = $row['Tree'];
    $upgradePR       = $row['PR'];
    // We only want to list the best 10 results
    if ($upgradePR >= $searchPR) {
        $indx ++;
        // If index is more than 10 then break the loop
        // if ($indx > 10) {
        //     break;
        // }
    }

    $upgradeRD       = $row['RD'];
    $upgradeGConly   = $row['GC only'];

    // Convert the current upgrade Tree to an array
    $upgradeTreeArray= treetoarray($upgradeTree);
    // Create the descriptions for each tree, easier to compare the arrays.
    $descriptionRD = arraytodescriptionrd($upgradeMaxRD, $upgradeTreeArray);
    $descriptionFU = arraytodescriptionfu($upgradeFU, $upgradeTreeArray);

    // Display each upgrade tree in a table, the PR is changed back from an interger to 1 DP.
    echo '<tr>

            <td>'.$upgradeTree.'&nbsp;</td>

            <td>'.number_format(($upgradePR / 10), 1).'&nbsp;</td>

            <td>'.number_format($upgradeRD).'&nbsp;</td>

            <td>'.number_format($upgradeGConly).'&nbsp;</td>

            <td>'.$descriptionRD.'&nbsp;</td>

            <td>'.$descriptionFU.'&nbsp;</td>

        </tr>';


}   //  END:  Table of upgrade records
?>
        </tbody>
    </table>
  </div>
<br /><hr /><br />

<!-- Button link to return to the dashboard-->
<a href="index.php" class="btn btn-info" role="button">Return to car table</a>

<br /><hr /><br />

<?php

/**
 * Compare upgrade with R$ tree and create the description on +ive values
 *
 * @param Array $treeRD The FU upgrade tree
 * @param Array $tree   The tree to compare
 *
 * @return Str Description
 */
function arraytodescriptionrd($treeRD, $tree)
{
    $ret = array();
    // Loop through the array and compare values
    foreach ($treeRD as $key => $value) {
        if ($treeRD[$key] < $tree[$key]) {
            // Only calculate the +ive value
            $ret[$key] = $tree[$key] - $treeRD[$key];
        } else {
            // negative or same are 0
            $ret[$key] = 0;
        }
    }
    // Sort on value
    arsort($ret);
    // prep the variables and string
    $upgradeDescription = '';
    $currentValue = 0;
    $i = 0;
    foreach ($ret as $key => $value) {
        if ($ret[$key] > 0) {
            if ($i) {
                //Only concatenate a comma when more than one keys
                $upgradeDescription .= ", ";
            }
            $i ++;

            if ($ret[$key] == $currentValue) {
                // Only concatenate the key if the same value e.g. -1 Engine, Drivetrain
                $upgradeDescription .= "$key";
            } else {
                // If a different value concatenate the +vale then the key
                // e.g. -2 Engine, -1 Drivetrain
                $currentValue = $ret[$key];
                $upgradeDescription .= "+$currentValue $key";
            }
        }
    }
    return $upgradeDescription;
}


/**
 * Compare FU upgrade array and create the description on -ive values
 *
 * @param Array $fuTree The FU upgrade tree
 * @param Array $tree   The tree to compare
 *
 * @return Str Description
 */
function arraytodescriptionfu($fuTree, $tree)
{
    $ret = array();
    foreach ($fuTree as $key => $value) {
        $ret[$key] = $tree[$key] - $fuTree[$key];
    }
    //Sort ascending on the values
    asort($ret);
    // Prep the string and variables
    $upgradeDescription = '';
    $currentValue = 0;
    $i = 0;
    //Loop through the array and compare values
    foreach ($ret as $key => $value) {
        // Only negative values
        if ($ret[$key] < 0) {
            if ($i) {
                // Add a comma on more then one key
                $upgradeDescription .= ", ";
            }
            //increase the index after the first value
            $i ++;
            if ($ret[$key] == $currentValue) {
                // If the value is the same as previously only concatenate the key
                $upgradeDescription .= "$key";
            } else {
                // Otherwise add the value concatenate they key
                $currentValue = $ret[$key];
                $upgradeDescription .= "$currentValue $key";
            }
        }
    }
    return $upgradeDescription;
}

/**
 * Convert an upgrade tree to a key array with the upgrades
 *
 * @param str $upgradeTree The upgrade tree to be converted
 *
 * @return Array
 */
function treetoarray($upgradeTree)
{
    // Check $upgradeTree is 7 characters
    if (strlen($upgradeTree) < 7) {
        return '';
    } else {
        // Convert the string to a key array
        $treeArray = str_split($upgradeTree);
        $upgradeArray = array(
            'Engine'            => $treeArray[0],
            'Drivetrain'        => $treeArray[1],
            'Body'              => $treeArray[2],
            'Suspension'        => $treeArray[3],
            'Exhaust'           => $treeArray[4],
            'Brakes'            => $treeArray[5],
            'Tires & Wheels'    => $treeArray[6],
        );
    }
    return $upgradeArray;
}


/**
 * Strip all bad characters from text, only numbers, letters - and _ are returned
 *
 * @param Str $input string to be checked for any dangerous characters
 *
 * @return Str Clean text
 * Source:
 * https://stackoverflow.com/questions/7128856/strip-out-html-and-special-characters
 */
function Strip_Bad_chars($input)
{
    // Strip HTML Tags
    $clear = strip_tags($input);
    // Clean up things like &amp;
    $clear = html_entity_decode($clear);
    // Strip out any url-encoded stuff
    $clear = urldecode($clear);
    // Replace non-AlNum characters with space
    // Can't use this due to special characters in cars. e.g. TM - ()
    // $clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);
    // Replace Multiple spaces with single space
    $clear = preg_replace('/ +/', ' ', $clear);
    // Trim the string of leading/trailing space
    $clear = trim($clear);
    return $clear;
}

?>


    </div><!--Container-->

</body>
</html>