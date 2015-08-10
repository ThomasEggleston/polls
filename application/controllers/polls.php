<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Default controller. Loads the master view.
 */
class Polls extends CI_CONTROLLER {
    
    public function index() {
        $this->load->helper('url', 'html');
        $this->load->view('templates/master');
    }
}