<?php
/**
 * Created by PhpStorm.
 * User: ChrisRisley
 * Date: 8/14/17
 * Time: 9:48 AM
 */

function get_input()
{
    $input = [];
    $selected_script = $_POST['script_choice'];
    $selected_year = $_POST['year'];
    $selected_month = $_POST['month'];

    if (empty($selected_script)){
        $selected_script = "";
    }
    if (empty($selected_year)){
        $selected_year = "2017";
    }
    if (empty($selected_month)){
        $selected_month = "Aug";
    }
    $input = array($selected_script, $selected_month, $selected_year);
    return $input;
}
