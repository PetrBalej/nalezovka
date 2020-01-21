<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Prostor extends MY_Controller {

    public function __construct() {
        parent::__construct();
        validate_query_result($this->public_sql, Array(9));
    }

    public function index($id = NULL) {




        // výběr záznamů 
        $query = $this->db->query(trim_sql_comments($this->public_sql[9]));
        $nalezy = $query->result_array();
        $data['vybrane_sql'] = $this->db->last_query();

        $data['pocet_nalezu'] = $query->num_rows();

        if (empty($nalezy)) {
            show_error("", 404, "SQL dotaz vrátil prázdný výsledek: 0 záznamů!");
        }

        // načtení knihovny geoPHP
        include_once(APPPATH . 'third_party/geoPHP/geoPHP.inc');

        // naplnění nálezů z databáze do GeoJSON formátu pro Leaflet
        $geojson = '{"type": "FeatureCollection","features": [';

        foreach ($nalezy as $row) {
            $geojson .= '{"type": "Feature", "geometry":';
            $point = geoPHP::load($row['souradniceWKT'], 'wkt');
            $geojson .= $point->out('json');
            $geojson .= ',"properties": {"speciesName": "<a href=\"/nalezovka/index.php/detail/index/' . $row['gbifID'] . '\" target=\"_blank\">' . $row['scientificName'] . ' (gbifID: ' . $row['gbifID'] . ')</a>"}},';
        }
        $geojson .= ']}';

        $data['geojson'] = $geojson;

        // získání obálky a následně centroidu ze všech souřadnic v tabulce event


        $sql_add = str_ireplace(" FROM ", ", ST_AsText(ST_Centroid(ST_Envelope(ST_GeomFromText(GROUP_CONCAT(ST_AsText(souradnice)))))) AS stred FROM ", trim_sql_comments($this->public_sql[9]));
        $query = $this->db->query($sql_add);
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
        $this->load->view('prostor_view', $data);
        $this->load->view('zaklad_konec_view', $data);
    }

}
