<?php

function getInitials(string $name=null): string
{
    if (empty($name)){
        return 'J';
    }
    // Split the name into parts (by spaces)
    $parts = preg_split('/\s+/', trim($name));

    // Take the first letter of the first word
    // and the first letter of the last word
    if (count($parts) >= 2) {
//        return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
        return strtoupper(substr($parts[0], 0, 1));
    }

    // If only one word, take the first two letters
    return strtoupper(substr($parts[0], 0, 2));
}

function showLess($text, $length){
    if ($text == null){
        return "-";
    }
    if (strlen($text) <= $length){
        return trim($text);
    }else{
        $get = substr(trim($text), 0, $length);
        $get_arr = explode(' ', $get);
        $array_words = array_slice($get_arr, 0, (count($get_arr) - 1));
        $result = implode(' ',$array_words);
        return "$result...";
    }
}
