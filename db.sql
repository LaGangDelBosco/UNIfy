SET NAMES 'utf8';

DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS profile;
DROP TABLE IF EXISTS post;
DROP TABLE IF EXISTS comment;
DROP TABLE IF EXISTS friendship;
DROP TABLE IF EXISTS reaction;
DROP TABLE IF EXISTS message;

CREATE TABLE user (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(64) NOT NULL,
    birthdate DATE NOT NULL,
    gender VARCHAR(32) NOT NULL,
    profile_picture_url VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE profile (
    profile_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    bio TEXT,
    location VARCHAR(50),
    website VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

CREATE TABLE post (
    post_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    content TEXT,
    media_url VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

CREATE TABLE comment (
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT,
    user_id INT,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES post(post_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

CREATE TABLE friendship (
    friendship_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id_1 INT,
    user_id_2 INT,
    status VARCHAR(32),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id_1) REFERENCES user(user_id),
    FOREIGN KEY (user_id_2) REFERENCES user(user_id)
);

CREATE TABLE reaction (
    reaction_id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT,
    user_id INT,
    type VARCHAR(32),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES post(post_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

CREATE TABLE message (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT,
    receiver_id INT,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES user(user_id),
    FOREIGN KEY (receiver_id) REFERENCES user(user_id)
);

INSERT INTO user (user_id, name, email, password, birthdate, gender, profile_picture_url, created_at, updated_at) VALUES                            
('1', 'Admin Admin', 'admin@example.com', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '2000-01-01', 'male', './path/to/img', '2024-07-21', '2024-07-21'), -- password: admin
('2', 'User User', 'user@example.com', '04f8996da763b7a969b1028ee3007569eaf3a635486ddab211d512c85b9df8fb', '2000-01-01', 'female', './path/to/img', '2024-07-22', '2024-07-22'), -- password: user
('3', 'Mario Rossi', 'mario.rossi@gmail.com', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '1990-01-01', 'male', './path/to/img', '2024-07-31', '2024-07-31'), -- password: password123
('4', 'Luigi Verdi', 'luigi.verdi@gmail.com', 'c6ba91b90d922e159893f46c387e5dc1b3dc5c101a5a4522f03b987177a24a91', '1995-05-05', 'male', './path/to/img', '2024-07-26', '2024-07-26'), -- password: password456
('5', 'Giuseppe Bianchi', 'giuseppe.bianchi@gmail.com', '5efc2b017da4f7736d192a74dde5891369e0685d4d38f2a455b6fcdab282df9c', '1985-06-25', 'male', './path/to/img', '2024-08-01', '2024-08-01'), -- password: password789
('6', 'Anna Rossi', 'anna.rossi@gmail.com', 'a20aff106fe011d5dd696e3b7105200ff74331eeb8e865bb80ebd82b12665a07', '1992-03-15', 'female', './path/to/img', '2024-07-29', '2024-07-29'), -- password: password321
('7', 'Marco Verdi', 'marco.verdi@gmail.com', '28e91b84bd4ac1d95d81b4510777d2b12f3dffa848bb6e219a42f98cdfa06d7d', '1993-06-20', 'male', './path/to/img', '2024-07-30', '2024-07-30'), -- password: password654
('8', 'Laura Bianchi', 'laura.bianchi@gmail.com', 'f6537a5a2f097921d1d1ab410facd30c4356da7326783c2f9ed29f093852cfe2', '1994-09-25', 'female', './path/to/img', '2024-07-31', '2024-07-31'), -- password: password987
('9', 'Francesco Neri', 'francesco.neri@gmail.com', 'd601d7629b263221dd541a3131d865a9bcb087e3edc702867143a996803307ab', '1988-10-31', 'male', './path/to/img', '2024-07-24', '2024-07-24'), -- password: password147
('10', 'Elena Gialli', 'elena.gialli@gmail.com', 'ff7fb48ec0bd80876c9c246d33d18efd0648bff6467fcc945db7f49692dab1e1', '1989-05-30', 'female', './path/to/img', '2024-07-31', '2024-07-31'); -- password: password258

INSERT INTO profile (profile_id, user_id, bio, location, website, created_at, updated_at) VALUES
(1, 1, 'I am the admin', 'Milan, Italy', 'https://www.example.com', '2024-07-21', '2024-07-21'),
(2, 2, 'I am a user', 'Rome, Italy', 'https://www.example1.com', '2024-07-22', '2024-07-22'),
(3, 3, 'I am Mario', 'Milan, Italy', '', '2024-07-31', '2024-07-31'),
(4, 4, 'I am Luigi', 'Rome, Italy', 'https://www.example2.com', '2024-07-26', '2024-07-26'),
(5, 5, 'I am Giuseppe', 'Milan, Italy', '', '2024-08-01', '2024-08-01'),
(6, 6, 'I am Anna', 'Rome, Italy', 'https://www.example3.com', '2024-07-29', '2024-07-29'),
(7, 7, 'I am Marco', 'Milan, Italy', 'https://www.example4.com', '2024-07-30', '2024-07-30'),
(8, 8, 'I am Laura', 'Rome, Italy', '', '2024-07-31', '2024-07-31'),
(9, 9, 'I am Francesco', 'Milan, Italy', 'https://www.example5.com', '2024-07-24', '2024-07-24'),
(10, 10, 'I am Elena', 'Rome, Italy', '', '2024-07-31', '2024-07-31');

INSERT INTO post (post_id, user_id, content, media_url, created_at, updated_at) VALUES
(1, 1, 'Hello, World!', '', '2024-07-21', '2024-07-21'),
(2, 2, 'Hi, there!', '', '2024-07-22', '2024-07-22'),
(3, 3, 'It''s me, Mario!', '', '2024-07-31', '2024-07-31'),
(4, 4, 'It''s me, Luigi!', '', '2024-07-26', '2024-07-26'),
(5, 5, 'It''s me, Giuseppe!', '', '2024-08-01', '2024-08-01'),
(6, 6, 'It''s me, Anna!', '', '2024-07-29', '2024-07-29'),
(7, 7, 'It''s me, Marco!', '', '2024-07-30', '2024-07-30'),
(8, 8, 'It''s me, Laura!', '', '2024-07-31', '2024-07-31'),
(9, 9, 'It''s me, Francesco!', '', '2024-07-24', '2024-07-24'),
(10, 10, 'It''s me, Elena!', '', '2024-07-31', '2024-07-31'),
(11, 1, 'Hello, World! 2', '', '2024-07-21', '2024-07-21'),
(12, 2, 'Hi, there! 2', '', '2024-07-22', '2024-07-22'),
(13, 3, 'It''s me, Mario! 2', '', '2024-07-31', '2024-07-31'),
(14, 4, 'It''s me, Luigi! 2', '', '2024-07-26', '2024-07-26'),
(15, 5, 'It''s me, Giuseppe! 2', '', '2024-08-01', '2024-08-01'),
(16, 6, 'It''s me, Anna! 2', '', '2024-07-29', '2024-07-29'),
(17, 7, 'It''s me, Marco! 2', '', '2024-07-30', '2024-07-30'),
(18, 8, 'It''s me, Laura! 2', '', '2024-07-31', '2024-07-31'),
(19, 9, 'It''s me, Francesco! 2', '', '2024-07-24', '2024-07-24'),
(20, 10, 'It''s me, Elena! 2', '', '2024-07-31', '2024-07-31'),
(21, 1, 'Hello, World! 3', '', '2024-07-21', '2024-07-21'),
(22, 2, 'Hi, there! 3', '', '2024-07-22', '2024-07-22'),
(23, 3, 'It''s me, Mario! 3', '', '2024-07-31', '2024-07-31'),
(24, 4, 'It''s me, Luigi! 3', '', '2024-07-26', '2024-07-26'),
(25, 5, 'It''s me, Giuseppe! 3', '', '2024-08-01', '2024-08-01'),
(26, 6, 'It''s me, Anna! 3', '', '2024-07-29', '2024-07-29'),
(27, 7, 'It''s me, Marco! 3', '', '2024-07-30', '2024-07-30'),
(28, 8, 'It''s me, Laura! 3', '', '2024-07-31', '2024-07-31'),
(29, 9, 'It''s me, Francesco! 3', '', '2024-07-24', '2024-07-24'),
(30, 10, 'It''s me, Elena! 3', '', '2024-07-31', '2024-07-31'),
(31, 1, 'Hello, World! 4', '', '2024-07-21', '2024-07-21'),
(32, 2, 'Hi, there! 4', '', '2024-07-22', '2024-07-22'),
(33, 3, 'It''s me, Mario! 4', '', '2024-07-31', '2024-07-31'),
(34, 4, 'It''s me, Luigi! 4', '', '2024-07-26', '2024-07-26'),
(35, 5, 'It''s me, Giuseppe! 4', '', '2024-08-01', '2024-08-01'),
(36, 6, 'It''s me, Anna! 4', '', '2024-07-29', '2024-07-29'),
(37, 7, 'It''s me, Marco! 4', '', '2024-07-30', '2024-07-30'),
(38, 8, 'It''s me, Laura! 4', '', '2024-07-31', '2024-07-31'),
(39, 9, 'It''s me, Francesco! 4', '', '2024-07-24', '2024-07-24'),
(40, 10, 'It''s me, Elena! 4', '', '2024-07-31', '2024-07-31'),
(41, 1, 'Hello, World! 5', '', '2024-07-21', '2024-07-21'),
(42, 2, 'Hi, there! 5', '', '2024-07-22', '2024-07-22'),
(43, 3, 'It''s me, Mario! 5', '', '2024-07-31', '2024-07-31'),
(44, 4, 'It''s me, Luigi! 5', '', '2024-07-26', '2024-07-26'),
(45, 5, 'It''s me, Giuseppe! 5', '', '2024-08-01', '2024-08-01'),
(46, 6, 'It''s me, Anna! 5', '', '2024-07-29', '2024-07-29'),
(47, 7, 'It''s me, Marco! 5', '', '2024-07-30', '2024-07-30'),
(48, 8, 'It''s me, Laura! 5', '', '2024-07-31', '2024-07-31'),
(49, 9, 'It''s me, Francesco! 5', '', '2024-07-24', '2024-07-24'),
(50, 10, 'It''s me, Elena! 5', '', '2024-07-31', '2024-07-31');

INSERT INTO comment (comment_id, post_id, user_id, content, created_at, updated_at) VALUES
(1, 1, 2, 'Hi, admin!', '2024-07-21', '2024-07-21'),
(2, 2, 1, 'Hello, user!', '2024-07-22', '2024-07-22'),
(3, 3, 4, 'Hi, Mario!', '2024-07-31', '2024-07-31'),
(4, 4, 3, 'Hello, Luigi!', '2024-07-26', '2024-07-26'),
(5, 5, 6, 'Hi, Giuseppe!', '2024-08-01', '2024-08-01'),
(6, 6, 5, 'Hello, Anna!', '2024-07-29', '2024-07-29'),
(7, 7, 8, 'Hi, Marco!', '2024-07-30', '2024-07-30'),
(8, 8, 7, 'Hello, Laura!', '2024-07-31', '2024-07-31'),
(9, 9, 10, 'Hi, Francesco!', '2024-07-24', '2024-07-24'),
(10, 10, 9, 'Hello, Elena!', '2024-07-31', '2024-07-31'),
(11, 1, 2, 'Hi, admin! 2', '2024-07-21', '2024-07-21'),
(12, 2, 1, 'Hello, user! 2', '2024-07-22', '2024-07-22'),
(13, 3, 4, 'Hi, Mario! 2', '2024-07-31', '2024-07-31'),
(14, 4, 3, 'Hello, Luigi! 2', '2024-07-26', '2024-07-26'),
(15, 5, 6, 'Hi, Giuseppe! 2', '2024-08-01', '2024-08-01'),
(16, 6, 5, 'Hello, Anna! 2', '2024-07-29', '2024-07-29'),
(17, 7, 8, 'Hi, Marco! 2', '2024-07-30', '2024-07-30'),
(18, 8, 7, 'Hello, Laura! 2', '2024-07-31', '2024-07-31'),
(19, 9, 10, 'Hi, Francesco! 2', '2024-07-24', '2024-07-24'),
(20, 10, 9, 'Hello, Elena! 2', '2024-07-31', '2024-07-31'),
(21, 1, 2, 'Hi, admin! 3', '2024-07-21', '2024-07-21'),
(22, 2, 1, 'Hello, user! 3', '2024-07-22', '2024-07-22'),
(23, 3, 4, 'Hi, Mario! 3', '2024-07-31', '2024-07-31'),
(24, 4, 3, 'Hello, Luigi! 3', '2024-07-26', '2024-07-26'),
(25, 5, 6, 'Hi, Giuseppe! 3', '2024-08-01', '2024-08-01'),
(26, 6, 5, 'Hello, Anna! 3', '2024-07-29', '2024-07-29'),
(27, 7, 8, 'Hi, Marco! 3', '2024-07-30', '2024-07-30'),
(28, 8, 7, 'Hello, Laura! 3', '2024-07-31', '2024-07-31'),
(29, 9, 10, 'Hi, Francesco! 3', '2024-07-24', '2024-07-24'),
(30, 10, 9, 'Hello, Elena! 3', '2024-07-31', '2024-07-31'),
(31, 1, 2, 'Hi, admin! 4', '2024-07-21', '2024-07-21'),
(32, 2, 1, 'Hello, user! 4', '2024-07-22', '2024-07-22'),
(33, 3, 4, 'Hi, Mario! 4', '2024-07-31', '2024-07-31'),
(34, 4, 3, 'Hello, Luigi! 4', '2024-07-26', '2024-07-26'),
(35, 5, 6, 'Hi, Giuseppe! 4', '2024-08-01', '2024-08-01'),
(36, 6, 5, 'Hello, Anna! 4', '2024-07-29', '2024-07-29'),
(37, 7, 8, 'Hi, Marco! 4', '2024-07-30', '2024-07-30'),
(38, 8, 7, 'Hello, Laura! 4', '2024-07-31', '2024-07-31'),
(39, 9, 10, 'Hi, Francesco! 4', '2024-07-24', '2024-07-24'),
(40, 10, 9, 'Hello, Elena! 4', '2024-07-31', '2024-07-31'),
(41, 1, 2, 'Hi, admin! 5', '2024-07-21', '2024-07-21'),
(42, 2, 1, 'Hello, user! 5', '2024-07-22', '2024-07-22'),
(43, 3, 4, 'Hi, Mario! 5', '2024-07-31', '2024-07-31'),
(44, 4, 3, 'Hello, Luigi! 5', '2024-07-26', '2024-07-26'),
(45, 5, 6, 'Hi, Giuseppe! 5', '2024-08-01', '2024-08-01'),
(46, 6, 5, 'Hello, Anna! 5', '2024-07-29', '2024-07-29'),
(47, 7, 8, 'Hi, Marco! 5', '2024-07-30', '2024-07-30'),
(48, 8, 7, 'Hello, Laura! 5', '2024-07-31', '2024-07-31'),
(49, 9, 10, 'Hi, Francesco! 5', '2024-07-24', '2024-07-24'),
(50, 10, 9, 'Hello, Elena! 5', '2024-07-31', '2024-07-31');

INSERT INTO friendship (friendship_id, user_id_1, user_id_2, status, created_at, updated_at) VALUES
(1, 1, 2, 'accepted', '2024-07-21', '2024-07-21'),
(2, 2, 3, 'sent', '2024-07-22', '2024-07-22'),
(3, 3, 4, 'accepted', '2024-07-31', '2024-07-31'),
(4, 4, 5, 'accepted', '2024-07-26', '2024-07-26'),
(5, 5, 6, 'sent', '2024-08-01', '2024-08-01'),
(6, 6, 7, 'accepted', '2024-07-29', '2024-07-29'),
(7, 7, 8, 'accepted', '2024-07-30', '2024-07-30'),
(8, 8, 9, 'accepted', '2024-07-31', '2024-07-31'),
(9, 9, 10, 'sent', '2024-07-24', '2024-07-24'),
(10, 10, 1, 'accepted', '2024-07-31', '2024-07-31'),
(11, 1, 3, 'accepted', '2024-07-21', '2024-07-21'),
(12, 2, 4, 'sent', '2024-07-22', '2024-07-22'),
(13, 3, 5, 'accepted', '2024-07-31', '2024-07-31'),
(14, 4, 6, 'accepted', '2024-07-26', '2024-07-26'),
(15, 5, 7, 'accepted', '2024-08-01', '2024-08-01'),
(16, 6, 8, 'sent', '2024-07-29', '2024-07-29'),
(17, 7, 9, 'accepted', '2024-07-30', '2024-07-30'),
(18, 8, 10, 'accepted', '2024-07-31', '2024-07-31'),
(19, 9, 1, 'accepted', '2024-07-24', '2024-07-24'),
(20, 10, 2, 'sent', '2024-07-31', '2024-07-31');

INSERT INTO reaction (reaction_id, post_id, user_id, type, created_at, updated_at) VALUES
(1, 1, 2, 'like', '2024-07-21', '2024-07-21'),
(2, 2, 1, 'like', '2024-07-22', '2024-07-22'),
(3, 3, 4, 'like', '2024-07-31', '2024-07-31'),
(4, 4, 3, 'like', '2024-07-26', '2024-07-26'),
(5, 5, 6, 'like', '2024-08-01', '2024-08-01'),
(6, 6, 5, 'like', '2024-07-29', '2024-07-29'),
(7, 7, 8, 'like', '2024-07-30', '2024-07-30'),
(8, 8, 7, 'like', '2024-07-31', '2024-07-31'),
(9, 9, 10, 'like', '2024-07-24', '2024-07-24'),
(10, 10, 9, 'like', '2024-07-31', '2024-07-31'),
(11, 1, 2, 'like', '2024-07-21', '2024-07-21'),
(12, 2, 1, 'like', '2024-07-22', '2024-07-22'),
(13, 3, 4, 'like', '2024-07-31', '2024-07-31'),
(14, 4, 3, 'like', '2024-07-26', '2024-07-26'),
(15, 5, 6, 'like', '2024-08-01', '2024-08-01'),
(16, 6, 5, 'like', '2024-07-29', '2024-07-29'),
(17, 7, 8, 'like', '2024-07-30', '2024-07-30'),
(18, 8, 7, 'like', '2024-07-31', '2024-07-31'),
(19, 9, 10, 'like', '2024-07-24', '2024-07-24'),
(20, 10, 9, 'like', '2024-07-31', '2024-07-31'),
(21, 1, 2, 'like', '2024-07-21', '2024-07-21'),
(22, 2, 1, 'like', '2024-07-22', '2024-07-22'),
(23, 3, 4, 'like', '2024-07-31', '2024-07-31'),
(24, 4, 3, 'like', '2024-07-26', '2024-07-26'),
(25, 5, 6, 'like', '2024-08-01', '2024-08-01'),
(26, 6, 5, 'like', '2024-07-29', '2024-07-29'),
(27, 7, 8, 'like', '2024-07-30', '2024-07-30'),
(28, 8, 7, 'like', '2024-07-31', '2024-07-31'),
(29, 9, 10, 'like', '2024-07-24', '2024-07-24'),
(30, 10, 9, 'like', '2024-07-31', '2024-07-31'),
(31, 1, 2, 'like', '2024-07-21', '2024-07-21'),
(32, 2, 1, 'like', '2024-07-22', '2024-07-22'),
(33, 3, 4, 'like', '2024-07-31', '2024-07-31'),
(34, 4, 3, 'like', '2024-07-26', '2024-07-26');

INSERT INTO message (message_id, sender_id, receiver_id, content, created_at, updated_at) VALUES
(1, 1, 2, 'Hi, user!', '2024-07-21', '2024-07-21'),
(2, 2, 1, 'Hello, admin!', '2024-07-22', '2024-07-22'),
(3, 3, 4, 'Hi, Luigi!', '2024-07-31', '2024-07-31'),
(4, 4, 3, 'Hello, Mario!', '2024-07-26', '2024-07-26'),
(5, 5, 6, 'Hi, Anna!', '2024-08-01', '2024-08-01'),
(6, 6, 5, 'Hello, Giuseppe!', '2024-07-29', '2024-07-29'),
(7, 7, 8, 'Hi, Laura!', '2024-07-30', '2024-07-30'),
(8, 8, 7, 'Hello, Marco!', '2024-07-31', '2024-07-31'),
(9, 9, 10, 'Hi, Elena!', '2024-07-24', '2024-07-24'),
(10, 10, 1, 'Hello, Francesco!', '2024-07-31', '2024-07-31'),
(11, 1, 2, 'Hi, user! 2', '2024-07-21', '2024-07-21'),
(12, 2, 1, 'Hello, admin! 2', '2024-07-22', '2024-07-22'),
(13, 3, 4, 'Hi, Luigi! 2', '2024-07-31', '2024-07-31'),
(14, 4, 3, 'Hello, Mario! 2', '2024-07-26', '2024-07-26'),
(15, 5, 6, 'Hi, Anna! 2', '2024-08-01', '2024-08-01');