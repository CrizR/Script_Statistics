<?php
/**
 * Created by PhpStorm.
 * User: ChrisRisley
 * Date: 8/22/17
 * Time: 5:13 AM
 */

/**
 * Class Script represents a Script within Studio. It contains an array of calls and a name.
 */
class Script
{
    private $calls;
    private $name;

    /**
     * Script constructor.
     * @param array $calls
     * @param string $script_name
     */
    public function __construct(array $calls, $script_name)
    {
        $this->calls = $calls;
        $this->name = $script_name;
    }

    /**
     * Retrieves the Script name
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Determines the lifespan of the script in terms of activity
     * @return array
     */
    public function getScriptLife()
    {
        $earliest_date = 99999999999999;
        $latest_date = 0;
        foreach ($this->calls as $call) {
            $call_life = $call->getCallLife();
            $earliest_node = $call_life[0];
            $latest_node = $call_life[1];
            if ($earliest_node < $earliest_date) {
                $earliest_date = $earliest_node;
            }
            if ($latest_node > $latest_date) {
                $latest_date = $latest_node;
            }
        }
        return array(
            date('Y-m-d h:i:s', $earliest_date / 1000),
            date('Y-m-d h:i:s', $latest_date / 1000));
    }

    /**
     * Determines the number of calls that occur per day
     * @param $month
     * @param $year
     * @return float|int
     */
    public function getCallsPerDay($month, $year)
    {
        $scriptLife = $this->getScriptLife();
        $time1 = new DateTime($scriptLife[0]);
        $diff_in_years = (int)$time1->diff(new DateTime(date('Y-m-d H:i:s')))->format("%y");
        $diff_in_months = (int)$time1->diff(new DateTime(date('Y-m-d H:i:s')))->format("%m");
        $diff_in_days = (int)$time1->diff(new DateTime(date('Y-m-d H:i:s')))->format("%d");
        $total_diff = ($diff_in_years * 365) + ($diff_in_months * 28) + $diff_in_days;
        return $this->totalCalls($month, $year) / $total_diff;
    }


    /**
     * Retrieves every node within the script
     * @return array
     */
    public function getNodes()
    {
        $script_nodes = [];
        foreach ($this->calls as $call) {
            $nodes = $call->getNodes();
            $script_nodes = array_merge($script_nodes, $nodes);
        }
        return array_unique($script_nodes);
    }


    /**
     * Determines the average length of a call in this script.
     * @param $month
     * @param $year
     * @return float|int
     */
    public function getAvgCallLength($month, $year)
    {
        $sum = 0;
        foreach ($this->calls as $call) {
            $callLife = $call->getCallLife();
            $time1 = new DateTime(date('Y-m-d h:i:s', $callLife[0] / 1000));
            $time2 = new DateTime(date('Y-m-d h:i:s', $callLife[1] / 1000));
            $interval = $time1->diff($time2);
            $sum += (int)$interval->format("%s");
        }
        return $sum / $this->totalCalls($month, $year);
    }

    /**
     * Determines the total number of calls.
     * @param $month
     * @param $year
     * @return int
     */
    public function totalCalls($month, $year)
    {
        return sizeof($this->calls);
    }


    /**
     * Determines the average number of calls on a each day in a week given the
     * month and year.
     * @param $month
     * @param $year
     * @return array
     */
    public function getUsageTimes($month, $year)
    {

        $days_of_week = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        //Maps each day to a value that will represent the number of calls on that day
        $avg_by_day = array(
            "Monday" => 0,
            "Tuesday" => 0,
            "Wednesday" => 0,
            "Thursday" => 0,
            "Friday" => 0,
            "Saturday" => 0,
            "Sunday" => 0
        );

        $current_by_day = array(
            "Monday" => 0,
            "Tuesday" => 0,
            "Wednesday" => 0,
            "Thursday" => 0,
            "Friday" => 0,
            "Saturday" => 0,
            "Sunday" => 0
        );

        $peak_by_day = array(
            "Monday" => 0,
            "Tuesday" => 0,
            "Wednesday" => 0,
            "Thursday" => 0,
            "Friday" => 0,
            "Saturday" => 0,
            "Sunday" => 0
        );

        $todays_week = date('W');
        $todays_month = date('M');


        foreach ($this->calls as $call) {
            $call_cdate = date('lMY', $call->getDate() / 1000);
            $call_date = date('Y-m-d', $call->getDate() / 1000);;
            $call_weak = date('W', $call->getDate() / 1000);
            $call_month = date('M', $call->getDate() / 1000);
            $call_day = date("l", $call->getDate() / 1000);

            $this->update_date($month, $year, $call_cdate, $avg_by_day, .25);
            if ($call_weak == $todays_week) {
                $this->update_date($month, $year, $call_cdate, $current_by_day, 1);
            }
            if ($call_month == $todays_month) {
                $calls_on_day = $this->callsOnDay($call_date);
                foreach ($days_of_week as $day) {
                    if ($call_day == $day && $calls_on_day > $peak_by_day[$day]) {
                        $peak_by_day[$day] = $calls_on_day;
                    }
                }
            }
        }

        return array("avg" => $avg_by_day, "current" => $current_by_day, "peak" => $peak_by_day);
    }

