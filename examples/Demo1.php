<?php

//Top Keywords as an array

$tm = new TextMiner();

//add any number of files and or text
$tm->addFile("http://www.google.com/search?q=data+mining");
$tm->addText("Text can be added this way.");

$tm->convertToLower = TRUE; // optional

$tm->process();//should be called before accessing keywords

printa($tm->getTopNGrams(10));
echo $tm->printSummary();
        
?>