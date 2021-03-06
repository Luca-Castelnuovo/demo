<?php

//Header
function head($title, $active_menu_item = null, $differen_menu_title = null, $extra = null)
{
    echo <<<END
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#003d14">

    <title>{$title}</title>
    <meta content="BetaSterren" name="description">
    <meta content="Betasterren, Beta Sterren, Het Baarnsch Lyceum, beta+, beta hbl" name="keywords">

    <!-- Tells Google not to provide a translation for this document -->
    <meta name="google" content="notranslate">

    <!-- Control the behavior of search engine crawling and indexing -->
    <meta name="robots" content="index,follow">
    <meta name="googlebot" content="index,follow">

    <!-- Favicons/Icons -->
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" sizes="192x192" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <link rel="mask-icon" href="/favicon.png" color="green">

    <link rel="preconnect" href="https://betaplus.ams3.cdn.digitaloceanspaces.com/" crossorigin>

    <!--Import Materialize CSS-->
    <link rel="stylesheet" href="{$GLOBALS['config']->cdn->css->main}">

    <!--Import Materialize CSS-->
    <link rel="stylesheet" href="{$GLOBALS['config']->cdn->css->materialize->library}">

    <!--Import Google Icon Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com/" crossorigin>
    <link href="{$GLOBALS['config']->cdn->css->materialize->icons}" rel="stylesheet">

    <!--Analytics-->
    <script src="https://cdn.lucacastelnuovo.nl/tracking/6"></script>

    {$extra}
</head>
END;

    $menu_title = $differen_menu_title ?? $title;

    $active_menu_item = $active_menu_item ?? null;

    if ($active_menu_item !== 10) {
        menu($active_menu_item, $menu_title);
    }
}

//Navbar
function menu($active_menu_item, $menu_title) //0 = Home, 1 = Leerlingen, 2 = Steropdrachten
{
    switch ($active_menu_item) {
        case 0:
            $home = 'class="active"';
            $students = null;
            $steropdrachten = null;
            break;

        case 1:
            $home = null;
            $students = 'class="active"';
            $steropdrachten = null;
            break;

        case 2:
            $home = null;
            $students = null;
            $steropdrachten = 'class="active"';
            break;

        default:
            $home = null;
            $students = null;
            $steropdrachten = null;
    }

    if ($_SESSION['admin']) {
        $admin_link = '<li class="divider"></li>
        <li><a href="/admin"><span class="black-text">Admin</span></a></li>';
    } else {
        $admin_link = null;
    }


    echo <<<END
<body>
    <nav>
        <div class="nav-wrapper color-primary--background">
            <a href="#" class="brand-logo">{$menu_title}</a>
            <a href="#" data-target="menu-mobile" class="right sidenav-trigger"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
                <li {$steropdrachten}><a href="/ster-opdrachten/"><i class="material-icons tooltipped" data-tooltip="Steropdrachten">folder_special</i></a></li>
                <li {$students}><a href="/leerlingen/"><i class="material-icons tooltipped" data-tooltip="Leerlingen">folder_shared</i></a></li>
                <li {$home}><a href="/general/home"><i class="material-icons tooltipped" data-tooltip="Home">assignment_ind</i></a></li>
                <li><a href="" class="dropdown-trigger" data-target="menu-desktop"><i class="material-icons">more_vert</i></a></li>
            </ul>
        </div>

        <ul id="menu-desktop" class="dropdown-content">
            <li><a href="/general/agenda"><span class="black-text">Agenda</span></a></li>
            <li><a href="/general/pdf/jaarschema" target="_blank"><span class="black-text">Schema</span></a></li>
            <li><a href="/general/settings"><span class="black-text">Settings</span></a></li>
            {$admin_link}
            <li class="divider"></li>
            <li><a href="/?logout"><span class="black-text">Logout</span></a></li>
        </ul>

        <ul id="menu-mobile" class="sidenav">
            <li><a href="/general/home">Home</a></li>
            <li><a href="/leerlingen/">Leerlingen</a></li>
            <li><a href="/ster-opdrachten/">Steropdrachten</a></li>
            <li class="divider"></li>
            <li><a href="/general/agenda">Agenda</a></li>
            <li><a href="/general/pdf/jaarschema">Jaarschema</a></li>
            <li><a href="/general/settings">Settings</a></li>
            {$admin_link}
            <li class="divider"></li>
            <li><a href="/?logout"><span class="black-text">Logout</span></a></li>
        </ul>
    </nav>
END;
}

function footer($extra = null)
{
    echo <<<END
    <!--Import Materialize JavaScript-->
    <script src="{$GLOBALS['config']->cdn->js->materialize->library}"></script>
    <!--Init Materialize Components-->
    <script src="{$GLOBALS['config']->cdn->js->materialize->init}"></script>
    {$extra}
END;
    alert_display();
    message_read();
    echo <<<END
</body>

</html>
END;
}
