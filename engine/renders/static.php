<?php
if(!defined("INCLUDED")) die("Access forbidden.");

if($_GET["page"] == "static")
{
    switch ($_GET["name"])
    {
        // System Stuff
        case 'login':
            header("Location: /engine/oauth/steam.php" . (isset($_GET["redirect"]) ? "?redirect=".$_GET["redirect"] : ""));
            break;

        case 'items':
            require_once $_SERVER['DOCUMENT_ROOT']."/engine/pages/items.php";
            break;

        case 'contracker':
            require_once $_SERVER['DOCUMENT_ROOT']."/engine/pages/contracker.php";
            break;

        case 'donate':
            require_once $_SERVER['DOCUMENT_ROOT']."/engine/pages/donate.php";
            break;

        case 'launcher':
            require_once $_SERVER['DOCUMENT_ROOT']."/engine/pages/launcher.php";
            break;

        case 'people':
            require_once $_SERVER['DOCUMENT_ROOT']."/engine/pages/people.php";
            break;

        case 'servers':
            require_once $_SERVER['DOCUMENT_ROOT']."/engine/pages/serverlist.php";
            break;

        case 'campaign':
            header("Location: /campaign/halloween");
            break;

        // Update Pages

        case 'barbocabarto':
            require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/page/tf_page_halloween2020.php";
            break;

        case 'classful':
            require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/page/tf_page_classful.php";
            break;

        case 'hazardousenvironments':
            require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/page/tf_page_hazardousenvironments.php";
            break;

        case 'selfmadesmissmas':
            require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/page/tf_page_smissmas2019.php";
            break;

        case 'promo':
            require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/page/tf_page_promo.php";
            break;

        default:
            $Page = $Core->pages->findAND("name", $_GET["name"] ?? NULL, 'surname', $_GET["surname"] ?? "");
            if(isset($Page))
            {
                if(isset($Page->redirect))
                {
                    $_GET_ARRAY = [];
                    if(explode("?",$Page->redirect) > 1)
                    {
                        foreach (explode("&", explode("?", $Page->redirect)[1]) as $a)
                        {
                            $k = explode("=", $a)[0];
                            $v = explode("=", $a)[1];
                            $_GET_ARRAY[$k] = $v;
                        }
                    }
                    $_GET_ARRAY = array_filter($_GET_ARRAY);
                    if(count($_GET_ARRAY) > 0)
                    {
                        header('Location:'.params_build_url($Page->redirect, array_merge($_GET_ARRAY, array_filter(["redirect" => $_GET["redi"]]))));
                    } else {
                        header('Location:'.$Page->redirect);
                    }
                }

                $_DATA['page_name'] = $Page->title;

                if($Page->fullscreen) define("RENDER_ONLY_CONTENT", 1);
                if(!$Page->right) define("NO_RIGHT", 1);

                if($Page->include == NULL)
                {
                    $Content = render(
                        "page",
                        [
                            'title' => $Page->title,
                            'content' => $Page->content,
                        ]
                    );
                } else {
                    $url = format($_SERVER['DOCUMENT_ROOT']."/engine/pages/%s.php",[$Page->include]);
                    define("RENDER_INCLUDE_FILE", $url);
                }
            } else {
                $Core->error = ERROR_NOT_FOUND;
            }
            break;
    }
}
?>
