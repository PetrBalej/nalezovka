<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Import extends MY_Controller
{
    public function index()
    {
        $tables = array("event", "occurrence", "taxon");
        $tables_col_types = array();
        // příprava k získání datových typů sloupců tabulek z DB
        foreach ($tables as $t) {
            $query = $this->db->query('DESCRIBE '.$t);
            $ts = $query->result_array();
            $col_type = '/([A-z]+)(\(([0-9\,]+)\))?/i';
            foreach ($ts as $item) {
                preg_match($col_type, $item['Type'], $m);
                $m[3] = $m[3] ?? null;
                $tables_col_types[$t][$item['Field']] = array($m[1], $m[3]);
            }
        }
       
        // exportovaný Simple soubor z GBIFu uložený ve složce "public" a pojmenovaný "gbif-sumava.csv"
        $gbif_simple = FCPATH . 'public/gbif-sumava.csv';



        // rozparsujeme si Simple export z GBIFu
        $csv_parsed = assoc_getcsv($gbif_simple);

        $tk = []; // pomocná proměnná pro seznam unikátních taxonů

        foreach ($csv_parsed as $row) {

            // nutno předem ošetřit uvozovky na vstupu zpětným lomítkem, aby nedošlo k jejich interpretaci (a rozbití syntaxe SQL) při vkládání do databáze
            // (správným postupem při vkládání hodnot do DB by bylo použít přímo příslušnou funkci k tomu určenou, viz https://phpfashion.com/escapovani-definitivni-prirucka#toc-sql-a-databaze)
            foreach ($row as $key => $value) {
                $row[$key] = addslashes($value);
            }
            
            foreach ($tables as $t) {
                if ($t == "event") {
                    echo "INSERT INTO " . $t . " (`" . implode("`,`", array_keys($tables_col_types[$t])) . "`) ";
                    // naplnění tabulky event
                    echo "VALUES ('" . GBIF_prepare('gbifID', $row, $tables_col_types[$t])  . "', '" .GBIF_prepare('datasetKey', $row, $tables_col_types[$t]) . "', '" .GBIF_prepare('countryCode', $row, $tables_col_types[$t]) . "', '" .GBIF_prepare('locality', $row, $tables_col_types[$t]) . "', '" .GBIF_prepare('stateProvince', $row, $tables_col_types[$t]) . "', '" .GBIF_prepare('decimalLatitude', $row, $tables_col_types[$t]) . "', '" .GBIF_prepare('decimalLongitude', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('elevation', $row, $tables_col_types[$t]) . "', '" .GBIF_prepare('eventDate', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('day', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('month', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('year', $row, $tables_col_types[$t]) . "', '" .GBIF_prepare('institutionCode', $row, $tables_col_types[$t]) . "', ST_PointFromText('POINT(" .GBIF_prepare('decimalLongitude', $row, $tables_col_types[$t]) . " " .GBIF_prepare('decimalLatitude', $row, $tables_col_types[$t]) . ")'));" . PHP_EOL;
                }

                if ($t == "occurrence") {
                    echo "INSERT INTO " . $t . " (`" . implode("`,`", array_keys($tables_col_types[$t])) . "`) ";
                    // naplnění tabulky event
                    echo "VALUES (NULL, '" . GBIF_prepare('gbifID', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('occurrenceID', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('basisOfRecord', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('license', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('rightsHolder', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('recordedBy', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('issue', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('taxonKey', $row, $tables_col_types[$t]) . "');" . PHP_EOL;
                }

                if ($t == "taxon") {
                    // naplnění tabulky taxon (normalizace)
                    // potřebujeme vypsat unikátní hodnoty taxonů (jen jedenkrát, vložení uníkátního klíče stejné hodnoty by databáze odmítla)
                    // zapíšeme si tedy po každém vypsání hodnoty 'taxonKey' do pole a budeme ověřovat v dalším cyklu jestli už nebylo zapsáno/uloženo
                    // order je v MariaDB klíčové slovo a musí být proto escapováno přes uvozovku zvanou backquote (backtick) na: `order`
                    if (!in_array($row['taxonKey'], $tk)) { // neexistuje-li zatím v poli $taxonKey naše hodnota 'taxonKey', můžeme vložit do tabulky
                        echo "INSERT INTO " . $t . " (`" . implode("`,`", array_keys($tables_col_types[$t])) . "`) ";
                        echo "VALUES ('" . GBIF_prepare('taxonKey', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('kingdom', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('phylum', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('class', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('order', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('family', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('genus', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('species', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('taxonRank', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('scientificName', $row, $tables_col_types[$t]) . "', '" . GBIF_prepare('speciesKey', $row, $tables_col_types[$t]) . "');" . PHP_EOL;
                    }

                    $tk[] = $row['taxonKey'];
                }
            }
          
        }
    }
}
