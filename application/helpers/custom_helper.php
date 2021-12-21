<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
 *
 * Dodatečně přidané helpery
 *
 */

// https://stackoverflow.com/questions/9690448/regular-expression-to-remove-comments-from-sql-statement/13823184#13823184
// odstraní z SQL komentáře
function trim_sql_comments($sql)
{
    $sqlComments = '@(([\'"]).*?[^\\\]\2)|((?:\#|--).*?$|/\*(?:[^/*]|/(?!\*)|\*(?!/)|(?R))*\*\/)\s*|(?<=;)\s+@ms';
    /* Commented version
      $sqlComments = '@
      (([\'"]).*?[^\\\]\2) # $1 : Skip single & double quoted expressions
      |(                   # $3 : Match comments
      (?:\#|--).*?$    # - Single line comments
      |                # - Multi line (nested) comments
      /\*             #   . comment open marker
      (?: [^/*]    #   . non comment-marker characters
      |/(?!\*) #   . ! not a comment open
      |\*(?!/) #   . ! not a comment close
      |(?R)    #   . recursive case
      )*           #   . repeat eventually
      \*\/             #   . comment close marker
      )\s*                 # Trim after comments
      |(?<=;)\s+           # Trim after semi-colon
      @msx';
     */
    //$uncommentedSQL = trim( preg_replace( $sqlComments, '$1', $sql ) );
    //preg_match_all( $sqlComments, $sql, $comments );
    //$extractedComments = array_filter( $comments[ 3 ] );
    //var_dump( $uncommentedSQL, $extractedComments );

    return str_ireplace("\r", ' ', str_ireplace("\n", ' ', trim(preg_replace($sqlComments, '$1', $sql))));
}

// https://stackoverflow.com/a/29711778
// funkce pro rozparsování CSV (respektive TSV) ze Simple exportu GBIF
function assoc_getcsv($csv_path)
{
    $r = array_map(function ($d) {
        return str_getcsv($d, "\t");
    }, file($csv_path));
    foreach ($r as $k => $d) {
        $r[$k] = array_combine($r[0], $r[$k]);
    }
    return array_values(array_slice($r, 1));
}

// funkce pro kontrolu vstupu - pokud není na vstupu číslo vrací NULL
function num_check($value, $invalid = 'NULL')
{
    if (preg_match('/^\d+$/', $value)) {
        return $value;
    } else {
        return $invalid;
    }
}

// převod binárního formátu bodu z databáze do stringu
function point_BIN_text($BIN)
{
    $coords = unpack('x4/clat/Llat/dlat/dlon', $BIN);
    return "POINT(" . $coords['lat'] . " " . $coords['lon'] . ")";
}

// převede vybrané hodnoty klíčů pole z GBIF atributů na klikatelné odkazy v HTML a vrátí takto upravené pole
function GBIF_hypertext($array = array())
{
    foreach ($array as $key => $row) {
        if ($key == "datasetKey") {
            // dataset: https://www.gbif.org/dataset/83cbb4fa-f762-11e1-a439-00145eb45e9a
            $array[$key] = "<a href='https://www.gbif.org/dataset/" . $row . "'>" . $row . "</a>";
        }
        if ($key == "speciesKey") {
            // species: https://www.gbif.org/species/2490719
            $array[$key] = "<a href='https://www.gbif.org/species/" . $row . "'>" . $row . "</a>";
        }
        if ($key == "gbifID") {
            // occurence: https://www.gbif.org/occurrence/29888211
            $array[$key] = "<a href='https://www.gbif.org/occurrence/" . $row . "'>" . $row . "</a>";
        }
        if ($key == "coordinates") {
            // navíc převod souřadnic z BIN do stringu
            $array[$key] = point_BIN_text($row);
        }
    }
    return $array;
}

// úprava hodnot z řádků GBIF
function GBIF_hypertext_all($array = array())
{
    foreach ($array as $key => $row) {
        $array[$key] =  GBIF_hypertext($row);
    }
    return $array;
}

// převod jednoho řádku pole do jednoduché tabulky s páry: klíč | hodnota
function GBIF_array_row_table($array = array(), $table_atr = "class='trida' style=''", $GBIF_hypertext = true)
{
    if ($GBIF_hypertext === true) {
        $array = GBIF_hypertext($array);
    }

    $output = "<table  $table_atr >" . PHP_EOL;
    foreach ($array as $key => $row) {
        $output .= "<tr>";
        $output .= "<td>" . $key . "</td> <td>" . $row . "</td>";
        $output .= "</tr>" . PHP_EOL;
    }
    $output .= "</table>" . PHP_EOL;

    return $output;
}