    /**
     * @param $month
     * @param $year
     * @param $call_day
     * @param $by_day
     * @param $num
     */
    private function update_date($month, $year, $call_day, &$by_day, $num): void
    {
        $days_of_week = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        foreach ($days_of_week as $day) {
            if ($call_day == $day . $month . $year) {
                $by_day[$day] += $num;
            }
        }
    }

    /**
     * Determines the top users of this script.
     * @param $month
     * @param $year
     * @return array
     */
    public function getTopUsers($month, $year)
    {
        $anis = [];
        foreach ($this->calls as $call) {
            $ani = $call->getAni();
            if (empty($ani) or $ani == ' ') {
                $ani = 0000000000;
            }
            array_push($anis, $ani);
        }
        $ranking = array_count_values($anis);
        arsort($ranking);
        return $ranking;
    }


    /**
     * To string method for Script.
     * @return string
     */
    public function __toString()
    {
        return $this->name . sizeof($this->calls);
    }

    /**
     * Retrieves information specific to the given node_type
     * @param $node_type
     * @return array
     */
    public function getNodeData($node_type): array
    {
        $node_data = [];
        foreach ($this->calls as $call) {
            $nd = $call->getNodeData($node_type);
            if (!empty($nd)) {
                array_push($node_data, $nd);
            }
        }
        if ($node_type != "menu") {
            return array("data_usage" => sizeof($node_data));
        } else {
            return $this->getMenuData($node_data);
        }
    }

    /**
     * Determines the average confidence level
     * @param $node_data
     * @return array
     */
    private function getMenuData($node_data)
    {
        $confidence = $this->confidenceInterval($node_data);
        $ratio = $this->speechRatio($node_data);
        $utterance = $this->commonUtterance($node_data);
        return array("data_usage" => sizeof($node_data), "utterance" => $utterance,
            "acl" => array_sum($confidence) / sizeof($confidence), "ratio" => $ratio);
    }

    /**
     * Determines the most common utterance and child node
     * @param $node_data
     * @return array
     */
    private function commonUtterance($node_data)
    {
        $utterance = [];
        foreach ($node_data as $value) {
            if (!empty($value)) {
                if ($value[0]["inputmode"] == "voice") {
                    array_push($utterance, $value[0]["utterance"]);
                }
            }
        }
        $ranking = array_count_values($utterance);
        arsort($ranking);
        $top = array_slice($ranking, 0, 3, true);
        return array_keys($top);
    }

    /**
     * Determines the ratio bewteen users choosing speech or dtmf
     * @param $node_data
     * @return float|int
     */
    private function speechRatio($node_data)
    {
        $dtmf_sum = 0;
        $speech_sum = 0;
        foreach ($node_data as $value) {
            if (!empty($value)) {
                if ($value[0]["inputmode"] == "dtmf") {
                    $dtmf_sum++;
                } else {
                    $speech_sum++;
                }
            }
        }
        return $speech_sum / $dtmf_sum;
    }


    /**
     * Determines the sum of the confidence intervals
     * @param $values
     * @return array
     */
    private function confidenceInterval($values)
    {
        $con = [];
        foreach ($values as $value) {
            if (!empty($value)) {
                if ($value[0]["inputmode"] == "voice") {
                    array_push($con, $value[0]["confidence"]);
                }
            }
        }
        return $con;
    }


    /**
     * Determines the avg call length of calls that occurred today.
     * @return float|int
     */
    public function getTodaysAvgCallLength()
    {
        $date = date('Y-m-d');
        $cl_on_day = [0];
        foreach ($this->calls as $call) {
            $call_date = date('Y-m-d', $call->getDate() / 1000);
            if ($date == $call_date) {
                $callLife = $call->getCallLife();
                $time1 = new DateTime(date('Y-m-d h:i:s', $callLife[0] / 1000));
                $time2 = new DateTime(date('Y-m-d h:i:s', $callLife[1] / 1000));
                $interval = $time1->diff($time2);
                array_push($cl_on_day, (int)$interval->format("%s"));
            }
        }

        if ((sizeof($cl_on_day) - 1) == 0) {
            return 0;
        } else {
            return array_sum($cl_on_day) / (sizeof($cl_on_day) - 1);
        }
    }

    /**
     * Determines the number of calls that occured today.
     * @param day
     * @return int
     */
    public function callsOnDay($day)
    {
        $count = 0;
        foreach ($this->calls as $call) {
            $call_date = date('Y-m-d', $call->getDate() / 1000);
            if ($day == $call_date) {
                $count++;
            }
        }
        return $count;
    }


}