# Simphotonics Dom

Simphotonics Dom can be used as a templating engine to create, edit, and output HTML nodes and documents. 

## Installation

From a terminal issue the command: 
```json
\# composer require simphotonics/dom 
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

To create a 'leaf' (an external noded without child nodes):
```php
use Simphotonics\Dom\HtmlLeaf;

// DTD
$dtd = new HtmlLeaf([
    'kind' => '!DOCTYPE',
    'cont' => 'html'
]);
```
To render the node as HTML just use:
```php
print $dtd;
```
which will output: 
```html
<!DOCTYPE html>
```

// Main document
$doc = new HtmlNode([
    'kind' => 'html',
    'attr' => [
        'xml:lang' => "en-GB",
        'lang' => "en-GB"
    ]
]);

// Web page title
$title = new HtmlTitle('PBRC');

$encoding = new HtmlLeaf([
    'kind' => 'meta',
    'attr' => [
        'http-equiv' => 'Content-Type', 
        'content' => 'text/html', 
        'charset'=>'utf-8'
    ]
]);

$icon = new htmlLeaf([
    'kind' => 'link',
    'attr' => [
        'rel' => 'shortcut icon',
        'href' => asset('favicon.ico')
    ]
]);

$css = new HtmlCssLink('/style');

// Header
$head = new HtmlNode([
  'kind' => 'head',
  'attr' => ['id' => 'head'],
  'child' => [$encoding, $title, $icon,$css]
  ]);

$body = new HtmlNode([
    'kind' => 'body', 
    'attr' => ['id' => 'body']
]);

$root = new HtmlNode([
    'kind' => 'div',
    'attr' => ['id' => 'root']
]);

$footer = new HtmlNode([
    'kind' => 'div',
    'attr' => ['id' => 'footer']
]);

// Compose emtpy template
$body->append([$root,$footer]);
$doc->append([$head,$body]);

// Append page content

// Output document
print $dtd;
print $doc;

```
