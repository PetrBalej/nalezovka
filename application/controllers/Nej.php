<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Nej extends MY_Controller {

    public function __construct() {
        parent::__construct();
        validate_query_result($this->public_sql, Array(2, 3, 5, 6, 7, 8));
    }


    public function index() {

        $nej_popis = array(5 => 'nejSevernější', 'nejJižnější', 'nejZápadnější', 'nejVýchodnější');

        for ($i = 5; $i <= 8; $i++) {
            $query = $this->db->query(trim_sql_comments($this->public_sql[2]) . " " . trim_sql_comments($this->public_sql[$i]));
            $nalezy[$i] = $query->row_array();
        }

        //print_r($nalezy);

        if (empty($nalezy)) {
            show_error("", 404, "SQL dotaz vrátil prázdný výsledek: 0 záznamů!");
        }

        // načtení knihovny geoPHP
        include_once(APPPATH . 'third_party/geoPHP/geoPHP.inc');

        // naplnění nálezů z databáze do GeoJSON formátu pro Leaflet
        $geojson = '{"type": "FeatureCollection","features": [';

        foreach ($nalezy as $key => $row) {
            $geojson .= '{"type": "Feature", "geometry":';
            $point = geoPHP::load($row['souradniceWKT'], 'wkt');
            $geojson .= $point->out('json');
            $geojson .= ',"properties": {"speciesName": "<b>' . $nej_popis[$key] . '</b>: <a href=\"' . site_url() . '/detail/index/' . $row['gbifID'] . '\" target=\"_blank\">' . $row['scientificName'] . ' (gbifID: ' . $row['gbifID'] . ')</a>"}},';
        }
        $geojson .= ']}';

        $data['geojson'] = $geojson;

        // získání obálky a následně centroidu ze všech souřadnic v tabulce event
        $sql_add = explode("GROUP_CONCAT", trim_sql_comments($this->public_sql[3]));
        $query = $this->db->query($sql_add[0] . "CONCAT('GEOMETRYCOLLECTION(', GROUP_CONCAT(ST_AsText(souradnice)), ')'))))) AS stred FROM event");
        $row = $query->row_array();


        if (isset($row) AND ! is_null($row['stred'])) {
            $point = geoPHP::load($row['stred'], 'wkt');
            $centX = $point->getX();
            $centY = $point->getY();
            $data['center'] = "[$centY , $centX]";
        } else {
            $data['center'] = "[0, 0]";
        }


        $this->load->view('zaklad_start_view', $data);
        $this->load->view('nej_view', $data);
        $this->load->view('zaklad_konec_view', $data);
    }

}
