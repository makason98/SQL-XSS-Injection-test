CREATE DATABASE IF NOT EXISTS sql_injection_lab;
USE sql_injection_lab;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Songs Table
CREATE TABLE IF NOT EXISTS songs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    artist VARCHAR(100)
);

-- Seed Users
INSERT INTO users (username, password) VALUES
('admin', 'supersecretadminpass'),
('john_doe', 'password123'),
('alice_w', 'wonderland!'),
('bob_builder', 'canwefixit'),
('eve_hacker', 'password'),
('charlie_brown', 'snoopy'),
('david_g', 'goliath'),
('frank_castle', 'punisher'),
('grace_hopper', 'compiler'),
('heidi_sql', 'maria123');

-- Seed Songs
INSERT INTO songs (title, description, artist) VALUES
('Bohemian Rhapsody', 'A song by the British rock band Queen. It was written by Freddie Mercury for the band''s 1975 album A Night at the Opera.', 'Queen'),
('Stairway to Heaven', 'A song by the English rock band Led Zeppelin, released in late 1971.', 'Led Zeppelin'),
('Hotel California', 'The title track from the Eagles'' album of the same name and was released as a single in February 1977.', 'Eagles'),
('Imagine', 'The best-selling single of his solo career, its lyrics encourage the listener to imagine a world at peace.', 'John Lennon'),
('Smells Like Teen Spirit', 'The opening track and lead single from the band''s second album, Nevermind (1991).', 'Nirvana');

-- Posts Table (For XSS)
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- XSS Logs Table (To capture stolen data)
CREATE TABLE IF NOT EXISTS xss_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
