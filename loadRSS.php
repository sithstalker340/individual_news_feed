<?php
$rss_url = $_GET['rss_url'];
$feed = $_GET['feed'];
$xmlDoc = new DOMDocument();
$xmlDoc->load($rss_url);

//get and output "<item>" elements
$x = $xmlDoc->getElementsByTagName('item');

for ($i = 0; $i <= $x->length; $i++){
	$item_title = $x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
	$item_link = $x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
	$item_desc = $x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
	$item_date = $x->item($i)->getElementsByTagName('pubDate')->item(0)->childNodes->item(0)->nodeValue;
	
	echo ("<div id=" . $feed . "_" . $i . " class='" . $feed ." item'>");
	echo ("<p class='title'><a href='" . $item_link. "'>" . $item_title . "</a>" . "<span id='" . $feed . $i . "fav' class='fav_box'>Favorite</span></p>");
	echo("<p class='description'>" . $item_desc . "</p>");
	echo ("<p class='pubDate'>" . $item_date . "</p></div>");
}
