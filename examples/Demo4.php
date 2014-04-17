<?php

/* STATIC METHOD DEMO */
//Note: no instance of tm is necessary nor does process need not be called
//STEMMING FUNCTIONALITY (requires class: Stemming.php)

//output stems in a table
$tm = new TextMiner();
$tm->addFile("http://en.wikipedia.org/wiki/Data_mining");
$tm->process();//should be called before accessing keywords
TextMiner::outputStemTable($tm->getNGrams(),12);
        
?>