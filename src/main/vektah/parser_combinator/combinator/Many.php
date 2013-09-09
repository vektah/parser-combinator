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
use vektah\parser_combinator\Result;

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
        $aggregatedResult = [];
        $isPositive = false;
        $count = 0;

        $lastOffset = $input->getOffset();
        while ($this->max === null || $count < $this->max) {
            $result = parent::combine($input);

            if ($result->positiveMatch) {
                $isPositive = true;
            }

            if ($result->errorMessage) {
                if ($count < $this->min) {
                    return Result::error('At ' . $input->getPositionDescription() . ": Expected {$this->min} elements, but found $count");
                } elseif ($result->positiveMatch) {
                    // If a branch got far enough to assert itself but still returned an error then we need to propagate
                    return $result;
                }

                // Most errors are normal, they just signal the end of the repetition.
                break;
            }

            if ($input->getOffset() == $lastOffset) {
                throw new GrammarException('At ' . $input->getPositionDescription() . ": parser did not consume any input, this is a bug with the grammar. Make sure there are no zero width matches in a Many.");
            }

            if ($result->hasData) {
                $aggregatedResult[] = $result->data;
            }

            $lastOffset++;
            $count++;
        }

        if ($count < $this->min) {
            return Result::error('At ' . $input->getPositionDescription() . ": Expected {$this->min} elements, but found $count", $isPositive);
        }

        if ($aggregatedResult === []) {
            return Result::nonCapturingMatch($isPositive);
        } else {
            return Result::match($aggregatedResult, $isPositive);
        }
    }
}
