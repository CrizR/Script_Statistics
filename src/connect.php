<?php
/**
 * Created by PhpStorm.
 * User: ChrisRisley
 * Date: 8/21/17
 * Time: 3:46 PM
 */

require("php/src/StatModel.php");
require("input.php");

/**
 * Change these to alter the Firebase DB being analyzed
 */
$default_url = 'https://script-data-visualization.firebaseio.com';
$default_token = '';
$default_path = 'nodelog';
$url = isset($_POST['url']) ? $_POST['url'] : $default_url;
$token = isset($_POST['secret']) ? $_POST['secret'] : $default_token;
$connect = new StatModel($url, $token, $default_path);
$input = get_input();