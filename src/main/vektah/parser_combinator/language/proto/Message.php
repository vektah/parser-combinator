<?php

namespace vektah\parser_combinator\language\proto;

class Message
{
    private $name;
    private $members;

    public function __construct($name, $members)
    {
        $this->name = $name;
        $this->members = $members;
    }

    /**
     * @return mixed
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
