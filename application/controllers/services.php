<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * The Services controller handles all AJAX requests to the server, to provide
 * an API for accessing and modifying the polls website database.
 */
class Services extends CI_CONTROLLER {
    
    /*
     * This function handles AJAX requests to services/polls(/id).
     * The response is determined by finding the http method as well as
     * whether an id is given as an argument.
     */
    public function polls($id = null) {
        $this->load->model('poll');
        $method = $this->input->server('REQUEST_METHOD'); // Find http request type
        $this->output->set_status_header(200);
        
        if (is_null($id)) { // AJAX request without poll id
            switch($method) {
                case 'GET': // Return list of polls
                    $this->_getPolls();
                    break;
                case 'POST': // Add a poll to the database
                    $this->_createPoll();
                    break;
                default:
                    $this->output->set_status_header(400); // Bad request
                    break;
            }
        } else { // AJAX request with poll id
            switch($method) {
                case 'GET': // Return a list of details including answers
                    $this->_getPollDetails($id);
                    break;
                case 'PUT': // Replace an existing poll in the database
                    $this->_updatePoll($id);
                    break;
                case 'DELETE': // Delete a poll
                    $this->_deletePoll($id);
                    break;
                default: // invalid request
                    $this->output->set_status_header(400);
                    break;
            }
        }
    }
    
    /*
     * polls() helper functions, which use the poll model to access 
     * the database.
     */
    private function _getPolls() {
        try {
            $list = $this->poll->getPolls();
            $this->output->set_output(json_encode($list));
        } catch (Exception $ex) {
            $this->output->set_status_header(500); // Internal server error
        }
    }
    
    private function _getPollDetails($id) {
        try {
            $data = $this->poll->getPollDetails($id);
            $this->output->set_output(json_encode($data));
        } catch (Exception $ex) {
            $this->output->set_status_header(500);
        }
    }
    
    private function _createPoll() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $this->poll->createPoll($data);
            $this->output->set_status_header(201);
        } catch (Exception $ex) {
            $this->output->set_status_header(500);
        }
    }
    
    private function _updatePoll($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $this->poll->updatePoll($id, $data);
        } catch (Exception $ex) {
            $this->output->set_status_header(500);
        }
    }
    
    private function _deletePoll($id) {
        try {
            $this->poll->deletePoll($id);
        } catch (Exception $ex) {
            $this->output->set_status_header(500);
        }
    }
    
    /*
     * Handles AJAX requests to services/votes/id(/vote). The response is
     * determined based on the http request method and whether the vote
     * argument is present.
     */
    public function votes($pollID, $vote = null) {
        $this->load->model('vote');
        $method = $this->input->server('REQUEST_METHOD');
        $this->output->set_status_header(200);
        
        if (is_null($vote)) { // $pollID is only argument
            
            if ($method === 'GET') { // return a list of votes
                $this->_getVotes($pollID);
                
            } else if ($method === 'DELETE') { // delete all votes for the given poll
                $this->_deleteVotes($pollID);
                
            } else {
                $this->output->set_status_header(400);
            }
        } else { // $vote is given
            
            if ($method === 'POST') { // add vote to database
                $this->_addVote($pollID, $vote);
            } else {
                $this->output->set_status_header(400);
            }
        }
    }
    
    /*
     * votes() helper functions, which use the votes model to access 
     * the database.
     */
    private function _getVotes($pollID) {
        try {
            $list = $this->vote->getVotes($pollID);
            $this->output->set_output(json_encode($list));
        } catch (Exception $ex) {
            $this->output->set_status_header(500);
        }
    }
    
    private function _deleteVotes($pollID) {
        try {
            $this->vote->deleteVotes($pollID);
        } catch (Exception $ex) {
            $this->output->set_status_header(500);
        }
    }
    
    private function _addVote($pollID, $vote) {
        try {
            $ipAddress = $this->input->ip_address();
            $this->vote->addVote($pollID, $vote, $ipAddress);
        } catch (Exception $ex) {
            $this->set_status_header(500);
        }
    }
}