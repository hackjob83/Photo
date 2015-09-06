<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
     * part of the sanitize function
     * This part is the blacklist section where we can specify things to be taken out
     * Also purifies() and htmlspecialchars() the input
     * 
     * @param type $input
     * @return type
     */
    function cleanInput($input) {
        $search = array(
            '@<script[^>]*?>.*?</script>@si',                           // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si',                                    // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU',                            // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@',                                // Strip multi-line comments
            '/onEvent\=+/i',                                            // Strip onEvent calls
            '/\"|<|>|{|}|\[|\]*/'                                       // Strip double quotes, left and right brackets, etc
        );
        
        $blacklist_input = preg_replace($search, '', trim($input));     // perform preg_replace to strip out blacklisted items
        $output = htmlspecialchars($blacklist_input);                   // htmlspecialchars as an added precaution
        return $output;                                                 // return cleaned input
    }

    /**
     * Second half of the sanitize function
     * This one stripslashes() and escapes the input
     * 
     * @param type $input
     * @return type
     */
    function sanitize($input) {

        if (is_array($input)) {
            foreach ($input as $var => $val) {
                $output[$var] = $this->sanitize($val);
            }
        } else {
            if (get_magic_quotes_gpc()) {
                $input = stripslashes($input);
            }
            $output = cleanInput($input);
        }
        return $output;
    }