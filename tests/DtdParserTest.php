<?php

namespace Simphotonics\Dom\Tests;

use PHPUnit\Framework\TestCase;
use Simphotonics\Dom\Parser\DtdParser;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\HtmlTitle using URI's with
 * different format.
 */
class DtdParserTest extends TestCase
{
  /**
   * DTD source
   * @var  string
   */
  private static $source = ' <!ENTITY % ContentType "CDATA">
                        <!ENTITY % LanguageCode "NMTOKEN">
                        <!ENTITY % URI "CDATA">
                        <!ENTITY % coreattrs
                         "id          ID             #IMPLIED
                          class       CDATA          #IMPLIED
                          style       %StyleSheet;   #IMPLIED
                          title       %Text;         #IMPLIED"
                          >
                        <!ENTITY % i18n
                         "lang        %LanguageCode; #IMPLIED
                          xml:lang    %LanguageCode; #IMPLIED
                          dir         (ltr|rtl)      #IMPLIED"
                          >

                        <!-- attributes for common UI events
                          onclick     a pointer button was clicked
                          ondblclick  a pointer button was double clicked
                          onmousedown a pointer button was pressed down
                          onmouseup   a pointer button was released
                          onmousemove a pointer was moved onto the element
                          onmouseout  a pointer was moved away from the element
                          onkeypress  a key was pressed and released
                          onkeydown   a key was pressed down
                          onkeyup     a key was released
                        -->
                        <!ENTITY % events
                         "onclick     %Script;       #IMPLIED
                          ondblclick  %Script;       #IMPLIED
                          onmousedown %Script;       #IMPLIED
                          onmouseup   %Script;       #IMPLIED
                          onmouseover %Script;       #IMPLIED
                          onmousemove %Script;       #IMPLIED
                          onmouseout  %Script;       #IMPLIED
                          onkeypress  %Script;       #IMPLIED
                          onkeydown   %Script;       #IMPLIED
                          onkeyup     %Script;       #IMPLIED"
                          >

                        <!--================ Document Structure ==================================-->
                        <!-- the namespace URI designates the document profile -->
                        <!ELEMENT html (head, body)>
                        <!ATTLIST html
                          %i18n;
                          id          ID             #IMPLIED
                          xmlns       %URI;          #FIXED \'http://www.w3.org/1999/xhtml\'
                          >
                        <!--================ Document Head =======================================-->
                        <!ENTITY % head.misc "(script|style|meta|link|object)*">
                        <!-- content model is %head.misc; combined with a single
                             title and an optional base element in any order -->
                        <!ELEMENT head (%head.misc;,
                             ((title, %head.misc;, (base, %head.misc;)?) |
                              (base, %head.misc;, (title, %head.misc;))))>
                        <!ATTLIST head
                          %i18n;
                          id          ID             #IMPLIED
                          profile     %URI;          #IMPLIED>
                        <!ELEMENT base EMPTY>
                        <!ATTLIST base
                          href        %URI;          #REQUIRED
                          id          ID             #IMPLIED
                          >
                        <!-- generic metainformation -->
                        <!ELEMENT meta EMPTY>
                        <!ATTLIST meta
                          %i18n;
                          id          ID             #IMPLIED
                          http-equiv  CDATA          #IMPLIED
                          name        CDATA          #IMPLIED
                          content     CDATA          #REQUIRED
                          scheme      CDATA          #IMPLIED
                          >';
  /**
   * Parser instance
   * @var  Simphotonics\Dom\Parser\DtdParser
   */
  private static $p;

  public static function setUpBeforeClass(): void
  {
    self::$p = new DtdParser(self::$source);
  }

  public function testGetEntities()
  {
    $entities = self::$p->getEntities();
    $this->assertEquals(
      '(script|style|meta|link|object)*',
      $entities['%head.misc;']
    );
  }

  public function testGetElements()
  {
    $elements = self::$p->getElements();
    $this->assertEquals(
      '((script|style|meta|link|object)*,
                             ((title, (script|style|meta|link|object)*, (base, (script|style|meta|link|object)*)?) |
                              (base, (script|style|meta|link|object)*, (title, (script|style|meta|link|object)*))))',
      $elements['head']
    );
    $this->assertEquals(
      '(head, body)',
      $elements['html']
    );
  }

  public function testGetEmptyElements()
  {
    $emptyElements = self::$p->getEmptyElements();
    $expectedArr = [
      'base' => 'empty',
      'meta' => 'empty'
    ];
    $this->assertEquals(
      $expectedArr,
      $emptyElements
    );
  }

  public function testGetElementNodes()
  {
    $nodes = self::$p->getElementNodes();

    $this->assertEquals(
      "#FIXED 'http://www.w3.org/1999/xhtml'",
      $nodes['html']->attributes()['xmlns'][1]
    );

    $this->assertEquals(
      ['CDATA', "#FIXED 'http://www.w3.org/1999/xhtml'"],
      $nodes['html']->attributes()['xmlns']
    );
  }
}
