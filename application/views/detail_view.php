<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h1>Detail nálezu</h1>

<?php
echo GBIF_array_row_table($nalez, "class='table table-bordered table-sm ' style='font-size: 70%;'");
?>
<h3>Nejbližší sídlo (GeoNames, JSON)</h3>
<code id="geonames" ></code>


<h3>Nejbližší nálezy v okolí</h3>

<code>-- SQL code used:
    <?= $okolni_sql ?></code>
<?php
echo $okolni;
?>