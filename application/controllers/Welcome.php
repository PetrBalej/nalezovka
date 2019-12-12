<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {

        /* test geoPHP knihovny */
        // načtení knihovny geoPHP
        include_once(APPPATH . 'third_party/geoPHP/geoPHP.inc');

        // Polygon WKT example
        $polygon = geoPHP::load('POLYGON((1 1,5 1,5 5,1 5,1 1),(2 2,2 3,3 3,3 2,2 2))', 'wkt');
        $area = $polygon->getArea();
        $centroid = $polygon->getCentroid();
        $centX = $centroid->getX();
        $centY = $centroid->getY();

        $data['geoPHP'][] = "This polygon has an area of " . $area . " and a centroid with X=" . $centX . " and Y=" . $centY;

        // MultiPoint json example
        $json = '{
                    "type": "MultiPoint",
                    "coordinates": [
                        [100.0, 0.0], [101.0, 1.0]
                    ]
                }';

        $multipoint = geoPHP::load($json, 'json');
        $multipoint_points = $multipoint->getComponents();
        $first_wkt = $multipoint_points[0]->out('wkt');

        $data['geoPHP'][] = "This multipoint has " . $multipoint->numGeometries() . " points. The first point has a wkt representation of " . $first_wkt;

        $this->load->view('welcome_message', $data);
    }

}
