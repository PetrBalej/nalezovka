<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tabulka extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('pagination');
        validate_query_result($this->public_sql, Array(1,2));
    }

    public function index($id = NULL, $page = NULL, $page_number = 0) {

        // výběr taxonů do roletového menu
        $query = $this->db->query($this->public_sql[1]);

        $data['taxony'] = $query->result_array();

        // hodnota z roletového menu s taxony z formuláře, byl-li odeslán
        if (is_null($id) OR $id == "page") {
            if (is_null($this->input->post('combobox'))) { // AND !empty($this->input->post('combobox'))
                $data['combobox'] = NULL;
                $config['uri_segment'] = 4;
                $config['base_url'] = site_url($this->router->fetch_class() . '/index/page/');

                $page_number = $page;
            } else {
                redirect(site_url($this->router->fetch_class() . '/index/' . $this->input->post('combobox')), 'auto', 302);
            }
        } else {
            $data['combobox'] = $id;
            $config['uri_segment'] = 5;
            $config['base_url'] = site_url($this->router->fetch_class() . '/index/' . $data['combobox'] . '/page/');
        }

        // kontrola byl-li odeslán formulář s ID číslem taxonu (nebo je číslené ID v URL)
        if (preg_match('/^\d+$/', $data['combobox'])) {
            $where = " WHERE taxon.taxonKey=" . $this->db->escape($data['combobox']);
        } else {
            $where = "";
        }
        // celkový počet záznamů
        $total_rows = $this->db->query($this->public_sql[2] . $where);


        $config['full_tag_open'] = '<ul class="pagination mt-1">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = '&laquo;';
        $config['last_link'] = '&raquo;';

        $config['prev_link'] = '&lt';
        $config['next_link'] = '&gt';

        $config['cur_tag_open'] = '<li class="page-item"><b class="page-link">';
        $config['cur_tag_close'] = '</b></li>';

        $config['num_tag_open'] = $config['first_tag_open'] = $config['last_tag_open'] = $config['prev_tag_open'] = $config['next_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = $config['first_tag_close'] = $config['last_tag_close'] = $config['prev_tag_close'] = $config['next_tag_close'] = '</li>';


        $config['total_rows'] = $total_rows->num_rows();
        $config['per_page'] = 10;
        $config['num_links'] = 3;

        $this->pagination->initialize($config);

        $data['strankovani'] = $this->pagination->create_links();

        // výběr záznamů (případně omezený taxonem)
        $query = $this->db->query($this->public_sql[2] . $where . ' LIMIT ' . intval($page_number) . ',' . intval($config['per_page']));
        $nalezy = $query->result_array();

        $data['pocet_nalezu'] = $total_rows->num_rows();

        if (empty($nalezy)) {
            show_error("", 404, "SQL dotaz vrátil prázdný výsledek: 0 záznamů!");
        }

        $this->table->set_heading(array_keys($nalezy[0]));
        $template = array('table_open' => '<table class="table table-bordered table-sm table-responsive" style="font-size: 70%;">');
        $this->table->set_template($template);

        $data['tabulka'] = $this->table->generate($nalezy);



        $this->load->view('zaklad_start_view', $data);
        $this->load->view('tabulka_view', $data);
        $this->load->view('zaklad_konec_view', $data);
    }

}
