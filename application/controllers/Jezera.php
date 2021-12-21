<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Jezera extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        validate_query_result($this->public_sql, array(10));
    }

    public function index($id = null)
    {




        // výběr záznamů
        $query = $this->db->query(trim_sql_comments($this->public_sql[10]));
        $nalezy = $query->result_array();
        $data['vybrane_sql'] = $this->db->last_query();

        $data['pocet_nalezu'] = $query->num_rows();

        if (empty($nalezy)) {
            show_error("", 404, "SQL query returned empty result: 0 rows!");
        }

        // načtení knihovny geoPHP
        include_once(APPPATH . 'third_party/geoPHP/geoPHP.inc');

        // naplnění nálezů z databáze do GeoJSON formátu pro Leaflet
        $geojson = '{"type": "FeatureCollection","features": [';

        foreach ($nalezy as $row) {
            $geojson .= '{"type": "Feature", "geometry":';
            $point = geoPHP::load($row['coordinatesWKT'], 'wkt');
            $geojson .= $point->out('json');
            $geojson .= ',"properties": {"speciesName": "<a href=\"' . site_url() . '/detail/index/' . $row['gbifID'] . '\" target=\"_blank\">' . $row['scientificName'] . ' (gbifID: ' . $row['gbifID'] . ')</a>"}},';
        }
        $geojson .= ']}';

        $data['geojson'] = $geojson;

        // získání obálky a následně centroidu ze všech souřadnic v tabulce event


        $sql_add = preg_replace("/\sFROM\s/m", ", ST_AsText(ST_Centroid(ST_Envelope(ST_GeomFromText( CONCAT( 'GEOMETRYCOLLECTION(', GROUP_CONCAT(ST_AsText(coordinates)), ')' ) )))) AS center FROM ", trim_sql_comments($this->public_sql[10]));
        $query = $this->db->query($sql_add);
        $row = $query->row_array();


        if (isset($row) and ! is_null($row['center'])) {
            $point = geoPHP::load($row['center'], 'wkt');
            $centX = $point->getX();
            $centY = $point->getY();
            $data['center'] = "[$centY , $centX]";
        } else {
            $data['center'] = "[0, 0]";
        }


        $this->load->view('zaklad_start_view', $data);
        $this->load->view('jezera_view', $data);
        $this->load->view('zaklad_konec_view', $data);
    }
}
