<?php
/**
 * Created by IntelliJ IDEA.
 * User: adam
 * Date: 9/8/13
 * Time: 6:37 PM
 * To change this template use File | Settings | File Templates.
 */

namespace vektah\parser_combinator\combinator;


use vektah\parser_combinator\exception\GrammarException;
use vektah\parser_combinator\exception\ParseException;
use vektah\parser_combinator\Input;

class Many extends Choice
{
    private $min;
    private $max;

    public function __construct(array $parsers, $min = 0, $max = null)
    {
        parent::__construct($parsers);

        $this->min = $min;
        $this->max = $max;
    }

    public function combine(Input $input)
    {
        $initialOffset = $input->getOffset();
        $aggregatedResult = [];
        $count = 0;

        $lastOffset = $input->getOffset();
        while (!$input->complete()) {
            if ($this->max !== null && $count >= $this->max) {
                return $aggregatedResult;
            }

            try {
                $result = parent::combine($input);
            } catch (ParseException $e) {
                if ($count < $this->min) {
                    throw new ParseException('At ' . $input->getPositionDescription() . ": Expected {$this->min} elements, but found $count");
                }

                if ($aggregatedResult) {
                    return $aggregatedResult;
                }
                return null;
            }
            if ($input->getOffset() == $lastOffset) {
                throw new GrammarException('At ' . $input->getPositionDescription() . ": parser did not consume any input, this is a bug with the grammar. Make sure there are no zero width matches in a Many.");
            }

            if ($result != null) {
                $aggregatedResult[] = $result;
            }

            $lastOffset++;
            $count++;
        }

        if ($count < $this->min) {
            throw new ParseException('At ' . $input->getPositionDescription() . ": Expected {$this->min} elements, but found $count");
        }

        if ($aggregatedResult) {
            return $aggregatedResult;
        }

        return null;
    }
}
