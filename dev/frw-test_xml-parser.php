<?php
/* Phoenix2
** Project Lead: Martin-Dietrich Glessgen, University of Zurich
** Code by: Samuel Läubli, University of Zurich
** Contact: samuel.laeubli@uzh.ch
** ===
** This is a playground for framework function tests.
*/

// Load the PHP framework
require_once('../settings.php');
require_once('framework/php/framework.php');

// Session
session_start();
isset($_SESSION[PH2_SESSION_KEY]) ? $ps = unserialize($_SESSION[PH2_SESSION_KEY]) : $ps = new PH2Session();

// PLAYGROUND
$xml = '<txt><maj>C\'</maj>est deniers chascun an, selonc <zw/> ce q<abr>u\'</abr>[ele] C\'est à savoir q<abr>u\'i</abr>l su<abr>n</abr>t</txt>';

$t = new XMLTextTokenizer();
echo $t->tokenize($xml);

print_r( analyseXMLFile('data/xml/text/text128.xml') );



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Phoenix2 Framework Test Playground</title>
</head>

<body>
	<?php //print_r($p); ?>
<?php
//Test XML
$test_xml =
"<?xml version=\"1.0\"?>
<testNS:RootNode xmlns:testNS=\"http://example.org\">
    <testNS:ChildElement>
        <testNS:AnotherChildElement>I'm a Another Child node.</testNS:AnotherChildElement>
    </testNS:ChildElement>
</testNS:RootNode>";

//We define our Test DOMDocument
$domDoc = new DOMDocument("1.0");
$domDoc->loadXML($test_xml);

//We use xpath to search ChildElement:
$domXPath = new DOMXPath($domDoc);
$domXPath->registerNamespace("testNS", "http://example.org");
$DOMNodeList_ChildElement = $domXPath->query("//testNS:RootNode/testNS:ChildElement");
$ChildElement = $DOMNodeList_ChildElement->item(0);

echo "Not usefull xml to load in another document:\n\n";
echo $domDoc->saveXML($ChildElement);

/**
* Function to help us clone the element to another document.
* @param DOMElement $node The node to clone.
* @param DOMDocument $doc The document where we are going to reference the elements.
* @return DOMElement The new cloned element without namespace.
*/
function cloneNode($node, $doc) {
    //Create new element with the original localName (w/o namespace)
    $nd = $doc->createElement($node->localName);

    //Clone attributes
    foreach($node->attributes as $value)
        $nd->setAttribute($value->localName, $value->value);

    //No more childs then we finish.
    if (!$node->childNodes)
        return $nd;
    //We have childs, add them
    foreach($node->childNodes as $child) {
        if ($child->nodeName=="#text") //Only needed to clone text nodes, i.e. text comments, spaces, tabs. etc.
            $nd->appendChild($doc->createTextNode($child->nodeValue));
        else
            $nd->appendChild(cloneNode($child, $doc)); //recursion to clone all children.
    }
    return $nd;
}

//New Document to reference the new node without namespaces
$domDoc2 = new DOMDocument("1.0");

//We clone this node taking out the namespace
$new_node = cloneNode($ChildElement, $domDoc2);

echo "\n\nWe can load this into a DOMDocument without problems:\n\n";
echo $domDoc2->saveXML($new_node);

