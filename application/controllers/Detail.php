<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Detail extends MY_Controller {

    public function __construct() {
        parent::__construct();
        validate_query_result($this->public_sql, Array(2,4));
    }

    public function index($gbifID = NULL) {


        // kontrola, je-li v parametru ID nálezu
        if (preg_match('/^\d+$/', $gbifID)) {
            $where = " WHERE occurrence.event_gbifID=" . $this->db->escape($gbifID);
        } else {
            $where = " WHERE 0=1 ";
            show_error("", 404, "Není vyplněn číselný parametr gbifID v URL!");
        }

        // výběr záznamů podle ID nálezu
        $query = $this->db->query($this->public_sql[2] . $where . ' LIMIT 0,1');
        $nalez = $query->row_array();
        // předání do view        
        $data['nalez'] = $nalez;

        if (empty($nalez)) {
            show_error("", 404, "SQL dotaz vrátil prázdný výsledek: 0 záznamů!");
        }

        // výběr záznamů v okolí aktuálně zobrazovaného nálezu, úprava dotazu č. 2   
        $sql_add = str_ireplace(" FROM ", ", " . trim_sql_comments($this->public_sql[4]) . "(ST_PointFromText('" . $nalez['souradniceWKT'] . "'), souradnice) AS distance FROM ", trim_sql_comments($this->public_sql[2]));
        $query = $this->db->query($sql_add . " WHERE gbifID != " . $this->db->escape($gbifID) . " ORDER BY distance LIMIT 10");

        $data['okolni_sql'] = $this->db->last_query();
        $okolni = $query->result_array();


        $this->table->set_heading(array_keys($okolni[0]));
        $template = array('table_open' => '<table class="table table-bordered table-sm table-responsive" style="font-size: 70%;">');
        $this->table->set_template($template);
        
        $data['okolni'] = $this->table->generate(GBIF_hypertext_all($okolni));



        $this->load->view('zaklad_start_view', $data);
        $this->load->view('detail_view', $data);
        $this->load->view('zaklad_konec_view', $data);
    }

}
