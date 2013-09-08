<?php
/**
 * Created by IntelliJ IDEA.
 * User: adam
 * Date: 9/8/13
 * Time: 8:22 PM
 * To change this template use File | Settings | File Templates.
 */

namespace vektah\parser_combinator\exception;


use Exception;

class GrammarException extends Exception
{
    /**
     * Workaround until php 5.5's class operator is implemented.
     *
     * @see http://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.class
     */
    const _CLASS = __CLASS__;
}
