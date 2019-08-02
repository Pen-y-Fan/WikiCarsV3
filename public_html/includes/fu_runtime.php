<?php
/**
 * Function: runtime
 *
 * PHP Version: 7.2.9
 *
 * @category PHP
 * @package  Wiki
 * @author   Michael Pritchard <twitter: @MikeAPritch>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://localhost/wiki/includes/fu_runtime.php
 * File:  includes\fu_runtime.php
 * Date:  2018-08-27
 *
 * Used in CarDashboard_vx.y.php (v2.4+)
 * ==============================================================================
 */
$time_start = microtime(true);
$rustart = getrusage();

/**
 * Function to calculate the processing time
 *
 * @param array $ru    the end time of the script
 * @param array $rus   the start time of the script
 * @param var   $index process type stime
 *
 * @return runtime from start to end
 */
function rutime($ru, $rus, $index)
{
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
    -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

?>