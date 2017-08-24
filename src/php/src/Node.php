<?php
/**
 * Created by PhpStorm.
 * User: ChrisRisley
 * Date: 8/22/17
 * Time: 5:15 AM
 */

/**
 * Class Node represents a Node found within the Studio Script.
 */
class Node
{
    private $node_type = '';
    private $timestamp = 0;
    private $uuid = 0;
    private $data = [];


    /**
     * Node constructor.
     * @param string $node_type
     * @param int $timestamp
     * @param int $uuid
     */
    public function __construct($node_type, $timestamp, $uuid, $data)
    {
        $this->node_type = $node_type;
        $this->timestamp = $timestamp;
        $this->uuid = $uuid;
        $this->data = $data;
    }

    /**
     * Retrieves the node type of this node.
     * @return string
     */
    public function getNodeType(): string
    {
        return $this->node_type;
    }

    /**
     * Retrieves the timestamp of this node.
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * Retrieves the Uuid of this node.
     * @return int
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Retrieves the data found in this Node instance.
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Overrides the toString method to print out Node on echo.
     * @return string
     */
    public function __toString()
    {
        return 'Node';
    }


}