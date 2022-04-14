<?php


function morseToString($morseCode, $morseDictionary)
{
    $morseLetters = explode(" ", $morseCode);
    $code = '';
    foreach ($morseLetters as $morseLetter) {
        foreach ($morseDictionary as $key => $dictionaryLetter) {
            if ($dictionaryLetter == $morseLetter) {
                $code .= $key;
            }
        }
    }
    return $code;
}

function stringToMorse($string, $morseDictionary)
{
    $stringParts = str_split(strtolower($string));
    $morse = '';
    foreach ($stringParts as $stringPart) {
        if (array_key_exists($stringPart, $morseDictionary)) {
            $morse .= $morseDictionary[$stringPart] . " ";
        }
    }
    return $morse;
}
