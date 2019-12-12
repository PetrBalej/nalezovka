<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<form method="post" accept-charset="utf-8" action="<?= site_url($this->router->fetch_class() . '/index') ?>" class="mb-1">


    <div class="ui-widget" style="display: inline-block; margin-right: 50px;">
        <label>Vyberte taxon: </label>
        <select id="combobox" name="combobox">
            <option value="">nevybráno</option>
            <?php
            foreach ($taxony as $row) {
                if ($row['taxonKey'] == $combobox) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                echo "<option value='" . $row['taxonKey'] . "' $selected>" . $row['scientificName'] . "</option>";
            }
            ?>
        </select>
    </div>

    <!--<button id="toggle">Show underlying select</button>-->

    <input type="submit" value="Filtrovat" class="btn btn-primary" > 
</form>