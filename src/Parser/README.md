# Simphotonics Node Parser

Simphotonics Node (SD) Parser contains classes that can be used to [parse data type definition files](#parse-dtd) in order to extract entities and elements, [parse HTML documents](#parse-html) to get the corresponding SD nodes, and [render SD nodes as PHP source code](#render-nodes).

## Installation

From a terminal issue the command:
```json
composer require simphotonics/node
```

Alternatively, add simphotonics/node to the list of required libraries in your composer.json file:

```json
{
    "require": {
        "simphotonics/node": ">=1.0.0"
    }
}
```

## Usage
<a id="parse-dtd"/>
### Parsing a DTD File
First, let's assume the following data type definitions are stored in the file *xhtml.dtd*:
```dtd
<!ENTITY % ContentType "CDATA">
<!ENTITY % LanguageCode "NMTOKEN">
<!ENTITY % URI "CDATA">
<!ENTITY % coreattrs
    "id          ID            #IMPLIED
    class       CDATA          #IMPLIED
    style       %StyleSheet;   #IMPLIED
    title       %Text;         #IMPLIED"
>
<!ENTITY % i18n
    "lang        %LanguageCode; #IMPLIED
    xml:lang    %LanguageCode;  #IMPLIED
    dir         (ltr|rtl)       #IMPLIED"
>
<!ELEMENT html (head, body)>
<!ATTLIST html
    %i18n;
    id          ID             #IMPLIED
    xmlns       %URI;          #FIXED 'http://www.w3.org/1999/xhtml'
>
<!ENTITY % head.misc "(script|style|meta|link|object)*">
<!-- content model is %head.misc; combined with a single
title and an optional base element in any order -->
<!ELEMENT head (%head.misc;,
    ((title, %head.misc;, (base, %head.misc;)?) |
    (base, %head.misc;, (title, %head.misc;))))
>
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
>
```
To access the entities and elements defined in *xhtml.dtd*, we use a DtdParser object:
```php
<?php
use Simphotonics\Node\Parser\DtdParser;

// A string containing the DTD can be passed directly to the constructor.
//$p = new DtdParser($dtd);

// Alternatively, the DTD can be read from a file.
$p = new DtdParser();
$p->loadDtd('xhtml.dtd');

$entities = $p->getEntities();
$elements = $p->getElements();
$attrLists = $p->getAttrLists();

// Export defined elements as DtdNodes to file 'dtdNodes.php'.
$p->exportNode('dtdNodes.php');
```
The arrays $entities, $elements, and $attrLists now contain the following entries (whitespace between entries has been changed to highlight the structure):
```php
$elements = [
  '%ContentType;' => 'CDATA',
  '%LanguageCode;' => 'NMTOKEN',
  '%URI;' => 'CDATA',
  '%coreattrs;' => 'id          ID           #IMPLIED
                  class       CDATA          #IMPLIED
                  style       %StyleSheet;   #IMPLIED
                  title       %Text;         #IMPLIED',
  '%i18n;' => 'lang        NMTOKEN #IMPLIED
               xml:lang    NMTOKEN #IMPLIED
               dir         (ltr|rtl)      #IMPLIED',
  '%head.misc;' => '(script|style|meta|link|object)*'
];

$elements = [
    'html' => '(head, body)',
    'head' => '((script|style|meta|link|object)*,
             ((title, (script|style|meta|link|object)*, (base, (script|style|meta|link|object)*)?) |
             (base, (script|style|meta|link|object)*, (title, (script|style|meta|link|object)*))))',
    'base' => 'EMPTY',
    'meta' => 'EMPTY'
];

$attrLists = [
    'html' => 'lang        NMTOKEN #IMPLIED
            xml:lang    NMTOKEN #IMPLIED
            dir         (ltr|rtl)      #IMPLIED
            id          ID             #IMPLIED
            xmlns       CDATA          #FIXED \'http://www.w3.org/1999/xhtml\'',
    'head' => 'lang        NMTOKEN #IMPLIED
            xml:lang    NMTOKEN #IMPLIED
            dir         (ltr|rtl)      #IMPLIED
            id          ID             #IMPLIED
            profile     CDATA          #IMPLIED',
    'base' => 'href        CDATA          #REQUIRED
            id          ID             #IMPLIED',
    'meta' => 'lang        NMTOKEN #IMPLIED
            xml:lang    NMTOKEN #IMPLIED
            dir         (ltr|rtl)      #IMPLIED
            id          ID             #IMPLIED
            http-equiv  CDATA          #IMPLIED
            name        CDATA          #IMPLIED
            content     CDATA          #REQUIRED
            scheme      CDATA          #IMPLIED'
];
```
Note that references to entities have been replaced by the entity value. Since DtdParser parses the DTD string only once, top to bottom, entities have to be defined before they are used.


<a id="parse-html"/>
## Parsing HTML Source Code - HtmlParser
HtmlParser is rather simplistic and should only be used to parse simple X(HT)ML documents with correct syntax. It is meant as a starting point for example to get a web page layout in terms of SD nodes. To parse complex HTML documents it is advisable to use other available HTML parsers e.g. [PHP Dom](http://php.net/manual/en/book.dom.php).

For the following example I am assuming that the file *sample-site.html* contains the following HTML source code:
```html
<!DOCTYPE html>
<html xml:lang="en" lang="en">
    <head id="head">
        <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
        <title>Sample Site - Home</title>
        <link rel="shortcut icon" href="http://www.samplesite.com/favicon.ico" />
        <!-- This is a comment -->
        <link rel="stylesheet" type="text/css" href="/style/Home.css" media="all" />
    </head>
    <body id="body">
        <div id="root">
            <div id="column1">
                <h1 class="larger has-white-shadow">HOME</h1>
                <div id="main1">
                    <p id="limitations">
                        This text is picked up by the parser!
                        <span class="emph has-white-shadow">
                            Welcome to the Sample Site
                        </span>,
                        This text is omitted by the parser!
                        <a href="http://www.sample-site.com.com">www.samplesite.com</a>.
                    </p>
                </div>
            </div>
            <div id="column2">
                <div id="main2">
                <img src="img/sample-site.jpeg" id="img1" alt="Image Sample Site" />
                </div>
            </div>
            <div class="clear">
            </div>
            <div id="footer">
            </div>
        </div>
    </body>
</html>
```
To parse the file *sample-site.html* use:
```php
<?php
use Simphotonics\Node\Parser\HtmlParser;

$p = new HtmlParser();
$p->loadHtml('sample-site.html');

// Get the 'top' nodes of the document:
$nodes = $p->getNodes();

$p->exportNodes('parsed-nodes.php');
```
The file *parsed-nodes.php* now contains PHP source code that replicates the nodes extracted while parsing *sample-site.html*. External nodes (leaves) are exported first and appended to their parent node. In this example, the top nodes required to render the HTML document are $doctype160 and $html161. Variable names are obtained by concatenating the element *kind* and an internal ID counter to ensure each name is unique.

Limitations: HtmlParser only processes pure text content (within non-empty HTML elements) encountered before the first child node. This is illustrated in the paragraph with *id="limitations"* in the example above.

```php
<?php
$doctype160 = new \Simphotonics\Node\HtmlLeaf([
  'kind' => '!DOCTYPE',
  'cont' => 'html'
]);

$meta163 = new \Simphotonics\Node\HtmlLeaf([
  'kind' => 'meta',
  'attr'=> [
    'http-equiv' => 'Content-Type',
    'content' => 'text/html',
    'charset' => 'utf-8'
  ],
]);

$title164 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'title',
  'cont' => 'Sample Site - Home'
]);

$link165 = new \Simphotonics\Node\HtmlLeaf([
  'kind' => 'link',
  'attr'=> [
    'rel' => 'shortcut',
    'href' => 'http://www.samplesite.com/favicon.ico'
  ],
]);

$comment166 = new \Simphotonics\Node\HtmlLeaf([
  'kind' => '!--',
  'cont' => 'This is a comment'
]);

$link167 = new \Simphotonics\Node\HtmlLeaf([
  'kind' => 'link',
  'attr'=> [
    'rel' => 'stylesheet',
    'type' => 'text/css',
    'href' => '/style/Home.css',
    'media' => 'all'
  ],
]);

$head162 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'head',
  'attr'=> [
    'id' => 'head'
  ],
  'child'=> [
    $meta163,
    $title164,
    $link165,
    $comment166,
    $link167
  ]
]);

$h1171 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'h1',
  'attr'=> [
    'class' => 'larger'
  ],,
  'cont' => 'HOME'
]);

$span174 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'span',
  'attr'=> [
    'class' => 'emph'
  ],,
  'cont' => 'Welcome to the Sample Site'
]);

$a175 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'a',
  'attr'=> [
    'href' => 'http://www.samplesite.com'
  ],,
  'cont' => 'www.samplesite.com'
]);

$p173 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'p'
  'child'=> [
    $span174,
    $a175
  ],
  'cont' => 'This text is picked up by the parser!'
]);

$div172 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'div',
  'attr'=> [
    'id' => 'main1'
  ],
  'child'=> [
    $p173
  ]
]);

$div170 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'div',
  'attr'=> [
    'id' => 'column1'
  ],
  'child'=> [
    $h1171,
    $div172
  ]
]);

$img178 = new \Simphotonics\Node\HtmlLeaf([
  'kind' => 'img',
  'attr'=> [
    'src' => 'img/sampleSite.jpeg',
    'id' => 'img1',
    'alt' => 'Image'
  ],
]);

$div177 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'div',
  'attr'=> [
    'id' => 'main2'
  ],
  'child'=> [
    $img178
  ]
]);

$div176 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'div',
  'attr'=> [
    'id' => 'column2'
  ],
  'child'=> [
    $div177
  ]
]);

$div179 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'div',
  'attr'=> [
    'class' => 'clear'
  ],
]);

$div180 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'div',
  'attr'=> [
    'id' => 'footer'
  ],
]);

$div169 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'div',
  'attr'=> [
    'id' => 'root'
  ],
  'child'=> [
    $div170,
    $div176,
    $div179,
    $div180
  ]
]);

$body168 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'body',
  'attr'=> [
    'id' => 'body'
  ],
  'child'=> [
    $div169
  ]
]);

$html161 = new \Simphotonics\Node\HtmlNode([
  'kind' => 'html',
  'attr'=> [
    'xml:lang' => 'en',
    'lang' => 'en'
  ],
  'child'=> [
    $head162,
    $body168
  ]
]);
```
<a id="render-nodes"/>
## Rendering Nodes As PHP Source Code - NodeRenderer
To store and reuse SD nodes it is at times useful to render them as PHP source code.
One example includes the storage of nodes obtained by parsing an HTML document (see previous section).
```php
<?php
use Simphotonics\Node\HtmlLeaf;
use Simphotonics\Node\Parser\NodeRenderer;

$myLink = new HtmlLeaf([
  'kind' => 'link',
  'attr'=> [
    'rel' => 'stylesheet',
    'type' => 'text/css',
    'href' => '/style/Home.css',
    'media' => 'all'
  ],
]);

$p = new HtmlNode([
    'kind' => 'p',
    'child'=> [$myLink]
]);

$r = new NodeRenderer();
$linkSource = $r->render($link);

// To render just the paragraph:
$paragraphSource = $r->render($p);

// To render first the link then the paragraph:
$source = $r->renderRecursive($p);
```
The variable *$linkSource* now contains a string enclosed by single quotes. All internal quotes are escaped.
```php
'$link167 = new \Simphotonics\Node\HtmlLeaf([
  \'kind\' => \'link\',
  \'attr\'=> [
    \'rel\' => \'stylesheet\',
    \'type\' => \'text/css\',
    \'href\' => \'/style/Home.css\',
    \'media\' => \'all\'
  ],
]);'
```
The variable *$source* contains the string:
```php
'$link167 = new \Simphotonics\Node\HtmlLeaf([
  \'kind\' => \'link\',
  \'attr\'=> [
    \'rel\' => \'stylesheet\',
    \'type\' => \'text/css\',
    \'href\' => \'/style/Home.css\',
    \'media\' => \'all\'
  ],
]);

$p168 = new \Simphotonics\Node\HtmlNode([
    \'kind\' => \'p\',
    \'child\'=> [$link167]
]);'
```
