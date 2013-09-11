<?php

namespace vektah\parser_combinator\language\proto;

class Message
{
    public $name;
    public $members;
    public $fields = [];
    public $options = [];

    public function __construct($name, $members)
    {
        $this->name = $name;
        $this->members = $members;

        foreach ($members as $member) {
            if ($member instanceof Field) {
                $this->fields[] = $member;
            }

            if ($member instanceof Option) {
                $this->options[] = $member;
            }
        }
    }
}
