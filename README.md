text-miner
==========

Simple text mining code that can be used to perform basic content analysis. (Originally written years ago.)


**Example 1:** Top Keywords as a comma separated string
```php
$tm = new TextMiner();

//add any number of files and or text
$tm->addFile("http://en.wikipedia.org/wiki/Data_mining");
$tm->addFile("http://freebase.com/search?limit=30&start=0&query=data+mining");
$tm->addText("Data mining text can also be added this way.");

$tm->convertToLower = TRUE; // optional

$tm->process();//should be called before accessing keywords

printa($tm->getTopNGrams(10,false));
echo $tm->printSummary();
```
Result:
```
data mining, knowledge discovery, machine learning, mining m, mining software, mining knowledge, discovery data, data analysis, doi 10, mining data
======================
Text: data mining - wikipedia the free encyclopedia data mining from wikipedia...
Total nGrams: 7009
======================
```


**Example 2:** Top Keywords as an array
```php
$tm = new TextMiner();

//add any number of files and or text
$tm->addFile("http://www.google.com/search?q=data+mining");
$tm->addText("Text can be added this way.");

$tm->convertToLower = TRUE; // optional

$tm->process();//should be called before accessing keywords

printa($tm->getTopNGrams(10));
echo $tm->printSummary();
```
Result:
```
Array
(
    [data mining] => 46
    [8206 cached] => 10
    [cached similar] => 7
    [mining data] => 6
    [8206 ad] => 3
    [big data] => 3
    [oracle data] => 3
    [mining 8206] => 3
    [predictive analytics] => 3
    [search search] => 2
)
======================
Text: data mining - google search search images maps play youtube news gmail drive more calendar translate...
Total nGrams: 483
======================
```


**Example 3:** Top N-Grams (including lower N-grams) as an array
```php
$tm = new TextMiner();

$tm->addFile("http://en.wikipedia.org/wiki/Data_mining");
$tm->setN(3);

$tm->convertToLower = TRUE;
$tm->includeLowerNGrams = TRUE; // include all lower N-Grams

$tm->process();

printa($tm->getTopNGrams(10));
echo $tm->printSummary();
```
Result:
```
Array
(
    [data] => 398
    [mining] => 257
    [data mining] => 229
    [knowledge] => 53
    [discovery] => 49
    [information] => 48
    [analysis] => 47
    [learning] => 43
    [patterns] => 40
    [edit] => 40
)
======================
Text: data mining - wikipedia the free encyclopedia data mining from wikipedia...
Total nGrams: 19173
======================
```


**Example 4:** Stemming
```php
//Note: no instance of tm is necessary nor does process need not be called 
//STEMMING FUNCTIONALITY (requires class: Stemming.php)

//get stem counts as an array
$text = "The quick brown fox jumped over the lazy dog";
echo $text;
$words = explode(" ",$text);
$sc = TextMiner::getStemCounts($words);
printa($sc);
```
Result:
```
Array
(
    [the] => 2
    [lazi] => 1
    [dog] => 1
    [over] => 1
    [fox] => 1
    [quick] => 1
    [brown] => 1
    [jump] => 1
)
```


**Example 5:** Stemming, output in a table
```php
//Note: no instance of tm is necessary nor does process need not be called
//STEMMING FUNCTIONALITY (requires class: Stemming.php)

//output stems in a table
$tm = new TextMiner();
$tm->addFile("http://en.wikipedia.org/wiki/Data_mining");
$tm->process();//should be called before accessing keywords
TextMiner::outputStemTable($tm->getNGrams(),12);
```
Result:

STEM | WORDS
-----|--------
data min | (237)	
knowledge discoveri	| (38)	
machine learn	| (24)	
data set	| (17)	
mining softwar	| (15)	
discovery data	| (13)	
data analysi	| (12)



**Example 6:** Removing stopwords
```php
/* STATIC METHOD EXAMPLE */
//Note: no instance of tm is necessary nor does process need not be called 
//STOPWORD REMOVAL

//Stopword removal
$text = "The quick brown fox jumped over the lazy dog";
echo $text;
$words = explode(" ",strtolower($text));
printa(TextMiner::removeStopWords($words));
```
Result:
```
The quick brown fox jumped over the lazy dog
Array
(
    [0] => quick
    [1] => brown
    [2] => fox
    [3] => jumped
    [4] => lazy
    [5] => dog
)
```
