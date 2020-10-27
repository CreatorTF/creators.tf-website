<?php
// Forming the Address
$src = $_SERVER['DOCUMENT_ROOT'].'/'.$_GET["src"];
if(!file_exists($src))
    exit(http_response_code(404));

$HASH = md5($_GET["src"]).md5(($_GET["width"] ?? 0).($_GET["height"] ?? 0));
$_CACHE_PATH = $_SERVER['DOCUMENT_ROOT']."/engine/cache/".$HASH;
$mime = mime_content_type($src);

$LastModified  = filemtime($src);
$ETag = sprintf( '%s-%s', $LastModified, md5( $src ));
$Expires = 60*60*24*5;
$ExpirationDate = gmdate('D, d M Y H:i:s \G\M\T', time() + $Expires);

header("Content-Type: $mime");
header("ETag: \"".$ETag."\"");
header("Last-Modified: ".gmdate( "D, d M Y H:i:s", $LastModified )." GMT");
header("Expires: ".$ExpirationDate);
header("Cache-Control: public max-age=".$Expires);

if(!isset($_GET["width"]) && !isset($_GET["height"]))
{
    $fp = fopen($src, 'rb');
    fpassthru($fp);
} else if(file_exists($_CACHE_PATH) && (filemtime($_CACHE_PATH) + $Expires) > time())
{
    $fp = fopen($_CACHE_PATH, 'rb');
    fpassthru($fp);
} else {
    // Getting base image sizes
    $sizes = getimagesize($src);
    // Loading old image
    $mime = mime_content_type($src);
    switch ($mime) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($src);
            break;
        case 'image/png':
            $source = imagecreatefrompng($src);
            imagealphablending($source, true);
            break;
        default:
            exit(http_response_code(404));
            break;
    }
    if(!isset($_GET["width"]) && !isset($_GET["height"]))
    {
        $canvas = $source;
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
    } else {
        $oldwidth = $sizes[0];
        $oldheight = $sizes[1];

        $width = (isset($_GET["width"]) && $_GET["width"] != "")
        ? (integer) $_GET["width"]
        : (
        isset($_GET["height"]) && $_GET["height"] != ""
        ? $oldwidth * ((integer) $_GET["height"]) / $oldheight
        : $oldwidth
        );

        $height = (isset($_GET["height"]) && $_GET["height"] != "")
        ? (integer) $_GET["height"]
        : (
        isset($_GET["width"]) && $_GET["width"] != ""
        ? $oldheight * ((integer) $_GET["width"]) / $oldwidth
        : $oldheight
        );

        $width = ceil($width);
        $height = ceil($height);

        $height = min($height, $oldheight);
        $width = min($width, $oldwidth);

        // Creating Canvas
        $canvas = imagecreatetruecolor($width, $height);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $height, $sizes[0], $sizes[1]);
        imagedestroy($source);
    }
    imagepng($canvas, $_CACHE_PATH);
    imagepng($canvas);
    imagedestroy($canvas);
}
?>
