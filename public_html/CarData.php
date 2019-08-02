<?php
/**
 * PHP Wiki to Select the Car Upgrades from carupgrades table
 *
 * PHP Version: 7.2.10 (probably 5.3+)
 *
 * @category PHP
 * @package  Wiki
 * @author   Michael Pritchard <twitter: @MikeAPritch>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://localhost/wiki/CarData_v1.1.php
 * File:  CarData.php
 * Date:  2018-10-06
 *
 * Requires:
 * MySQL table `tCarData` containing the car PR and tree data
 *
 * Outputs the car to the SelectCarUpgrades.php script
 *
 * v1.0 Created based on CarDashboard_v1.3.1.php, changed to 1 car
 *      using $_GET['carID'], passed from CarDashboard_v1.4.php
 * v1.1 Updated link to SelectCarUpgradesv3.0.php (on the fly test)
 * v1.2 Webversion for `tCarData`
 *
 * ==================================================================================
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Real Racing 3 Car Upgrade Selector</title>
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
        <strong>Displaying: </strong> <?php echo $message; ?> cars
    </div>
    <?php
}

// Check if the carID has been passed to the script.
if (isset($_GET['carID'])) {
    $carID = Strip_Bad_chars($_GET['carID']);
    //echo "<h1><b>$ID</b></h1>";
} else {
    // If not sent display to go back to dashboard and stop the script
    ?>
      <div class="alert alert-danger">
        <strong>No car specified!</strong> return to car
        <a href="index.php" class="alert-link">dashboard</a>.
      </div>
    <?php
    die();
}
// MySQL connection variables (PDO used)
// ($host, $dbname, $user, $password, $options)
require '../htconfig/dbConfigwiki.php';

//try
{ // PDO Connection string
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=$charset",
        $user, $password, $options
    );
}
// catch(PDOException $e) {
//     echo "Error unable to connect: <br/>" . $e->getMessage();
//     echo "<br/>";
// }

// # ID, Car, BasePR, RD_PR, RD_Upg, FU_PR, FU_Upg, Seq
// '1', 'ARIEL ATOM 3.5', '44.4', '54.2', '3222222', '62.9', '8444333', '1'
// No need for ID or Seq
$car_SQLselect = "SELECT Car, BasePR, RD_PR, RD_Upg, FU_PR, FU_Upg
                FROM `tCarData`
                WHERE `ID` = '$carID';";

try {   // PDO Connection string
    $stmt = $pdo->prepare($car_SQLselect);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $row = $stmt->fetch();  // Only fetch one record for 1 car
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
$pdo = null;

if (!$row) {
    ?>
    <div class="alert alert-danger">
      <strong>No car found!</strong> return to car
      <a href="index.php" class="alert-link">dashboard</a>.
    </div>
    <?php
    die();
}
//# Car, BasePR, RD_PR, RD_Upg, FU_PR, FU_Upg
$car =      $row['Car'];
$basePR =   $row['BasePR'];
$rdPR =     $row['RD_PR'];
$rdTree =   $row['RD_Upg'];
$fuPR =     $row['FU_PR'];
$fuTree =   $row['FU_Upg'];

echo "<h2>$car</h2>";


{   //  FORM searchPR
?>
<p><b>Select the required PR from the drop down list</b></p>
<div class="form-group">
    <form name="search" action="CarUpgrades.php" method="post">
    <input type="hidden" name="car" value="<?php echo $car; ?>"/>
    <input type="hidden" name="carID" value="<?php echo $carID; ?>"/>
    <input type="hidden" name="TreeRD" value="<?php echo $rdTree; ?>"/>
    <input type="hidden" name="TreeFU" value="<?php echo $fuTree; ?>"/>
    <input type="hidden" name="basePR" value="<?php echo $basePR; ?>"/>
    <input type="hidden" name="rdPR" value="<?php echo $rdPR; ?>"/>
    <input type="hidden" name="fuPR" value="<?php echo $fuPR; ?>"/>
    <table>
    <tr>
        <td>Car Base PR</td>
        <td align="right"><?php echo number_format($basePR, 1); ?></td>
    </tr>
    <tr>
        <td>PR max with R$</td>
        <td align="right"><?php echo number_format($rdPR, 1); ?></td>
    </tr>
    <tr>
        <td>Fully Upgraded PR</td>
        <td align="right"><?php echo number_format($fuPR, 1); ?></td>
    </tr>
    <tr>
        <td>Upgrades with R$ only</td>
        <td align="right"><?php echo $rdTree; ?></td>
    </tr>
    <tr>
        <td>Upgrades when fully upgraded</td>
        <td align="right"><?php echo $fuTree; ?></td>
    </tr>

    <tr>
        <td>Select Required PR to Search</td>
        <td align="right"><select name="searchPR">

        <option value="<?php $basePR; ?>" label="Search PR.."
        selected="selected"><?php echo number_format($basePR, 1); ?></option>
        <?php
        // Create drop down box from BasePR to fuPR with increments of 0.1
        // Seeing some odd rounding e.g. 75.6 + 0.1 = 75.699999!! So multiply by 10
        // Display the value divided by 10 displayed to one decimal place.
        $fromPR = $basePR*10;
        $toPR   = $fuPR*10;
        for ($i=$fromPR; $i <= $toPR; $i += 1) {
            echo "<option>".number_format($i/10, 1)."</option>";
        }
        ?>
        </select></td>
        <!--</tr> Try the button next to the drop down list
        <tr>
            <td></td>-->
            <td>
                <button type="submit" class="btn btn-primary">Search</button>
            </td>
        </tr>
    </table>
    </form>
    </div>
<?php
} // End of form
?>

<br /><hr /><br />

<!-- Link to return to the dashboard-->
<a href="index.php" class="btn btn-danger" role="button">Return to car table</a>

<br /><hr /><br />

<?php
// runtime_end will display the stats for how long the script took to run
// needs fn_runtime to be called at the beginning of a script to initialise
// require 'includes/runtime_end.php';

/**
 * Strip all bad characters from text, only numbers are returned
 *
 * @param Str $input string to be checked for any dangerous characters
 *
 * @return Str Clean text
 */
function Strip_Bad_chars($input)
{
    //$output = preg_replace("/[^a-zA-Z0-9_-]/", "", $input);
    $output = preg_replace("/[^0-9]/", "", $input);
    return $output;
}

?>