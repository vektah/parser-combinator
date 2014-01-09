<?php


namespace vektah\parser_combinator\language\css;

class HashSelector
{
    public $hash;

    public function __construct($hash)
    {
        $this->hash = $hash;
    }
}