$test_xml="<gl xmlns=\"http://www.rose.uzh.ch/phoenix/schema/storage\" zitf=\"testgl\">
    <an>
        <nom>testgl chNCh 001</nom>
        <d>1238.</d>
        <d0>0000/00/00</d0>
        <scripta>neuch.</scripta>
        <loc>-</loc>
        <loc0>-</loc0>
        <soc>-</soc>
        <soc0>-</soc0>
        <type>-</type>
        <r>-</r>
        <aut>-</aut>
        <disp>-</disp>
        <s>-</s>
        <b>-</b>
        <act>-</act>
        <rd>-</rd>
        <rd0>-</rd0>
        <sc>-</sc>
        <f>Parchemin jadis scellé de deux sceaux sur lacs de lin blanc, rouge et vert ; il subsiste un fragment de celui d’Amé de Montfaucon. </f>
        <l>AEN, S3 n° 21. </l>
        <ed>Publication par L. Viellard, Documents…, p. 437; J. Gauthier, Les documents…, p. 525-526, n° II.</ed>
        <ana>-</ana>
        <ec>-</ec>
        <met>-</met>
        <v>de Biavoir (<abr>XIV<sup>e</sup> siècle</abr>). P<abr>ar</abr>taige de Ve<abr>n</abr>nes <abr>et</abr> de Biaulvoir (<abr>XV<sup>e</sup> siècle</abr>).</v>
        <transcr>NN</transcr>
        <resp>Andres Kristol</resp>
    </an>
    <txt>
        <div n=\"1\">
            <token n=\"1\" type=\"occ\">Ge</token>
            <token n=\"2\" type=\"punct\">,</token>
            <token n=\"3\" type=\"occ\">Amey</token>
            <token n=\"4\" type=\"punct\">,</token>
            <token n=\"5\" type=\"occ\">sires</token>
            <token n=\"6\" type=\"occ\">de</token>
            <token n=\"7\" type=\"occ\">Mo<abr>n</abr>falco<abr>n</abr>
            </token>
            <token n=\"8\" type=\"punct\">,</token>
	</div>
    </txt>
</gl>";
$testDoc = new DOMDocument("1.0");
$testDoc->loadXML($test_xml);

$DOMNodeList_DivElement = $testDoc->getElementsByTagName("div");
$DivElement = $DOMNodeList_DivElement->item(0);
$TxtElement = $DivElement->parentNode;
echo "<br/> parent path: ";
echo $TxtElement->getNodePath();
$cloneDiv = $DivElement->cloneNode(true);
$cloneDiv->setAttribute("n","2");
echo "<br/> child paht before append: ";
echo $cloneDiv->getNodePath();
$newcloneDiv = $TxtElement->appendChild($cloneDiv);
echo "<br/> after append: ";
echo $newcloneDiv->getNodePath();

$anotherDoc = new DOMDocument("1.0");
$anotherDoc->loadXML("<gl zitf=\"another test doc\"><an><resp>me</resp></an><txt></txt></gl>");
echo "<br/> Another document <br/>";
echo $anotherDoc->saveXML($anotherDoc->documentElement);
echo "<br/>";

$copyDiv = $anotherDoc->importNode($DivElement,true);
$anotherDoc->documentElement->appendChild($copyDiv);

echo "<br/> Another document only with the division tag<br/>";
echo $anotherDoc->saveXML($anotherDoc->documentElement);
echo "<br/>HERE";
echo html_entity_decode($anotherDoc->saveXML(), ENT_NOQUOTES, 'UTF-8');
echo "<br/>HERE<br/><br/>";

$anotherDoc->documentElement->setAttribute('xmlns', 'http://www.rose.uzh.ch/phoenix/schema/storage');
echo "<br/> Another document only with the division tag with namespace set at the top<br/>";
echo $anotherDoc->saveXML($anotherDoc->documentElement);
echo "<br/>";

//$testDoc->load('data/xml/text/text128.xml');
//We clone this node taking out the namespace
//$new_docnode = cloneNode($testDoc->documentElement, $do);
echo "<br/>";
echo $testDoc->saveXML($testDoc->documentElement);
echo "<br/>";
echo 'Wrote: ' . $testDoc->save("data/xml/temp/testdoc1.xml") . ' bytes';
//echo 'Wrote: ' . $testDoc->save("data/xml/temp/testdoc.xml") . ' bytes'; // Wrote: 72 bytes
echo "<br/>";
//echo $testDoc->saveXML();
?>
</body>
</html><?php /* Save ph2session */ $ps->save(); ?>
