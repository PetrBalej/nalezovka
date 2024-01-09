<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
</div>
</main>




<!-- patička stránky -->
<footer class="footer mt-auto py-3">
    <div class="container">
        <span class="text-muted">Place sticky footer content here.</span>
        <p>Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo (ENVIRONMENT === 'development') ? 'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?>
        </p>
    </div>
</footer>






<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://getbootstrap.com/docs/4.4/dist/js/bootstrap.bundle.min.js"></script>



<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- jQuery Autocompete Combobox  https://jqueryui.com/autocomplete/#combobox -->
<script>
    $(function() {
        $.widget("custom.combobox", {
            _create: function() {
                this.wrapper = $("<span>")
                    .addClass("custom-combobox")
                    .insertAfter(this.element);

                this.element.hide();
                this._createAutocomplete();
                this._createShowAllButton();
            },

            _createAutocomplete: function() {
                var selected = this.element.children(":selected"),
                    value = selected.val() ? selected.text() : "";

                this.input = $("<input>")
                    .appendTo(this.wrapper)
                    .val(value)
                    .attr("title", "")
                    .addClass(
                        "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left"
                    )
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
                    autocompleteselect: function(event, ui) {
                        ui.item.option.selected = true;
                        this._trigger("select", event, {
                            item: ui.item.option
                        });
                    },

                    autocompletechange: "_removeIfInvalid"
                });
            },

            _createShowAllButton: function() {
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
                    .on("mousedown", function() {
                        wasOpen = input.autocomplete("widget").is(":visible");
                    })
                    .on("click", function() {
                        input.trigger("focus");

                        // Close if already visible
                        if (wasOpen) {
                            return;
                        }

                        // Pass empty string as value to search for, displaying all results
                        input.autocomplete("search", "");
                    });
            },

            _source: function(request, response) {
                var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
                response(this.element.children("option").map(function() {
                    var text = $(this).text();
                    if (this.value && (!request.term || matcher.test(text)))
                        return {
                            label: text,
                            value: text,
                            option: this
                        };
                }));
            },

            _removeIfInvalid: function(event, ui) {

                // Selected an item, nothing to do
                if (ui.item) {
                    return;
                }

                // Search for a match (case-insensitive)
                var value = this.input.val(),
                    valueLowerCase = value.toLowerCase(),
                    valid = false;
                this.element.children("option").each(function() {
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
                this._delay(function() {
                    this.input.tooltip("close").attr("title", "");
                }, 2500);
                this.input.autocomplete("instance").term = "";
            },

            _destroy: function() {
                this.wrapper.remove();
                this.element.show();
            }
        });


        $("#combobox").combobox();

    });
</script>


<?php if ($this->router->fetch_class() == "uvod" or $this->router->fetch_class() == "nej" or $this->router->fetch_class() == "prostor" or $this->router->fetch_class() == "reky" or $this->router->fetch_class() == "jezera") { ?>
<!-- leaflet JS -->
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
<script src="https://leaflet.github.io/Leaflet.markercluster/dist/leaflet.markercluster-src.js"></script>
<script>
    var zoom = 10; // výchozí zoom mapy (1-18)
    var center = <?php echo $center; ?> ; // střed mapy
    var mymap = L.map('mapid').setView(center, zoom);

    L.tileLayer(
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
                '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            id: 'mapbox/streets-v11'
        }).addTo(mymap);



    <?php $oblast = @file_get_contents('public/oblast.geojson');
    if ($oblast) {
        ?>
    var oblast =
        <?php if (json_decode($oblast)) {
            echo $oblast;
        } ?>
    ;
    L.geoJSON(oblast).addTo(mymap);

    <?php
    } ?>
    <?php $reky = file_get_contents('public/reky.geojson'); ?>
    var reky =
        <?php if (json_decode($reky)) {
        echo $reky;
    }  ?>
    ;
    L.geoJSON(reky).addTo(mymap);

    <?php $jezera = file_get_contents('public/jezera.geojson'); ?>
    var jezera =
        <?php if (json_decode($jezera)) {
        echo $jezera;
    }  ?>
    ;
    L.geoJSON(jezera).addTo(mymap);

    var data = <?php echo $geojson; ?> ;

    <?php if ($this->router->fetch_class() == "prostor") { ?>
    L.geoJson(data, {
        /*
         style: function (feature) {
         return {color: feature.properties.color};
         },
         */
        onEachFeature: function(feature, layer) {
            layer.bindPopup(feature.properties.speciesName);
        }
    }).addTo(mymap);

    <?php } ?>

    <?php if ($this->router->fetch_class() == "jezera") { ?>
    L.geoJson(data, {
        /*
         style: function (feature) {
         return {color: feature.properties.color};
         },
         */
        onEachFeature: function(feature, layer) {
            layer.bindPopup(feature.properties.speciesName);
        }
    }).addTo(mymap);

    <?php } ?>

    <?php if ($this->router->fetch_class() == "reky") { ?>
    L.geoJson(data, {
        /*
         style: function (feature) {
         return {color: feature.properties.color};
         },
         */
        onEachFeature: function(feature, layer) {
            layer.bindPopup(feature.properties.speciesName);
        }
    }).addTo(mymap);

    <?php } ?>

    <?php if ($this->router->fetch_class() == "uvod" or $this->router->fetch_class() == "nej") { ?>
    var markers = L.markerClusterGroup();

    var geoJsonLayer = L.geoJson(data, {
        onEachFeature: function(feature, layer) {
            layer.bindPopup(feature.properties.speciesName);
        }
    });
    markers.addLayer(geoJsonLayer);

    mymap.addLayer(markers);
    mymap.fitBounds(markers.getBounds());
    <?php } ?>
</script>
<?php } ?>


<?php if ($this->router->fetch_class() == "detail") { ?>
<!-- geonames -->
<script>
    $.getJSON(
        "http://api.geonames.org/findNearbyPlaceNameJSON?formatted=true&lat=<?php echo $nalez['decimalLatitude']; ?>&lng=<?php echo $nalez['decimalLongitude']; ?>&username=petrb&style=full",
        function(data) {
            // success
            $(document).ready(
                function() {
                    $('#geonames').text(JSON.stringify(data, null, '\t'));
                }
            );
        }).fail(function() {
        // fail
        $(document).ready(
            function() {
                $('#geonames').text("Nepodařilo se načíst data z api.geonames.org!");
            }
        );
    });
</script>
<?php } ?>

</body>

</html>
