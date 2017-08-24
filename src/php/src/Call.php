<?php
/**
 * Created by PhpStorm.
 * User: ChrisRisley
 * Date: 8/22/17
 * Time: 5:15 AM
 */

/**
 * Class Call represents an array of nodes that were logged upon usage.
 */
class Call
{
    private $nodes = [];
    private $ani;
    private $dnis;
    private $uuid;
    private $date;

    /**
     * Call constructor.
     * @param $nodes
     * @param $ani
     * @param $dnis
     * @param $name
     */
    public function __construct($nodes, $ani, $dnis, $uuid, $date)
    {
        $this->nodes = $nodes;
        $this->ani = $ani;
        $this->dnis = $dnis;
        $this->uuid = $uuid;
        $this->date = $date;
    }

    /**
     * Determines the date of the call
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Determines the Uuid of the call
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }


    /**
     * Retrieves the nodes found in the call in order of time (asc).
     * @return mixed
     */
    public function getNodes()
    {
        $nodes = [];
        foreach ($this->nodes as $node) {
            $nodes = array_merge($nodes, array($node->getNodeType() => $node->getTimestamp()));
        }
        asort($nodes);
        return array_keys($nodes);
    }

    /**
     * Retrieves the ANI of the call.
     * @return mixed
     */
    public function getAni()
    {
        return $this->ani;
    }

    /**
     * Retrieves the DNIS of the call.
     * @return mixed
     */
    public function getDnis()
    {
        return $this->dnis;
    }


    /**
     * Adds the given Node to the call.
     * @param $node
     */
    public function addNode($node)
    {
        array_push($this->nodes, $node);
    }


    /**
     * toString method Overrides existing toString method.
     * @return string
     */
    public function __toString()
    {
        return "Call || UUID:" . $this->uuid . "|| Nodes#:" . sizeof($this->nodes);
    }

    /**
     * Determines the lifespan in terms of earliest and latest node timestamp of the call.
     * @return array
     */
    public function getCallLife()
    {
        $earliest_time = 999999999999999;
        $latest_time = 0;
        foreach ($this->nodes as $node) {
            if ($node->getTimestamp() < $earliest_time) {
                $earliest_time = $node->getTimestamp();
            }
            if ($node->getTimestamp() > $latest_time) {
                $latest_time = $node->getTimestamp();
            }
        }
        return array($earliest_time, $latest_time);
    }

    /**
     * Retrieves all of the node data found within the call that is relevant to the given node_type.
     * @param $node_type
     * @return array
     */
    public function getNodeData($node_type)
    {
        $node_data = [];
        foreach ($this->nodes as $node) {
            if ($node->getNodeType() == $node_type) {
                $data = $node->getData();
                array_push($node_data, json_decode($data, true));
            }
        }
        return $node_data;
    }

}