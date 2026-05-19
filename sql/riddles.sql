CREATE TABLE riddles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    riddle VARCHAR(255) NOT NULL,
    answer VARCHAR(100) NOT NULL,
    hint VARCHAR(255),
    roomId INT NOT NULL
);

INSERT INTO riddles (riddle, answer, hint, roomId)
VALUES
    ('Welke Pokémon is nummer 25 in de Pokédex?', 'Pikachu', 'Het is de mascotte van Pokémon.', 1),
    ('')
