<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class My_Controller extends CI_Controller {

    public function __construct() {

        parent::__construct();

        $map = directory_map(FCPATH . 'public', 1);

        $this->public_sql = Array();

        foreach ($map as $key => $value) {

            if (preg_match('/^dotaz([0-9]{2})\.sql$/', $value, $matches)) {

                $this->public_sql[intval($matches[1])] = read_file(FCPATH . 'public/' . $value);
            }
        }

        if (empty($this->public_sql)) {
            show_error("Chybí soubory dotaz01.sql, dotaz02.sql atd. v adresáři public!", 404, "V adresáři public neexistují soubory s SQL dotazy!");
        }

        
    }

}
