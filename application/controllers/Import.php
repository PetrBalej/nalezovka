<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Import extends MY_Controller {

    public function index() {

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

            
            // naplnění tabulky event
            echo "INSERT INTO event (gbifID, datasetKey, countryCode, locality, stateProvince, decimalLatitude, decimalLongitude, elevation, eventDate, day, month, year, institutionCode, souradnice) ";
            echo "VALUES ('" . $row['gbifID'] . "', '" . $row['datasetKey'] . "', '" . $row['countryCode'] . "', '" . $row['locality'] . "', '" . $row['stateProvince'] . "', '" . $row['decimalLatitude'] . "', '" . $row['decimalLongitude'] . "', " . num_check($row['elevation']) . ", '" . $row['eventDate'] . "', " . num_check($row['day']) . ", " . num_check($row['month']) . ", " . num_check($row['year']) . ", '" . $row['institutionCode'] . "', ST_PointFromText('POINT(" . $row['decimalLongitude'] . " " . $row['decimalLatitude'] . ")'));" . PHP_EOL;

            // naplnění tabulky occurrence
            echo "INSERT INTO occurrence (event_gbifID, occurrenceID, basisOfRecord, license, rightsHolder, recordedBy, issue, taxon_taxonKey) ";
            echo "VALUES ('" . $row['gbifID'] . "', '" . $row['occurrenceID'] . "', '" . $row['basisOfRecord'] . "', '" . $row['license'] . "', '" . $row['rightsHolder'] . "', '" . $row['recordedBy'] . "', '" . $row['issue'] . "', " . num_check($row['taxonKey']) . ");" . PHP_EOL;

            // naplnění tabulky taxon (normalizace)
            // potřebujeme vypsat unikátní hodnoty taxonů (jen jedenkrát, vložení uníkátního klíče stejné hodnoty by databáze odmítla)
            // zapíšeme si tedy po každém vypsání hodnoty 'taxonKey' do pole a budeme ověřovat v dalším cyklu jestli už nebylo zapsáno/uloženo
            // order je v MariaDB klíčové slovo a musí být proto escapováno přes uvozovku zvanou backquote (backtick) na: `order`
            if (!in_array($row['taxonKey'], $tk)) { // neexistuje-li zatím v poli $taxonKey naše hodnota 'taxonKey', můžeme vložit do tabulky 
                echo "INSERT INTO taxon (taxonKey, kingdom, phylum, class, `order`, family, genus, species, taxonRank, scientificName, speciesKey) ";
                echo "VALUES ('" . $row['taxonKey'] . "', '" . $row['kingdom'] . "', '" . $row['phylum'] . "', '" . $row['class'] . "', '" . $row['order'] . "', '" . $row['family'] . "', '" . $row['genus'] . "', '" . $row['species'] . "', '" . $row['taxonRank'] . "', '" . $row['scientificName'] . "', " . num_check($row['speciesKey']) . ");" . PHP_EOL;
            }

            $tk[] = $row['taxonKey'];
        }

    }

}
