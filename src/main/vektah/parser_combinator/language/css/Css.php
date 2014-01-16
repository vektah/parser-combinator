<?php


namespace vektah\parser_combinator\language\css;

use vektah\parser_combinator\exception\ParseException;

class Css
{
    /** @var CssRuleSet[] */
    public $rulesets = [];

    /**
     * @param CssRuleSet[] $rulesets
     *
     * @throws ParseException
     */
    public function __construct(array $rulesets) {
        foreach ($rulesets as $ruleset) {
            if (!($ruleset instanceof CssRuleSet)) {
                throw new ParseException('Non CssRuleSet in a Css file');
            }
        }
        $this->rulesets = $rulesets;
    }

    /**
     * @return CssRuleSet[]
     */
    public function getRulesets()
    {
        return $this->rulesets;
    }

    /**
     * @param string $selector
     * @return CssDeclaration[]
     */
    public function getDeclarations($selector) {
        $matches = [];
        // When dealing with selectors describing objects commas should denote a match of any of these things.
        foreach (explode(',', $selector) as $part) {
            $part = trim($part);
            $object = CssSelectorParser::instance()->parseString($part)->define();

            foreach ($this->rulesets as $ruleset) {
                if ($ruleset->getSelector()->matchesObject($object)) {
                    foreach ($ruleset->getDeclarations() as $declaration) {
                        $matches[] = $declaration;
                    }
                }
            }
        }

        return $matches;
    }

    /**
     * From a given selector work out what css applies
     *
     * @param string $selector
     * @return string
     */
    public function getMatchingCss($selector) {
        $declarations = $this->getDeclarations($selector);

        $css = '';

        foreach ($declarations as $declaration) {
            $css .= $declaration->toCss();
        }

        return $css;
    }

    public function toCss()
    {
        $css = '';
        foreach ($this->rulesets as $ruleset) {
            $css .= $ruleset->toCss();
        }

        return $css;
    }
}
