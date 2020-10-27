<?php
if(!defined("INCLUDED")) die("Access forbidden.");

function render($name, $tags = [], $brackets = [])
{
    global $Core;

    // Active template name.
    $sTemplate = $Core->config->website->template;

    // Checking if template file exists.
    $sBaseUrl = $_SERVER['DOCUMENT_ROOT']."/templates/$sTemplate/$name.tpl";
    if(!file_exists($sBaseUrl)) return "";

    // Getting template content.
    $sContent = file_get_contents($_SERVER['DOCUMENT_ROOT']."/templates/$sTemplate/$name.tpl");

    // Merging [BRACKETS] array.
    $hBrackets = (array) ($Core->config->templator->brackets ?? []);
    $hBrackets = array_merge($brackets, $hBrackets);

    // Merging {tags} array.
    $hTags = (array) ($Core->config->templator->tags ?? []);
    $hTags = array_merge($tags, $hTags);

    // Merging #Localization keys.
    $hLanguageMap = (array) ($Core->config->templator->keys ?? []);
    $hLanguageMap = array_merge($Core->LanguageMap, $hLanguageMap);

    // Parsing all brackets.
    foreach($hBrackets as $bracket => $value)
    {
        $sContent = preg_replace("/\[$bracket\](.*)\[\/$bracket\]/sU", $value ? "$1" : "", $sContent);
    }

    // Parsing all tags.
    foreach($hTags as $tag => $value)
    {
        $sContent = preg_replace("/\{$tag\}/", $value, $sContent);
    }

    // Getting all localization tokens with variables in the content.
    preg_match_all('/\#(\w*)\((.*)\)/', $sContent, $hTokens);
    for($i = 0; $i < count($hTokens[0]); $i++)
    {
        $sKey = $hTokens[1][$i];
        if(isset($sKey))
        {
            $sValue = $hTokens[2][$i];
            $sReplace = "$0";
            if(isset($hLanguageMap[$sKey]))
            {
                $sReplace = format($hLanguageMap[$sKey], explode(",", $sValue));
            }

            $sContent = preg_replace("/\#".$sKey."\(".preg_quote($sValue)."\)/", $sReplace, $sContent, 1);
        }
    }

    // Getting all localization tokens without variables in the content.
    preg_match_all('/\#(\w*)/', $sContent, $hTokens);
    foreach($hTokens[1] as $sKey)
    {
        if(isset($sKey))
        {
            $sContent = preg_replace("/\#$sKey/", $hLanguageMap[$sKey] ?? "$0", $sContent, 1);
        }
    }
    return $sContent;
}
?>
