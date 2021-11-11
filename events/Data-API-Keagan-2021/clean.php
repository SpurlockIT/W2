<?php
    # [4.11.19] PKS: Added following function to clean values before being echoed.
    #regex searches for pattern and replaces instance that does not fall within the alotted characters with ''
        # ^: flag everything except:
        # \w: word character (letter, num, underscore)
        # p{M}: unicode characters with the "Mark" property (typically accents that affect the preceding character)
        # \s: whitespace
        # +$-: one or more dash @ end of string?O r more than 1 whitespace? Couldn't determine.
        # u: match with full unicode

    function clean($string) {
      //return preg_replace('/[^A-Za-z0-9\-]/','', $string);        // ORIGINAL CLEAN (old)
      //return preg_replace("/[^\w\p{M}\s+$-]/u", '', $string);     // PREVIOUS CLEAN (April 2019)
      //return strip_tags(rawurldecode(htmlspecialchars(trim(stripslashes($string))))); // PREVIOUS CLEAN (April 2019)
        $cleaned_string = strip_tags(rawurldecode(htmlspecialchars(trim(stripslashes($string))))); // CURRENT CLEAN (January 2020)
        /*
        * Below code was added to fix the errors using clean creates 
        * The htmlspecialchars function replaces certain characters with html entities, and we use the characters in our code
        * See URL below for php htmlspecialchars function reference 
        * https://www.php.net/manual/en/function.htmlspecialchars.php
        */
        $ampersand_fixed = str_replace("T&amp;E", "T&E", $cleaned_string); 
        $quotes_fixed = str_replace("&quot;", '"', $ampersand_fixed);
        return $quotes_fixed;
    }
?>