<?php

/* STATIC METHOD DEMO */
//Note: no instance of tm is necessary nor does process need not be called 
//STEMMING FUNCTIONALITY (requires class: Stemming.php)

//get stem counts as an array
$text = "The quick brown fox jumped over the lazy dog";
echo $text;
$words = explode(" ",$text);
$sc = TextMiner::getStemCounts($words);
printa($sc);
        
?>