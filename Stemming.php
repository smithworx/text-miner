<?php
/***********************************************************************************
This class implements stemming algorithm by Dr. Porter (coded by Anu Joseph-
CDLR, Strathclyde University). Create an instance of the class and call the methods.
For e.g.
                $string =strtolower($string) //string to be stemmed
                $stm = new stemming();
                $stm->reset();
                for($i=0;$i<strlen($string);$i++)
                        $stm->add(substr($string,$i,1));
                $stm->stem(0);
                $stem = $stm->toString(); //stem of the string

Bug fix [1] made 18 Nov 2003, thanks to Michael Cortez, who earlier sent this
email:

-------------

Howdy,

There is a bug fix (#2) in the Java version, that does not appear to have
been applied to the php version.

From the Java Version:

    case 'o': if (ends("ion") && j >= 0 && (b[j] == 's' || b[j] == 't')) break;
                              // j >= 0 fixes Bug 2

From the PHP Version from your site:

    case "o": if ($this->ends("ion") && $this->b[$this->j] == "s" || $this->b[$this->j] == "t") break;

This causes an unknown index error for some words, for example 'rpgshop'

I duplicated the fix from the Java version, and it appears to have solved the
problem:

    case "o": if ($this->ends("ion") && $this->j >=0 && ($this->b[$this->j] == "s" || $this->b[$this->j] == "t")) break;
                                     // $this->j >=0 fixes bug [1]


Thanks,

Michael Cortez

-------------

***********************************************************************************/

class Stemming{
    var  $b;
    var  $i, $j, $k, $k0;

    function stemming(){
        $this->b = Array();
        $this->i =0;
    }
    function reset(){
        $this->i =0;
        $this->b = Array();
    }

    function add($ch){
        $this->b[$this->i++] = $ch;

    }//end of function add

    function toString(){
        $stem = join($this->b);
        return (substr($stem,0,$this->i));
    }

    /* cons(i) is true <=> b[i] is a consonant. */

    function cons($i){
        switch ($this->b[$i]){
        case "a": case "e": case "i": case "o": case "u": return false;
        case "y": return ($i==$this->k0) ? true : !($this->cons($i-1));
        default: return true;
        }
    }



            /*------------------------------------------------------------------------
        m() measures the number of consonant sequences between k0 and j. if c is
               a consonant sequence and v a vowel sequence, and <..> indicates arbitrary
               presence,

          <c><v>       gives 0
          <c>vc<v>     gives 1
          <c>vcvc<v>   gives 2
          <c>vcvcvc<v> gives 3
          ....
            ---------------------------------------------------------------------------*/

    function m(){
        $n = 0;
        $i = $this->k0;
        while(true){
            if ($i > $this->j) return $n;
            if (! $this->cons($i)) break;
            $i++;
        }
        $i++;
        while(true){
            while(true){
                if ($i > $this->j) return $n;
                if ($this->cons($i)) break;
                $i++;
            }
            $i++;
            $n++;
            while(true){
                if ($i > $this->j) return $n;
                if (! $this->cons($i)) break;
                $i++;
            }
            $i++;
        }
    }//end of function m


    /* vowelinstem() is true <=> k0,...j contains a vowel */

    function vowelinstem(){
        for ($i = $this->k0; $i <= $this->j; $i++)
            if (! ($this->cons($i)))
                return true;
        return false;
    }

    /* doublec(j) is true <=> j,(j-1) contain a double consonant. */

    function doublec($j){
        if ($j < $this->k0+1)
            return false;
        if ($this->b[$j] != $this->b[$j-1])
            return false;
        return $this->cons($j);
    }

            /*-----------------------------------------------------------------------
        cvc(i) is true <=> i-2,i-1,i has the form consonant - vowel - consonant
               and also if the second c is not w,x or y. this is used when trying to
               restore an e at the end of a short word. e.g.

          cav(e), lov(e), hop(e), crim(e), but
          snow, box, tray.

            --------------------------------------------------------------------------*/

