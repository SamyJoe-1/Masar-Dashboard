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
