<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Represents a poll object.
 */
class Poll extends CI_Model {
    
    public $id;
    public $title;
    public $question;
    public $answers = array();
    
    public function __construct() {
        $this->load->database();
    }
    
    /* 
     * Creates a list of all polls.
     * @return associative array containing the id and title for each poll.
     */
    public function getPolls() {
        $this->db->select('id, Title as title');
        $rows = $this->db->get('Polls')->result();
        $list = array();
        foreach($rows as $row) {
            $list[] = array('id'   => $row->id,
                            'title'=> $row->title);
        }
        return $list;
    }
    
    /*
     * Creates a list of details for a given poll. All of the answers for
     * this poll are included.
     */
    public function getPollDetails($id) {
        $pollDetails = new Poll();
        $this->db->select('id, Title as title, Question as question');
        $pollQuery = $this->db->get_where('Polls', array('id' => $id))->result();
        $pollData = $pollQuery[0];
        foreach((array) $pollData as $field => $value) {
            $pollDetails->$field = $value;
        }
        // Create list of answers
        $this->db->select('Answer as answer');
        $this->db->from('Answers');
        $this->db->where(array('PollID' => $id));
        $this->db->order_by('id', 'asc');
        
        $rows = $this->db->get()->result();
        foreach($rows as $row) {
            $pollDetails->answers[] = $row->answer;
        }
        return $pollDetails;
    }
    
    /*
     * Uses the data received from an AJAX request to add a new poll into
     * the database. It also adds rows into the Answers table for each
     * answer contained in $data.
     */
    public function createPoll($data) {
        
        $pollDetails = array('Title' => $data['title'],
                           'Question' => $data['question']);
        $this->db->insert('Polls', $pollDetails); // Create poll record
        $pollID = $this->db->insert_id(); // Get the id of the new poll
        $answers = $data['answers'];
        
        $ansNum = 1;
        foreach($answers as $answer) {
            $answerData = array('Answer' => $answer,
                                'AnswerNumber'=> (string) $ansNum,
                                'PollID'      => $pollID);
            $this->db->insert('Answers', $answerData); // Create answers record
            $ansNum++;
        }
    }
    
    /*
     * Deletes a Poll with the given id.
     */
    public function deletePoll($id) {
        $this->db->where('id', $id);
        $this->db->delete('Polls');
    }
    
    /*
     * Updates a poll with the given id.
     */
    public function updatePoll($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('Polls', array('title'=> $data['title'],
                                         'question'=>$data['question']));
        
    }
}