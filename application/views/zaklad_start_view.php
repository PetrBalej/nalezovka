<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="en" class="h-100">

<head>
    <!-- bootstrap template:  https://getbootstrap.com/docs/4.4/examples/sticky-footer/ -->
    <meta name="robots" content="noindex">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Nálezovka</title>

    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/4.4/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://jqueryui.com/resources/demos/style.css">


    <style>
        /* jquery UI */
        .custom-combobox {
            position: relative;
            display: inline-block;
        }

        .custom-combobox-toggle {
            position: absolute;
            top: 0;
            bottom: 0;
            margin-left: -1px;
            padding: 0;
        }

        .custom-combobox-input {
            margin: 0;
            padding: 5px 10px;
            width: 400px;
        }

        #ui-id-1 {
            max-height: 500px;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            /* překrytí leaflet mapy a mapových zdrojů */
            width: 400px;
        }

        /* bootstrap */
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        /* leaflet */
        #mapid {
            height: 180px;
        }

        /* custom */
        pre {
            margin: 20px;
            padding: 10px;
            background-color: #F0F0F0;
            border-radius: 10px;
            border: 1px dotted gray;
            font-size: 70%;
            white-space: pre-wrap;
        }

        code {
            font-family: Consolas, Monaco, Courier New, Courier, monospace;
            font-size: 12px;
            background-color: #f9f9f9;
            border: 1px solid #D0D0D0;
            color: #002166;
            display: block;
            margin: 14px 0 14px 0;
            padding: 12px 10px 12px 10px;
            white-space: pre-wrap;
        }
    </style>


    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/4.4/examples/sticky-footer-navbar/sticky-footer-navbar.css"
        rel="stylesheet">

    <!-- leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css">
    <link rel="stylesheet" href="https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.Default.css" />
</head>


<body class="d-flex flex-column h-100">

    <header>
        <!-- hlavička - horní menu stránky -->
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <a class="navbar-brand"
                href="<?php echo site_url(); ?>">Nálezovka</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link"
                            href="<?php echo site_url(); ?>/tabulka">Tabulka<span
                                class="sr-only">(aktivní)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="<?php echo site_url(); ?>/nej">Nej-
                            N/S/E/W</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="<?php echo site_url(); ?>/prostor">Prostor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="<?php echo site_url(); ?>/jezera">Jezera</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="<?php echo site_url(); ?>/reky">Řeky</a>
                    </li>


                </ul>
            </div>
        </nav>
    </header>



    <!-- hlavní obsah stránky -->
    <main role="main" class="flex-shrink-0">
        <div class="container">