// kontrola funkčnosti vložených SQL queryů
function validate_query_result($sqls, $sqls_selected = array())
{
    $CI = & get_instance();

    if (!empty($sqls_selected)) {
        foreach ($sqls_selected as $value) {
            $sqls_prep[$value] = $sqls[$value];
        }
        $sqls = $sqls_prep;
    }


    $k[1] = "taxonKey|scientificName|taxon|ORDER BY";
    $k[2] = "*|ST_AsText|coordinates|coordinatesWKT|event|INNER|JOIN|occurrence|ON|event|gbifID|occurrence|event_gbifID|taxon|taxon_taxonKey|taxonKey";
    $k[3] = "ST_AsText|ST_Centroid|ST_Envelope|ST_GeomFromText|GROUP_CONCAT|ST_AsText|coordinates|center|event";
    $k[5] = "ORDER|BY|ST_Y|coordinates|DESC|LIMIT"; // sever
    $k[6] = "ORDER|BY|ST_Y|coordinates|ASC|LIMIT"; // jih
    $k[7] = "ORDER|BY|ST_X|coordinates|ASC|LIMIT"; // západ
    $k[8] = "ORDER|BY|ST_X|coordinates|DESC|LIMIT"; // východ
    $k[9] = "coordinatesWKT|ST_|coordinates|gbifID";
    $k[10] = "coordinatesWKT|ST_|coordinates|gbifID|geo_poly";
    $k[11] = "coordinatesWKT|ST_|coordinates|gbifID|geo_line";
    foreach ($sqls as $key => $value) {
        if ($key == 4) {
            if (strtolower(trim_sql_comments($value)) != "st_distance") {
                show_error("<p>The (correct) SQL function for determining the distance between two geometries is not specified!</p><p>Query used within the controller: <i>application/controllers/<b>" . ucfirst($CI->router->fetch_class()) . "</b>.php</i></p>", 404, "Error in SQL query no. " . $key . " v public/<b>query" . str_pad($key, 2, "0", STR_PAD_LEFT) . ".sql</b>");
            }
        } elseif ($key == 5 or $key == 6 or $key == 7 or $key == 8) {
            if (substr(trim(strtolower(trim_sql_comments($value))), 0, 8) != "order by") {
                show_error("<p>The (correct) SQL function for finding The MOST- (N/S/E/W) coordinate is not specified!</p><p>Query used within the controller: <i>application/controllers/<b>" . ucfirst($CI->router->fetch_class()) . "</b>.php</i></p>", 404, "Error in SQL query no. " . $key . " v public/<b>query" . str_pad($key, 2, "0", STR_PAD_LEFT) . ".sql</b>");
            }

            if (!$CI->db->simple_query(trim_sql_comments($CI->public_sql[2] . " " . trim_sql_comments($value)))) {
                show_error("<p>The (correct) SQL function for finding The MOST- (N/S/E/W) coordinate is not specified!</p>" . "<b>SQL:</b><code>" . $value . "</code>" . "<b>Error:</b><code>" . print_r($CI->db->error(), true) . "</code>" . "<p>Query used within the controller: <i>application/controllers/<b>" . ucfirst($CI->router->fetch_class()) . "</b>.php</i></p>", 404, "Error in SQL query no. " . $key . " v public/<b>query" . str_pad($key, 2, "0", STR_PAD_LEFT) . ".sql</b>");
            }

            $missing = explode("|", $k[$key]);
            $missing_matched = array();

            // kontrola povinných slov a odstranění SQL komentářů
            foreach ($missing as $w) {
                if (preg_match('/(' . preg_quote($w) . ')/i', trim_sql_comments($value)) !== 1) {
                    $missing_matched[] = $w;
                }
            }
            if (!empty($missing_matched)) {
                show_error("<b>SQL:</b><code>" . $value . "</code>" . "<b>One of the following attributes / expressions is missing in the SQL query:</b><code>" . implode(", ", $missing_matched) . "</code>", 404, "Error in SQL query no. " . $key . " v public/<b>query" . str_pad($key, 2, "0", STR_PAD_LEFT) . ".sql</b>");
            }
        } else {
            if (!empty(trim_sql_comments($value))) {
                if (!$CI->db->simple_query(trim_sql_comments($value))) {
                    show_error("<b>SQL:</b><code>" . $value . "</code>" . "<b>Error:</b><code>" . print_r($CI->db->error(), true) . "</code>" . "<p>Query used within the controller: <i>application/controllers/<b>" . ucfirst($CI->router->fetch_class()) . "</b>.php</i></p>", 404, "Error in SQL query no. " . $key . " v public/<b>query" . str_pad($key, 2, "0", STR_PAD_LEFT) . ".sql</b>");
                }
            }
            $missing = explode("|", $k[$key]);
            $missing_matched = array();

            // kontrola povinných slov a odstranění SQL komentářů
            foreach ($missing as $w) {
                if (preg_match('/(' . preg_quote($w) . ')/i', trim_sql_comments($value)) !== 1) {
                    $missing_matched[] = $w;
                }
            }
            if (!empty($missing_matched)) {
                show_error("<b>SQL:</b><code>" . $value . "</code>" . "<b>One of the following attributes / expressions is missing in the SQL query:</b><code>" . implode(", ", $missing_matched) . "</code>", 404, "Error in SQL query no. " . $key . " v public/<b>query" . str_pad($key, 2, "0", STR_PAD_LEFT) . ".sql</b>");
            }
        }
    }
}


// ošetření vstupů z GBIF simple csv
function GBIF_prepare($col_name, $csv_row, $table_col_types)
{
    $value = $csv_row[$col_name];


    if ($col_name == "gbifID" and isset($table_col_types["event_gbifID"])) {
        $col_name = "event_gbifID";
    }
    if ($col_name == "taxonKey" and isset($table_col_types["taxon_taxonKey"])) {
        $col_name = "taxon_taxonKey";
    }

   
    $type = $table_col_types[$col_name][0];
    $range = $table_col_types[$col_name][1];

    if ($type == "varchar") {
        $res_value = mb_substr($value, 0, $range);
    } elseif ($type == "int" or $type == "smallint") {
        $res_value = mb_substr(intval($value), 0, $range);
    } elseif ($type == "decimal") {
        $res_value = round(floatval($value), explode(",", $range)[1]);
    } else {
        $res_value = $value;
    }


    return htmlspecialchars($res_value, ENT_QUOTES);
}
