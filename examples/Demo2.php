<?php

//Top N-Grams (including lower N-grams) as an array
$tm = new TextMiner();

$tm->addFile("http://en.wikipedia.org/wiki/Data_mining");
$tm->setN(3);

$tm->convertToLower = TRUE;
$tm->includeLowerNGrams = TRUE; // include all lower N-Grams

$tm->process();

printa($tm->getTopNGrams(10));
echo $tm->printSummary();
        
?>