CREATE TABLE riddles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    riddle VARCHAR(255) NOT NULL,
    answer VARCHAR(100) NOT NULL,
    hint VARCHAR(255),
    roomId INT NOT NULL
);

INSERT INTO riddles (riddle, answer, hint, roomId)
VALUES
    ('er staan 3 stapel dozen en achter welke ligt de hint?', 'achter de stapel met de minste dozen', 'De hint ligt achter de stapel met de minste dozen.', 1),
    ('met wat was ik de kleding?', 'de stroom kabel', 'Hoe krijgt de wasmachine stroom?', 1),
    ('hoeveel trap tredens zijn er en deel het door 2 en dan heb je het laaste cijfer voor de kluis', '6', 'klik op de trap', 1);