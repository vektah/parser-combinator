<?php


namespace vektah\parser_combinator\test\language\css;

use PHPUnit_Framework_TestCase as TestCase;
use vektah\parser_combinator\language\css\CssParser;

class LargeCssFileTest extends TestCase
{
    /** @var CssParser */
    private $parser;

    public function setUp() {
        $this->parser = new CssParser();
    }

    public function testLargeCssFile()
    {
        $css = $this->parser->parseString($this->css());

        $this->assertEquals([], $css->getDeclarations('h1'));
    }

    private function css() {
        return <<< HERE
/* ---------- Color Pallet ---------- */
/* ---------- Global Variables ---------- */
/**********************************************
* Ink v1.0.3 - Copyright 2013 ZURB Inc        *
**********************************************/
/* Client-specific Styles & Reset */
/* line 7, ../../../../scss/email/_default.scss */
#outlook a {
  padding: 0;
}

/* line 11, ../../../../scss/email/_default.scss */
body {
  width: 100% !important;
  -webkit-text-size-adjust: 100%;
  -ms-text-size-adjust: 100%;
  margin: 0;
  padding: 0;
}

/* line 19, ../../../../scss/email/_default.scss */
.ExternalClass {
  width: 100%;
}

/* line 28, ../../../../scss/email/_default.scss */
.ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
  line-height: 100%;
}

/* line 32, ../../../../scss/email/_default.scss */
#backgroundTable {
  margin: 0;
  padding: 0;
  width: 100% !important;
  line-height: 100% !important;
}

/* line 39, ../../../../scss/email/_default.scss */
img {
  outline: none;
  text-decoration: none;
  -ms-interpolation-mode: bicubic;
  width: auto;
  max-width: 100%;
  clear: both;
  display: block;
}

/* line 49, ../../../../scss/email/_default.scss */
center {
  width: 100%;
  min-width: 580px;
}

/* line 54, ../../../../scss/email/_default.scss */
a img {
  border: none;
}

/* line 58, ../../../../scss/email/_default.scss */
p {
  margin: 0 0 0 10px;
}

/* line 62, ../../../../scss/email/_default.scss */
table {
  border-spacing: 0;
  border-collapse: collapse;
}

/* line 67, ../../../../scss/email/_default.scss */
td {
  word-break: break-word;
  -webkit-hyphens: auto;
  -moz-hyphens: auto;
  hyphens: auto;
  border-collapse: collapse !important;
}

/* line 75, ../../../../scss/email/_default.scss */
table, tr, td {
  padding: 0;
  vertical-align: top;
  text-align: left;
}

/* line 81, ../../../../scss/email/_default.scss */
hr {
  color: #d9d9d9;
  background-color: #d9d9d9;
  height: 1px;
  border: none;
}

/* Responsive Grid */
/* line 92, ../../../../scss/email/_default.scss */
table.body,
table.footer,
table.unsubscribe {
  height: 100%;
  width: 100%;
}

/* line 97, ../../../../scss/email/_default.scss */
table.container {
  width: 580px;
  margin: 0 auto;
  text-align: inherit;
}

/* line 103, ../../../../scss/email/_default.scss */
table.row {
  padding: 0px;
  width: 100%;
  position: relative;
}

/* line 109, ../../../../scss/email/_default.scss */
table.container table.row {
  display: block;
}

/* line 113, ../../../../scss/email/_default.scss */
td.wrapper {
  padding: 10px 20px 0px 0px;
  position: relative;
}

/* line 119, ../../../../scss/email/_default.scss */
table.columns,
table.column {
  margin: 0 auto;
}

/* line 124, ../../../../scss/email/_default.scss */
table.columns td,
table.column td {
  padding: 0px 0px 10px;
}

/* line 131, ../../../../scss/email/_default.scss */
table.columns td.sub-columns,
table.column td.sub-columns,
table.columns td.sub-column,
table.column td.sub-column {
  padding-right: 3.448276%;
}

/* line 136, ../../../../scss/email/_default.scss */
table.row td.last,
table.container td.last {
  padding-right: 0px;
}

/* line 140, ../../../../scss/email/_default.scss */
table.one {
  width: 30px;
}

/* line 141, ../../../../scss/email/_default.scss */
table.two {
  width: 80px;
}
HERE;

    }
}
