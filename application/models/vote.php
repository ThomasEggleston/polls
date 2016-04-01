<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vote extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    /*
     * Returns a list of votes for each answer in the given poll.
     */
    public function getVotes($pollID) {
        $sql = "SELECT count(Votes.id) as votes
                FROM Answers
                LEFT JOIN Votes ON Answers.AnswerNumber = Votes.AnswerNumber
                    AND Votes.PollID = {$pollID}
                WHERE Answers.PollID = {$pollID}
                GROUP BY Answer
                ORDER BY Answers.id ASC";
        $rows = $this->db->query($sql)->result();

        $list = array();
        foreach($rows as $row) {
            $list[] = array('votes' => $row->votes);
        }
        return $list;
    }

    /*
     * Adds a new row into the Vote table of the database. This is done
     * each time a vote is submitted.
     */
    public function addVote($pollID, $answerNumber, $ipAddress) {
        $voteData = array('AnswerNumber'=> $answerNumber,
                          'IpAddress'   => $ipAddress,
                          'PollID'      => $pollID);

        $this->db->insert('Votes', $voteData);
    }

    /*
     * Deletes all the existing rows in the Vote table which correspond to
     * the given poll.
     */
    public function deleteVotes($pollID) {
        $this->db->where('PollID', $pollID);
        $this->db->delete('Votes');
    }
}
