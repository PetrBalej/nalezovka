<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>Mapa lokalitních nálezů (&sum; <?php echo $pocet_nalezu; ?>)</h1>

<?php
// načtení view s formulářem pro výběr taxonu
$this->load->view('autocompete_view');
?>

<div id="mapid"  class="flex-fill" style="height: 500px;"></div>