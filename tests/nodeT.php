<?php

include('../vendor/autoload.php');

use Simphotonics\Dom\Node;
use Simphotonics\Dom\HtmlTable;
use Simphotonics\Dom\HtmlNode;
use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Dom\HtmlNavigator;



// Templates
$L = new HtmlLeaf(kind: 'a');
$B = new HtmlNode(kind: 'li', childNodes: [$L]);
// Home
$B_home = clone $B;
$B_home[0]->setAttributes(['href' => '/'])->setContent('HOME');
// Services
$B_services = clone $B;
$B_services[0]->setAttributes(['href' => '/services'])
  ->setContent('SERVICES');

$Menu = new HtmlNode(
  kind: 'ul',
  attributes: ['id' => 'mainMenu'],
);

$Menu->append([$B_home, $B_services]);
$_SERVER['REQUEST_URI'] = '/services';
$nav = new HtmlNavigator(
  kind: 'div',
  attributes: ['id' => 'nav', 'class' => 'has-shadow'],
  childNodes: [$Menu]
);

print($nav);
print("\n");
return;



$n = new Node();
$n0 = new Node();
$n1 = new Node();
$n->append([$n0, $n1]); // [$n0, $n1]





$n2 = new Node();
$n->offsetSet(0, $n2); // [$n2, $n0, $n1]

1 == 2 ? $n2 = new Node() : $n2 = $n1;

//print_r($n);


$n = new HtmlNode(
  kind: 'div',
  content: 'node content',
  attributes: ['class' => 'main']
);
$n->appendChild(new HtmlNode(kind: 'p', attributes: ['id' => 'id89' ]));

//print($n);

$l = new HtmlLeaf(kind: 'input');
print_r(HtmlLeaf::getElements());

print $l;

return;


$data = [];


for ($i = 0; $i < 16; ++$i) {
  $data[] = 'Data' . $i;



  $table = new HtmlTable(inputData: $data);
  print('<table><tr><th class="col1"><span>' .
    'Data1</span></th><th class="col2"><span>' .
    'Data2</span></th></tr><tr><td class="col1"><span>' .
    'Data3</span></td><td class="col2"><span>' .
    'Data4</span></td></tr></table>' == "$table");
}



print($table);
