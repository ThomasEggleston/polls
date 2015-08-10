
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE if NOT EXISTS Polls
(
    id int NOT NULL AUTO_INCREMENT,
    Title varchar(30) NOT NULL,
    Question varchar(100) NOT NULL,
    PRIMARY KEY(id)
);

CREATE TABLE if NOT EXISTS Answers
(
    id int NOT NULL AUTO_INCREMENT,
    Answer varchar(40) NOT NULL,
    AnswerNumber int NOT NULL,
    PollID int NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY(PollID)
        REFERENCES Polls(id) ON DELETE CASCADE
);

CREATE TABLE if NOT EXISTS Votes
(
    id int NOT NULL AUTO_INCREMENT,
    AnswerNumber int NOT NULL,
    IpAddress varchar(15) NOT NULL,
    PollID int NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY(PollID)
        REFERENCES Polls(id) ON DELETE CASCADE
);

INSERT INTO Polls (id, Title, Question) VALUES
('1', 'Favourite fruit',       'Which of the following is your favourite fruit?'),
('2', 'Preferred web browser', 'Which web browser do you prefer?'),
('3', 'Best NZ rugby team',    'Which is the best Super Rugby team in New Zealand?');

INSERT INTO Answers (id, Answer, AnswerNumber, PollID) VALUES
('1',  'Apples',            '1', '1'),
('2',  'Bananas',           '2', '1'),
('3',  'Oranges',           '3', '1'),
('4',  'Kiwifruit',         '4', '1'),
('5',  'Chrome',            '1', '2'),
('6',  'Firefox',           '2', '2'),
('7',  'Internet Explorer', '3', '2'),
('8',  'Safari',            '4', '2'),
('9',  'Opera',             '5', '2'),
('10', 'Blues',             '1', '3'),
('11', 'Chiefs',            '2', '3'),
('12', 'Crusaders',         '3', '3'),
('13', 'Highlanders',       '4', '3'),
('14', 'Hurricanes',        '5', '3');

INSERT INTO Votes (id, AnswerNumber, IpAddress, PollID) VALUES
('1',  '1', '192.168.0.101', '1'),
('2',  '2', '192.168.0.101', '1'),
('3',  '2', '192.168.0.101', '1'),
('4',  '3', '192.168.0.101', '1'),
('5',  '3', '192.168.0.101', '1'),
('6',  '3', '192.168.0.101', '1'),
('7',  '4', '192.168.0.101', '1'),
('8',  '4', '192.168.0.101', '1'),
('9',  '4', '192.168.0.101', '1'),
('10', '4', '192.168.0.101', '1');
