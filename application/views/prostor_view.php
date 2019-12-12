<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>Navrhnutý prostorový SQL dotaz (&sum; <?php echo $pocet_nalezu; ?>)</h1>


<code>-- použitý SQL kód:
    <?= $vybrane_sql ?></code>
<div id="mapid"  class="flex-fill" style="height: 500px;"></div>