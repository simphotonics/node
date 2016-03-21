# Simphotonics Dom

Simphotonics Dom (SD) nodes can be used to to create, edit, search, and output HTML nodes. The library contains classes that help with the creation of [navigators](#navigator), input elements, and HTML [tables](#table).

SD nodes make composing HTML documents easier by removing the need for structured text and enabling reuse of HTML nodes. Most web sites use a fixed page layout that is then filled with the page content: text, anchors, images, etc. The section [web page template](#web-page-template) shows how to use SD nodes to create a simple two column empty web page prototype.

## Installation

From a terminal issue the command: 
```json
composer require simphotonics/dom 
```

Alternatively, add simphotonics/dom to the list of required libraries in your composer.json file:

```json
{
    "require": {
        "simphotonics/dom": ">=1.0.0"
    }
}
```

## Usage
To create SD nodes, an array with optional entries *kind, attr, cont, and child* is passed to the constructor:
```php
<?php
use Simphotonics\Dom\HtmlNode;
use Simphotonics\Dom\HtmlLeaf;

$img = new HtmlLeaf([
    'kind' => 'img',    
    // Element attributes are specified in array format!  
    'attr' => [
        'id' => 'logo',
        'src' => 'assets/images/logo.jpg',
        'alt' => 'Logo'
    ]
]);

// All input array entries are optional. If no element kind is specified it defaults to div. 
$div = new HtmlNode();

// Attributes and content can be added later.
$div->setAttr(['id' => 'demo-div'])->setCont('Text within the div element.');

$p = new HtmlNode([
    'kind' => 'p',
    'attr' => [
        'class' => 'demo-paragraph'        
    ],
    // The (string) content of an element.
    'cont' => 'This is the paragraph text.',
    // An array of child nodes.
    'child' => [$img,$div]
]);
```
Note that the element **kind** refers to the HTML element *tag name*. The HTML paragraph in the example above is of kind *p*, whereas the HTML image is of kind *img*. To render the nodes in the example above, we use:
```php
<?php
print $p;
```
The statement above returns the following string (whitespace has been added to highlight the structure of the html source code): 
```html
<p class="demo-paragraph">This is the paragraph text.
    <img id="logo" src="assets/images/logo.jpg" alt="Logo"/>
    <div id="demo-div">Text within the div element.</div>
</p>
```
<a id="web-page-template" />
##Web Page Template 
The following example shows how to quickly generate a simple web page layout using SD nodes. It can be used as a prototype empty HTML document that is later filled with actual web page content. 
```php
use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Dom\HtmlNode;
use Simphotonics\Dom\HtmlCssLink;
use Simphotonics\Dom\HtmlTitle;   

// DTD
$dtd = new HtmlLeaf([
    'kind' => '!DOCTYPE',
    'cont' => 'html'
]);

// HTML document
$doc = new HtmlNode([
    'kind' => 'html',
    'attr' => [
        'xml:lang' => "en-GB",
        'lang' => "en-GB"
    ]
]);

// Web page title
// The title is set dynamically depending on the current URI.
// Example: www.samplesite.com/about-us => Title: My Site - About Us
$title = new HtmlTitle('My Site');

$encoding = new HtmlLeaf([
    'kind' => 'meta',
    'attr' => [
        'http-equiv' => 'Content-Type', 
        'content' => 'text/html', 
        'charset'=>'utf-8'
    ]
]);

$icon = new HtmlLeaf([
    'kind' => 'link',
    'attr' => [
        'rel' => 'shortcut icon',
        'href' => asset('favicon.ico')
    ]
]);

// The input path tells the class HtmlCssLink that style files are located in '/style'. 
// If the current URI is www.samplesite.com/about-us, 
//    the style file is assumed to be /style/AboutUs.css.
$css = new HtmlCssLink('/style');

// Head
$head = new HtmlNode([
  'kind' => 'head',
  'attr' => ['id' => 'head'],
  'child' => [$encoding, $title, $icon, $css]
  ]);

$body = new HtmlNode([
    'kind' => 'body', 
    'attr' => ['id' => 'body']
]);

// We are using a two column layout. 
$col1 = new HtmlNode([
    'kind' => 'div',
    'attr' => ['id' => 'col1']
]);

// This demonstrates cloning of nodes.
$col2 = clone $col1;
$col2->setAttr(['id' => 'col2']);

$footer = new HtmlNode([
    'kind' => 'div',
    'attr' => ['id' => 'footer']
]);

// Compose emtpy template
$body->append([$col1,$col2,$footer]);
$doc->append([$head,$body]);
```
Let's assume that the PHP source code above was saved to the file *layouts/emptyDocument.php*.
We now use the empty document layout to create the page AboutUs.php. If you are using a framework this could be the *view* loaded when routing to */about-us*.   
```php
<?php
// Load empty document
require 'layouts/emptyDocument.php';

// Compose content
$info = new HtmlLeaf([
    'kind' => 'p',
    'cont' => 'Information about www.samplesite.com.'
]);

$imgAboutUs = new HtmlLeaf([
    'kind' => 'img',    
    'attr' => [
        'id' => 'img-about-us',
        'src' => 'assets/images/aboutUs.jpg',
        'alt' => 'About Us'
    ]
]);

// Add content to the empty document

// Add the info paragraph to column 1. 
$col1->appendChild($info);

// Note that HtmlNode implements the array access interface. 
// $col1 can also be accessed using array notation.
// Example: $doc[0] === $head, $doc[1] === $body.
//          $doc[1][0] === $col1, $doc[1][1] === $col2.

// The image is added to column 2. 
$col2->appendChild($imgAboutUs);

// Render html document
print $dtd;
print $doc;
```

<a id="navigator"/>
## Web Page Navigator - HtmlNavigator
 The class HtmlNavigator can be  used to create a PHP/CSS driven web page navigator. The class searches all descendant nodes for anchors pointing to the current uri.  The parent node of the anchor is then added to the CSS class 'here' (to enable styling). 

A web page navigator typically consists of an unordered list where the list items are the navigator buttons and contain the navigator anchors (links). The following example illustrates how to create a simple navigator with just two entries - Home and Services. 
```php
<?php
// Anchor template
$a = new HtmlLeaf([
'kind' => 'a'
]);

// Navigator button template
$b = new HtmlNode([
'kind' => 'li',
'child' => [$a]
]);

// Create entry for home
$b_home = clone $b;
$b_home[0]->setAttr(['href' => '/'])->setCont('HOME');

// Services
$b_services = clone $b;
$b_services[0]->setAttr(['href' => '/services'])->setCont('SERVICES');

$menu = new HtmlNode([
'kind' => 'ul',
'attr' => ['id' => 'mainMenu'],
'child'=> [$b_home, $b_services]
]);

$nav =  new HtmlNavigator([
'kind' => 'div',
'attr' => ['id' => 'nav','class' => 'has-shadow'],
'child' => [$menu]
]);
``` 
Let's assume that the current relative uri is */services*, then rendering $nav from within PHP yields the string:
```html
<div id="nav" class="has-shadow">
    <ul id="mainMenu">
        <li>
            <a href="/">HOME</a>
        </li>
        <li class="here">
            <a href="/services">SERVICES</a>
        </li>
    </ul>
</div>
```
For completeness I include a rudimentary CSS file showing the basic styling of the navigator components. Notice
the styling of the class *li.here*, that will highlight the navigator button pointing to the currently shown page. 
```css
#nav {
  position: relative;
  margin: auto;
  margin-bottom: 1em;
}

#mainMenu {
  top: 0;
  list-style: none;
  width: 100%;
  height: 100%;
}

#mainMenu li.here {
  background-color: #133557;
  border-left-color: #234567;
  border-right-color: #002142;
}
```

<a id="table"/>
## Html Tables Made Easy - HtmlTable 
The class HtmlTable can be used to create and manipulate HTML tables. The usage is demonstrated below:
```php
<?php
use Simphotonics\Dom\HtmlTable;

\\ Table data
for ($i=1; $i < 9; $i++) {
            $data[] = 'Data'.$i;
}
\\ Construct table
$table = new HtmlTable(
    $data,   // Input data (could also be SD nodes)
    3,       // Set table layout to 3 columns                    
    HtmlTable::SET_TABLE_HEADERS, // Enable table headers      
    2,       // Each 2nd row will have the style attribute class="alt"
    1        // Omit styling of the first row.
);

$print $table;
```
The code above will render the following html table: 
<table>
    <tr>
        <th class="col1"><span>Data1</span></th>
        <th class="col2"><span>Data2</span></th>
        <th class="col3"><span>Data3</span></th>
    </tr>
    <tr class="alt">
        <td class="col1"><span>Data4</span></td>
        <td class="col2"><span>Data5</span></td>
        <td class="col3"><span>Data6</span></td>
    </tr>
    <tr>
        <td class="col1"><span>Data7</span></td>
        <td class="col2"><span>Data8</span></td>
        <td class="col3"><span>Data9</span></td>
    </tr>
</table>
Alternative rows can be styled using the CSS class *alt*. 
Table input other than SD nodes are wrapped in an SD node of kind *span*. The HTML source code is shown below: 
```html
<table>
    <tr>
        <th class="col1"><span>Data1</span></th>
        <th class="col2"><span>Data2</span></th>
        <th class="col3"><span>Data3</span></th>
    </tr>
    <tr class="alt">
        <td class="col1"><span>Data4</span></td>
        <td class="col2"><span>Data5</span></td>
        <td class="col3"><span>Data6</span></td>
    </tr>
    <tr>
        <td class="col1"><span>Data7</span></td>
        <td class="col2"><span>Data8</span></td>
        <td class="col3"><span>Data9</span></td>
    </tr>
</table>
```
The class HtmlTable contains methods that allow changing the table layout: 
```php
<?php
// Set number of columns
$table->setNumberOfColumns(4);

// Append data to last row
$table->appendToLastRow(['Data10','Data11']);

// Append new row
$table->appendRow(['Data12','Data13']);

// Delete individual row (note count starts from 0).
$table->deleteRow(1);

// Delete column (count starts from 0).
$table->deleteColumn(2);
``` 