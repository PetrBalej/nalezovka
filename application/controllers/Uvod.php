<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Uvod extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        validate_query_result($this->public_sql, array(1,2,3));
    }

    public function index($id = null)
    {



        // výběr taxonů do roletového menu
        $query = $this->db->query($this->public_sql[1]);

        $data['taxony'] = $query->result_array();

        // hodnota z roletového menu s taxony z formuláře, byl-li odeslán
        if (is_null($id)) {
            if (is_null($this->input->post('combobox'))) { // AND !empty($this->input->post('combobox'))
                $data['combobox'] = null;
            } else {
                redirect(site_url('uvod/index/' . $this->input->post('combobox')), 'auto', 302);
            }
        } else {
            $data['combobox'] = $id;
        }



        // kontrola byl-li odeslán formulář s ID číslem taxonu (nebo je číslené ID v URL)
        if (preg_match('/^\d+$/', $data['combobox'])) {
            $where = " WHERE taxon.taxonKey=" . $this->db->escape($data['combobox']);
        } else {
            $where = "";
        }



        // výběr záznamů (případně omezený taxonem)
        $sql_prep = explode("*", trim_sql_comments($this->public_sql[2]), 2); // optimalizace, následně výběr jen dvou sloupců místo *
        $query = $this->db->query('SELECT gbifID, scientificName' . $sql_prep[1] . $where . ' LIMIT 0,100000');
        $nalezy = $query->result_array();

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
            $geojson .= ',"properties": {"speciesName": "<a href=\"' . site_url() . '/detail/index/' . $row['gbifID'] . '\" target=\"_blank\">' . $row['scientificName'] . ' (gbifID: ' . $row['gbifID'] . ')</a>"}},' . PHP_EOL;
        }
        $geojson .= ']}';

        $data['geojson'] = $geojson;

        // získání obálky a následně centroidu ze všech souřadnic v tabulce event
        $query = $this->db->query($this->public_sql[3]);
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
        $this->load->view('uvod_view', $data);
        $this->load->view('zaklad_konec_view', $data);
    }
}
