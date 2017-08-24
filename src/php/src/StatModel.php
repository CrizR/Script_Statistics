<?php
/**
 * Created by PhpStorm.
 * User: ChrisRisley
 * Date: 8/14/17
 * Time: 9:48 AM
 */


require("vendor/ktamas77/firebase-php/src/firebaseInterface.php");
require("vendor/ktamas77/firebase-php/src/firebaseStub.php");
require("vendor/ktamas77/firebase-php/src/firebaseLib.php");
require("Node.php");
require('Call.php');
require("Script.php");
//error_reporting(LIBXML_ERR_FATAL);
//ini_set("display_errors","On");
//
/**
 * Class StatModel encapsulates the data found in the Firebase DB and reorganizes it as such:
 * Script ->
 * TODO: DATE ->
 * Call ->
 * Node.
 *
 * TODO: TESTING (Can't install PHPunit for some reason)
 */
class StatModel
{

    private $scripts_data = [];

    /**
     * firebaseconnect constructor.
     * @param string $default_url
     * @param string $default_token
     * @param string $default_path
     */
    public function __construct($default_url, $default_token, $default_path)
    {
        $this->initModel($default_url, $default_token, $default_path);
    }


    /**
     * Pulls the data from the fb db and then initializes the model's script data.
     * @param $default_url
     * @param $default_token
     * @param $default_path
     */
    private function initModel($default_url, $default_token, $default_path)
    {
        date_default_timezone_set("America/Los_Angeles");
        $firebase = new \Firebase\FirebaseLib($default_url, $default_token);
        $data = $firebase->get($default_path);
        $data = json_decode($data, true);
        $script_uuids = [];
        $script_names = [];
        $uuids = [];
        $calls = [];

        //iterate through the data in the DB
        foreach ($data as $var) {
            foreach ($var as $date) {
                foreach ($date as $day) {
                    foreach ($day as $log) {
                        foreach ($log as $log_data) {
                            $node_type = $log_data['node_type'];
                            $timestamp = $log_data['timestamp'];
                            $uuid = $log_data["uuid"];
                            $node_data = $log_data['node_values'];

                            $this->initCall($node_type, $log_data, $uuid, $timestamp, $uuids,
                                $calls, $script_names, $script_uuids, $script_name);

                            //add each node to the relevent call and date
                            $calls[$uuid]->addNode(new Node($node_type, $timestamp, $uuid, $node_data));
                        }
                    }
                }
            }
        }
        //initialize each script with the relevent calls
        $this->initScripts($script_names, $script_uuids, $calls);
    }


    /**
     * Initializes a Call object with the data found in the Firebase DB.
     * @param $node_type
     * @param $log_data
     * @param $uuid
     * @param $uuids
     * @param $calls
     * @param $script_names
     * @param $script_uuids
     * @param $script_name
     */
    private function initCall($node_type, $log_data, $uuid, $timestamp, &$uuids, &$calls,
                              &$script_names, &$script_uuids, &$script_name): void
    {
        if ($node_type == "start_node") {
            $node_values = json_decode($log_data["node_values"], true);
            $script_name = $node_values["instance_name"];
            $ani = $node_values["ANI"];
            $dnis = $node_values["DNIS"];

            //If uuid is not in uuids, it's a new call: add it to calls
            if (!in_array($uuid, $uuids)) {
                $call = new Call(array(), $ani, $dnis, $uuid, $timestamp);
                $calls = array_merge($calls, array($uuid => $call));
                array_push($uuids, $uuid);
            }

            //if it's a new script name, add it to the script names and uuids
            if (!in_array($script_name, $script_names)) {
                array_push($script_names, $script_name);
                $script_uuids = array_merge($script_uuids, array($script_name => array()));
            } else {
                array_push($script_uuids[$script_name], $uuid);
            }
        }
    }

