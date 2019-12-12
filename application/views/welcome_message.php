<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="en" class="h-100">
    <head>
        <!-- bootstrap template:  https://getbootstrap.com/docs/4.4/examples/sticky-footer/ -->

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Nálezovka - test: jQuery, leaflet, bootstrap, geophp, geonames v jedné stránce</title>

        <!-- Bootstrap core CSS -->
        <link href="https://getbootstrap.com/docs/4.4/dist/css/bootstrap.min.css" rel="stylesheet" >

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
            }
            #ui-id-1 { 
                max-height: 500px; 
                overflow-y: auto;
                overflow-x: hidden;
                z-index: 1000; /* překrytí leaflet mapy a mapových zdrojů */
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
            #mapid { height: 180px; }

            /* custom */
            pre {margin: 20px; padding: 10px; background-color: #F0F0F0; border-radius: 10px; border: 1px dotted gray; font-size: 70%;}
        </style>


        <!-- Custom styles for this template -->
        <link href="https://getbootstrap.com/docs/4.4/examples/sticky-footer-navbar/sticky-footer-navbar.css" rel="stylesheet">

        <!-- leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css">

    </head>


    <body class="d-flex flex-column h-100">

        <header>
            <!-- hlavička - horní menu stránky -->
            <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
                <a class="navbar-brand" href="./">Nálezovka</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="#">Odkaz menu1<span class="sr-only">(aktivní)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Odkaz menu2</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Odkaz menu2</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>



        <!-- hlavní obsah stránky -->
        <main role="main" class="flex-shrink-0">
            <div class="container">
                <h1 class="mt-5">Sticky footer with fixed navbar</h1>
                <p class="lead">Pin a footer to the bottom of the viewport in desktop browsers with this custom HTML and CSS. A fixed navbar has been added with <code>padding-top: 60px;</code> on the <code>main &gt; .container</code>.</p>



                <!-- jQuery autocompete -->
                <h4>Test jQuery Autocompete:</h4>
                <div class="ui-widget">
                    <label>Your preferred programming language: </label>
                    <select id="combobox">
                        <option value="">Select one...</option>
                        <option value="ActionScript">ActionScript</option>
                        <option value="AppleScript">AppleScript</option>
                        <option value="Asp">Asp</option>
                        <option value="BASIC">BASIC</option>
                        <option value="C">C</option>
                        <option value="C++">C++</option>
                        <option value="Clojure">Clojure</option>
                        <option value="COBOL">COBOL</option>
                        <option value="ColdFusion">ColdFusion</option>
                        <option value="Erlang">Erlang</option>
                        <option value="Fortran">Fortran</option>
                        <option value="Groovy">Groovy</option>
                        <option value="Haskell">Haskell</option>
                        <option value="Java">Java</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="Lisp">Lisp</option>
                        <option value="Perl">Perl</option>
                        <option value="PHP">PHP</option>
                        <option value="Python">Python</option>
                        <option value="Ruby">Ruby</option>
                        <option value="Scala">Scala</option>
                        <option value="Scheme">Scheme</option>
                    </select>
                </div>

                <hr>

                <!-- leaflet - mapové okno -->
                <h4>Test Leaflet:</h4>
                <div id="mapid"  class="flex-fill" style="height: 500px;"></div>

                <hr>

                <!-- test geoPHP -->
                <h4>Test geoPHP:</h4>
                <pre>
                    <?php
                    print_r($geoPHP);
                    ?>
                </pre>
                <!-- test geonames -->
                <h4>Test geoNames:</h4>
                <pre id="geonames" style="word-wrap: break-word; word-break: break-all;"></pre>
                
 <!-- + nadmořská výška? -->
 
            </div>
        </main>




        <!-- patička stránky -->
        <footer class="footer mt-auto py-3">
            <div class="container">
                <span class="text-muted">Place sticky footer content here.</span>
                <p>Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo (ENVIRONMENT === 'development') ? 'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
            </div>
        </footer>






        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" ></script>
        <script src="https://getbootstrap.com/docs/4.4/dist/js/bootstrap.bundle.min.js" ></script>



        <!-- jQuery UI -->
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <!-- jQuery Autocompete Combobox  https://jqueryui.com/autocomplete/#combobox -->     
        <script>
            $(function () {
                $.widget("custom.combobox", {
                    _create: function () {
                        this.wrapper = $("<span>")
                                .addClass("custom-combobox")
                                .insertAfter(this.element);

                        this.element.hide();
                        this._createAutocomplete();
                        this._createShowAllButton();
                    },

                    _createAutocomplete: function () {
                        var selected = this.element.children(":selected"),
                                value = selected.val() ? selected.text() : "";

                        this.input = $("<input>")
                                .appendTo(this.wrapper)
                                .val(value)
                                .attr("title", "")
                                .addClass("custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left")
                                .autocomplete({
                                    delay: 0,
                                    minLength: 0,
                                    source: $.proxy(this, "_source")
                                })
                                .tooltip({
                                    classes: {
                                        "ui-tooltip": "ui-state-highlight"
                                    }
                                });

                        this._on(this.input, {
                            autocompleteselect: function (event, ui) {
                                ui.item.option.selected = true;
                                this._trigger("select", event, {
                                    item: ui.item.option
                                });
                            },

                            autocompletechange: "_removeIfInvalid"
                        });
                    },

                    _createShowAllButton: function () {
                        var input = this.input,
                                wasOpen = false;

                        $("<a>")
                                .attr("tabIndex", -1)
                                .attr("title", "Show All Items")
                                .tooltip()
                                .appendTo(this.wrapper)
                                .button({
                                    icons: {
                                        primary: "ui-icon-triangle-1-s"
                                    },
                                    text: false
                                })
                                .removeClass("ui-corner-all")
                                .addClass("custom-combobox-toggle ui-corner-right")
                                .on("mousedown", function () {
                                    wasOpen = input.autocomplete("widget").is(":visible");
                                })
                                .on("click", function () {
                                    input.trigger("focus");

                                    // Close if already visible
                                    if (wasOpen) {
                                        return;
                                    }

                                    // Pass empty string as value to search for, displaying all results
                                    input.autocomplete("search", "");
                                });
                    },

                    _source: function (request, response) {
                        var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
                        response(this.element.children("option").map(function () {
                            var text = $(this).text();
                            if (this.value && (!request.term || matcher.test(text)))
                                return {
                                    label: text,
                                    value: text,
                                    option: this
                                };
                        }));
                    },

                    _removeIfInvalid: function (event, ui) {

                        // Selected an item, nothing to do
                        if (ui.item) {
                            return;
                        }

                        // Search for a match (case-insensitive)
                        var value = this.input.val(),
                                valueLowerCase = value.toLowerCase(),
                                valid = false;
                        this.element.children("option").each(function () {
                            if ($(this).text().toLowerCase() === valueLowerCase) {
                                this.selected = valid = true;
                                return false;
                            }
                        });

                        // Found a match, nothing to do
                        if (valid) {
                            return;
                        }

                        // Remove invalid value
                        this.input
                                .val("")
                                .attr("title", value + " didn't match any item")
                                .tooltip("open");
                        this.element.val("");
                        this._delay(function () {
                            this.input.tooltip("close").attr("title", "");
                        }, 2500);
                        this.input.autocomplete("instance").term = "";
                    },

                    _destroy: function () {
                        this.wrapper.remove();
                        this.element.show();
                    }
                });

                $("#combobox").combobox();

            });
        </script>    



        <!-- leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
        <script>

            var mymap = L.map('mapid').setView([51.505, -0.09], 13);

            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
                maxZoom: 18,
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
                        '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
                        'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                id: 'mapbox/streets-v11'
            }).addTo(mymap);

            L.marker([51.5, -0.09]).addTo(mymap)
                    .bindPopup("<b>Hello world!</b><br />I am a popup.").openPopup();

            L.circle([51.508, -0.11], 500, {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5
            }).addTo(mymap).bindPopup("I am a circle.");

            L.polygon([
                [51.509, -0.08],
                [51.503, -0.06],
                [51.51, -0.047]
            ]).addTo(mymap).bindPopup("I am a polygon.");


            var popup = L.popup();

            function onMapClick(e) {
                popup
                        .setLatLng(e.latlng)
                        .setContent("You clicked the map at " + e.latlng.toString())
                        .openOn(mymap);
            }

            mymap.on('click', onMapClick);

        </script>

        <!-- geonames -->
        <script>
            $.getJSON("http://api.geonames.org/findNearbyPlaceNameJSON?formatted=true&lat=50.0&lng=18.0&username=petrb&style=full", function (data) {
                // success
                $(document).ready(
                        function () {
                            $('#geonames').text(JSON.stringify(data, null, '\t'));
                        }
                );
            }).fail(function () {
                // fail
                $(document).ready(
                        function () {
                            $('#geonames').text("Nepodařilo se načíst data z api.geonames.org!");
                        }
                );
            });
        </script>     


    </body>
</html>