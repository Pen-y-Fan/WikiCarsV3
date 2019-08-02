<?php
/**
 * Index page for Wiki Cars, lists all cars in the tCarData table as a table
 *
 * PHP Version: 7.2.10 (probably 5.3+)
 *
 * @category PHP
 * @package  Wiki
 * @author   Michael Pritchard <twitter: @MikeAPritch>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://wikicars.000webhostapp.com/initialise/initialise_tCarData.php
 * File:    index.php
 * Date:    2018-10-05
 *
 * This script displays the cars from MySQL tCarData table as a table
 * with the option to select one.
 * The next script will GET the ID, then choose the PR to find.
 *
 * Using MySQL PDO
 * =====================================
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
  <h2>Real Racing 3 Car Upgrade Selector</h2>
  <p>Select the car from the list below to choose the upgrade required</p>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Car</th>
        <th>Base PR</th>
        <th>PR max R$</th>
        <th>PR Fully Upgraded</th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>

<?php

// PDO connection variables
require '../htconfig/dbConfigwiki.php';

try { // PDO Connection string
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=$charset",
        $user, $password, $options
    );
}
catch(PDOException $e) {
    echo "Error unable to connect: <br/>" . $e->getMessage();
    echo "<br/>";
}

$car_SQLselect = "SELECT ID, Car, BasePR, RD_PR, FU_PR
                FROM `tCarData`
                ORDER BY Seq;";

try {   // PDO Connection string
    $stmt = $pdo->prepare($car_SQLselect);
    $stmt->execute();
    //    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $carlist = $stmt->fetchAll();
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
$pdo = null;

?>

<div class="alert alert-success alert-dismissible fade show">
<button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Displaying: </strong> <?php echo count($carlist); ?> cars
</div>
<?php
foreach ($carlist as $row) {
    // # ID, Car, BasePR, RD_PR, RD_Upg, FU_PR, FU_Upg, Seq
    // '1', 'ARIEL ATOM 3.5', '44.4', '54.2', '3222222', '62.9', '8444333', '1'
    // RD_Upg, FU_Upg and Seq isn't required for this table.
    $carID =    $row['ID'];
    $car =      $row['Car'];
    $basePR =   $row['BasePR'];
    $rdPR =     $row['RD_PR'];
    $fuPR =     $row['FU_PR'];
    // Use ID for the CarID for the link as it is an integer number
    $selectLink = '<a href="CarData.php?carID='.$carID.'" class="btn btn-primary" role="button">Select</a>'
    //$selectLink = '<a href="CarData.php?carID='.$carID.'">Select</a>';
    // Create the HTML table with the variables
    ?>
    <!-- HTML table row -->
    <tr>
        <td><?php echo '<a href="CarData.php?carID='.$carID.'">'.$car.'</a>'; ?></td>
        <td><?php echo $basePR; ?></td>
        <td><?php echo $rdPR; ?></td>
        <td><?php echo $fuPR; ?></td>
        <td><?php echo $selectLink; ?></td>
    </tr>

    <?php
}   //End of foreach

?>

    </tbody>
</table>
</div>

</body>
</html>