    /**
     * Initialize each script with the corresponding call logs (array)
     * @param $script_names
     * @param $script_uuids
     * @param $calls
     */
    private function initScripts($script_names, $script_uuids, $calls): void
    {
        foreach ($script_names as $script_name) {
            $script_calls = [];
            $uuids = $script_uuids[$script_name];
            foreach ($uuids as $uuid) {
                array_push($script_calls, $calls[$uuid]);
            }
            $this->scripts_data = array_merge($this->scripts_data,
                array($script_name => new Script($script_calls, $script_name)));
        }
    }

    /**
     * Retrieves the names of the scripts within the database.
     * @return array
     */
    public function getScripts()
    {
        return $this->scripts_data;
    }


    /**
     * Determines the total calls that occur on the script in the given time period.
     * @param $script_name
     * @param $month
     * @param $year
     * @return mixed
     */
    public function getTotalCalls($script_name, $month, $year)
    {
        return $this->scripts_data[$script_name]->totalCalls($month, $year);
    }

    /**
     * Determines the average length of a call on the given script in the given time period.
     * TODO: APPLY TIME PERIOD TO RESULTS
     * @param $script_name
     * @param $month
     * @param $year
     * @return mixed
     */
    public function getAvgCallLength($script_name, $month, $year)
    {
        return $this->scripts_data[$script_name]->getAvgCallLength($month, $year);
    }

    /**
     * Determines the number of calls that occur on the given script, in the given time period.
     * TODO: APPLY TIME PERIOD TO RESULTS
     * @param $script_name
     * @param $month
     * @param $year
     * @return mixed
     */
    public function getCallsPerDay($script_name, $month, $year)
    {
        return $this->scripts_data[$script_name]->getCallsPerDay($month, $year);

    }

    /**
     * Retrieves the lifespan in terms of activity of the given script.
     * @param $script_name
     * @return mixed
     */
    public function getScriptLife($script_name)
    {
        return $this->scripts_data[$script_name]->getScriptLife();
    }


    /**
     * Retrieves information about the given node within the given script.
     * @param $script_name
     * @param $node_type
     * @return mixed
     */
    public function getNodeData($script_name, $node_type)
    {
        return $this->scripts_data[$script_name]->getNodeData($node_type);
    }

    /**
     * Retrieves the Nodes that are within the given script.
     * @param $script_name
     * @return mixed
     */
    public function getNodes($script_name)
    {
        return $this->scripts_data[$script_name]->getNodes();
    }

    /**
     * Determines who the top users are, and how often they've used the script.
     * @param $script_name
     * @param $month
     * @param $year
     * @return array
     */
    public function getTopUsers($script_name, $month, $year)
    {
        return $this->scripts_data[$script_name]->getTopUsers($month, $year);
    }


    /**
     * Determine peak times of script usage for the given script at the given time.
     * @param $script_name
     * @param $month
     * @param $year
     * @return mixed
     */
    public function getUsageTimes($script_name, $month, $year)
    {
        return $this->scripts_data[$script_name]->getUsageTimes($month, $year);
    }


    /**
     * Determines the relative call time in relation to the average call time.
     * @param $script_name
     * @param $month
     * @param $year
     * @return float|int
     */
    public function relativeCallLength($script_name, $month, $year)
    {
        $avg_call_length = $this->scripts_data[$script_name]->getAvgCallLength($month, $year);
        $today_call_length = $this->scripts_data[$script_name]->getTodaysAvgCallLength();
        return ($today_call_length / $avg_call_length) * 100;

    }

    /**
     * Determines the number of calls today.
     * @param $script_name
     * @return mixed
     */
    public function callsToday($script_name)
    {
        return $this->scripts_data[$script_name]->callsOnDay(date('Y-m-d'));
    }

    /**
     * Determines the script usage of today compared to other days.
     * @param $script_name
     * @param $month
     * @param $year
     * @return float|int
     */
    public function relativeScriptUsage($script_name, $month, $year)
    {
        $calls_total = $this->scripts_data[$script_name]->totalCalls($month, $year);
        $calls_today = $this->scripts_data[$script_name]->callsOnDay(date('Y-m-d'));
        return ($calls_today / $calls_total) * 100;
    }
}





