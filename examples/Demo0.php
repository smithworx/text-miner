<?php

//Demo: Top Keywords as a comma separated string

$tm = new TextMiner();

//add any number of files and or text
$tm->addFile("http://en.wikipedia.org/wiki/Data_mining");
$tm->addFile("http://freebase.com/search?limit=30&start=0&query=data+mining");
$tm->addText("Data mining text can also be added this way.");

$tm->convertToLower = TRUE; // optional

$tm->process();//should be called before accessing keywords

printa($tm->getTopNGrams(10,false));
echo $tm->printSummary();
        
?>