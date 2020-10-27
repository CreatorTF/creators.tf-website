<?php
$dir = $_SERVER['DOCUMENT_ROOT'].'/cdn/assets/images/maps';
$needle = $_GET["map"];

$candidates = [];

if (is_dir($dir))
{
    if ($dh = opendir($dir))
    {
        while (($file = readdir($dh)) !== false)
        {
            similar_text($file, $needle, $percent);
            if($percent > 50)
            {
                $candidates[$file] = $percent;
            }
        }
        closedir($dh);
    }
}

if(count($candidates) == 0)
{
	$name = $_SERVER['DOCUMENT_ROOT'].'/cdn/assets/images/map_no_thumb.jpg';
} else {
	$file = array_search(max($candidates), $candidates);
	$name = $_SERVER['DOCUMENT_ROOT'].'/cdn/assets/images/maps/'.$file;
}

$LastModified  = filemtime($name);
$fp = fopen($name, 'rb');

$ETag = sprintf( '%s-%s', $LastModified, md5( $needle ));
$Expires = 60*60*24*5;
$ExpirationDate = gmdate('D, d M Y H:i:s \G\M\T', time() + $Expires);

header('Content-Type: image/jpeg');
header("ETag: \"".$ETag."\"");
header("Last-Modified: ".gmdate( "D, d M Y H:i:s", $LastModified )." GMT");
header("Expires: ".$ExpirationDate);
header("Cache-Control: public max-age=".$Expires);

fpassthru($fp);
?>