    function  cvc($i){
        if ($i < $this->k0+2 || !$this->cons($i) || $this->cons($i-1) || !$this->cons($i-2)) return false;
        {
            $ch = $this->b[$i];
            if ($ch == "w" || $ch == "x" || $ch == "y") return false;
        }
        return true;
    }

    function ends($s){
        $l = strlen($s);
        $o = $this->k-$l+1;
        if ($o < $this->k0) return false;
        for ($i = 0; $i < $l; $i++)
            if ($this->b[$o+$i] != substr($s,$i,1)) return false;
        $this->j = $this->k-$l;
        return true;
    }//end of function ends

    /*-- setto(s) sets (j+1),...k to the characters in the string s, readjusting k. --*/

    function setto($s){

        $l = strlen($s);
        $o = $this->j+1;
        for ($i = 0; $i < $l; $i++)
            $this->b[$o+$i] = substr($s,$i,1);
        $this->k = $this->j+$l;

    }

    /*----------- r(s) is used further down. ------------------*/

    function r($s) {
        if ($this->m() > 0) $this->setto($s);
    }

            /* step1() gets rid of plurals and -ed or -ing. e.g.

           caresses  ->  caress
           ponies    ->  poni
           ties      ->  ti
           caress    ->  caress
           cats      ->  cat

           feed      ->  feed
           agreed    ->  agree
           disabled  ->  disable

           matting   ->  mat
           mating    ->  mate
           meeting   ->  meet
           milling   ->  mill
           messing   ->  mess

           meetings  ->  meet

             */

    function step1(){

        if ($this->b[$this->k] == "s"){

            if ($this->ends("sses"))
                $this->k -= 2;
            else
                if ($this->ends("ies"))
                    $this->setto("i");
                else
                    if ($this->b[$this->k-1] != "s")
                        $this->k--;
        }
        if ($this->ends("eed")) {
            if ($this->m() > 0)
                $this->k--;
        } else
            if (($this->ends("ed") || $this->ends("ing")) && $this->vowelinstem()){
                $this->k = $this->j;
                if ($this->ends("at")) $this->setto("ate"); else
                    if ($this->ends("bl")) $this->setto("ble"); else
                        if ($this->ends("iz")) $this->setto("ize"); else
                            if ($this->doublec($this->k)){
                                $this->k--;
                            {  $ch = $this->b[$this->k];
                                if ($ch == "l" || $ch == "s" || $ch == "z")
                                    $this->k++;
                            }
                            }
                            else if ($this->m() == 1 && $this->cvc($this->k))
                                $this->setto("e");

            }
    }//end of function step1


    /*------ step2() turns terminal y to i when there is another vowel in the stem. ---*/

    function step2() {

        if ($this->ends("y") && $this->vowelinstem())
            $this->b[$this->k] = "i";
    }


            /*--------------------------------------------------------------------------
        step3() maps double suffices to single ones. so -ization ( = -ize plus
               -ation) maps to -ize etc. note that the string before the suffix must give
               m() > 0.
            -----------------------------------------------------------------------------*/

    function step3() {

        switch ($this->b[$this->k-1]){

        case "a": if ($this->ends("ational")) { $this->r("ate"); break; }
            if ($this->ends("tional")) { $this->r("tion"); break; }
            break;
        case "c": if ($this->ends("enci")) { $this->r("ence"); break; }
            if ($this->ends("anci")) { $this->r("ance"); break; }
            break;
        case "e": if ($this->ends("izer")) { $this->r("ize"); break; }
            break;
        case "l": if ($this->ends("bli")) { $this->r("ble"); break; }
            if ($this->ends("alli")) { $this->r("al"); break; }
            if ($this->ends("entli")) { $this->r("ent"); break; }
            if ($this->ends("eli")) { $this->r("e"); break; }
            if ($this->ends("ousli")) { $this->r("ous"); break; }
            break;
        case "o": if ($this->ends("ization")) { $this->r("ize"); break; }
            if ($this->ends("ation")) { $this->r("ate"); break; }
            if ($this->ends("ator")) { $this->r("ate"); break; }
            break;
        case "s": if ($this->ends("alism")) { $this->r("al"); break; }
            if ($this->ends("iveness")) { $this->r("ive"); break; }
            if ($this->ends("fulness")) { $this->r("ful"); break; }
            if ($this->ends("ousness")) { $this->r("ous"); break; }
            break;
        case "t": if ($this->ends("aliti")) { $this->r("al"); break; }
            if ($this->ends("iviti")) { $this->r("ive"); break; }
            if ($this->ends("biliti")) { $this->r("ble"); break; }
            break;
        case "g": if ($this->ends("logi")) { $this->r("log"); break; }

        }//end of switch

    }//end of function step3

    /* step4() deals with -ic-, -full, -ness etc. similar strategy to step3. */

    function step4() {

        switch ($this->b[$this->k]){

        case "e": if ($this->ends("icate")) { $this->r("ic"); break; }
            if ($this->ends("ative")) { $this->r(""); break; }
            if ($this->ends("alize")) { $this->r("al"); break; }
            break;
        case "i": if ($this->ends("iciti")) { $this->r("ic"); break; }
            break;
        case "l": if ($this->ends("ical")) { $this->r("ic"); break; }
            if ($this->ends("ful")) { $this->r(""); break; }
            break;
        case "s": if ($this->ends("ness")) { $this->r(""); break; }
            break;

        }//end of switch
    }//end of function step4


    /*-- step5() takes off -ant, -ence etc., in context <c>vcvc<v>. ---*/

    function step5(){
        switch ($this->b[$this->k-1]){

        case "a": if ($this->ends("al")) break; return;
        case "c": if ($this->ends("ance")) break;
            if ($this->ends("ence")) break; return;
        case "e": if ($this->ends("er")) break; return;
        case "i": if ($this->ends("ic")) break; return;
        case "l": if ($this->ends("able")) break;
            if ($this->ends("ible")) break; return;
        case "n": if ($this->ends("ant")) break;
            if ($this->ends("ement")) break;
            if ($this->ends("ment")) break;
            /* element etc. not stripped before the m */
            if ($this->ends("ent")) break; return;
        case "o": if ($this->ends("ion") && $this->j >=0 && ($this->b[$this->j] == "s" || $this->b[$this->j] == "t")) break;
            /* $this->j >=0 fixes bug [1] */
            if ($this->ends("ou")) break; return;
            /* takes care of -ous */
        case "s": if ($this->ends("ism")) break; return;
        case "t": if ($this->ends("ate")) break;
            if ($this->ends("iti")) break; return;
        case "u": if ($this->ends("ous")) break; return;
        case "v": if ($this->ends("ive")) break; return;
        case "z": if ($this->ends("ize")) break; return;
        default: return;

        }//end of switch
        if ($this->m() > 1) $this->k = $this->j;
    }//end of step5

    /*------------------ step6() removes a final -e if m() > 1. -----------------*/

    function step6(){

        $this->j = $this->k;
        if ($this->b[$this->k] == "e"){
            $a = $this->m();
            if ($a > 1 || $a == 1 && !$this->cvc($this->k-1))
                $this->k--;
        }
        if ($this->b[$this->k] == "l" && $this->doublec($this->k) && $this->m() > 1)
            $this->k--;

    } //end of step6


    function stem($i0){

        $this->k = $this->i - 1;
        $this->k0 = $i0;
        if ($this->k > $this->k0+1){
            $this->step1();
            if($this->k >2)
                $this->step2();
            if($this->k >2)
                $this->step3();
            if($this->k >2)
                $this->step4();
            if($this->k >2)
                $this->step5();
            if($this->k >2)
                $this->step6();
        }
        $this->i = $this->k+1;
    } //end of function stem

}//end of class
?>
