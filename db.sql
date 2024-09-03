SET NAMES 'utf8';

DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS profile;
DROP TABLE IF EXISTS post;
DROP TABLE IF EXISTS comment;
DROP TABLE IF EXISTS friendship;
DROP TABLE IF EXISTS reaction;
DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS book;
DROP TABLE IF EXISTS chat_message;
DROP TABLE IF EXISTS room;
DROP TABLE IF EXISTS room_message;

CREATE TABLE user (
    username VARCHAR(100) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(64) NOT NULL,
    birthdate DATE NOT NULL,
    gender VARCHAR(32) NOT NULL,
    profile_picture_path VARCHAR(100),
    banned BOOLEAN DEFAULT FALSE,
    ban_reason TEXT DEFAULT NULL,
    ban_start TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE profile (
    profile_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100),
    bio TEXT,
    location VARCHAR(50),
    website VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES user(username)
);

CREATE TABLE post (
    post_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100),
    content TEXT,
    media_path VARCHAR(100) DEFAULT NULL,
    hidden BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES user(username)
);

CREATE TABLE comment (
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT,
    username VARCHAR(100),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES post(post_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (username) REFERENCES user(username)
);

CREATE TABLE friendship (
    friendship_id INT PRIMARY KEY AUTO_INCREMENT,
    username_1 VARCHAR(100),
    username_2 VARCHAR(100),
    status VARCHAR(32),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (username_1) REFERENCES user(username),
    FOREIGN KEY (username_2) REFERENCES user(username)
);

CREATE TABLE reaction (
    reaction_id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT,
    username VARCHAR(100),
    type VARCHAR(32),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES post(post_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (username) REFERENCES user(username)
);

CREATE TABLE message (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id VARCHAR(100),
    receiver_id VARCHAR(100),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES user(username) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES user(username)
);

CREATE TABLE likes (
    post_id INT,
    username VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (post_id, username),
    FOREIGN KEY (post_id) REFERENCES post(post_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (username) REFERENCES user(username)
);

CREATE TABLE book (
    book_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100),
    title VARCHAR(100) NOT NULL,
    author VARCHAR(100) NOT NULL,
    genre VARCHAR(100) NOT NULL,
    year INT NOT NULL,
    description TEXT,
    cover_path VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    price DECIMAL(10, 2),
    FOREIGN KEY (username) REFERENCES user(username)
);

CREATE TABLE chat_message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_annuncio INT,
    sender_username VARCHAR(100),
    receiver_username VARCHAR(100),
    message TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_annuncio) REFERENCES book(book_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (sender_username) REFERENCES user(username) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (receiver_username) REFERENCES user(username) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE room (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by VARCHAR(255) NOT NULL,
    FOREIGN KEY (created_by) REFERENCES user(username) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE room_message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_code VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO user (username, name, email, password, birthdate, gender, profile_picture_path, created_at, updated_at) VALUES
('admin', 'Admin Admin', 'admin@example.com', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '2000-01-01', 'male', './media/profile-pictures/default.jpg', '2024-07-21', '2024-07-21'), -- password: admin
('user', 'User User', 'user@example.com', '04f8996da763b7a969b1028ee3007569eaf3a635486ddab211d512c85b9df8fb', '2000-01-01', 'female', './media/profile-pictures/default.jpg', '2024-07-22', '2024-07-22'), -- password: user
('supermario', 'Mario Rossi', 'mario.rossi@gmail.com', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '1990-01-01', 'male', './media/profile-pictures/default.jpg', '2024-07-31', '2024-07-31'), -- password: password123
('luigi', 'Luigi Verdi', 'luigi.verdi@gmail.com', 'c6ba91b90d922e159893f46c387e5dc1b3dc5c101a5a4522f03b987177a24a91', '1995-05-05', 'male', './media/profile-pictures/default.jpg', '2024-07-26', '2024-07-26'), -- password: password456
('giuseppe', 'Giuseppe Bianchi', 'giuseppe.bianchi@gmail.com', '5efc2b017da4f7736d192a74dde5891369e0685d4d38f2a455b6fcdab282df9c', '1985-06-25', 'male', './media/profile-pictures/default.jpg', '2024-08-01', '2024-08-01'), -- password: password789
('anna', 'Anna Rossi', 'anna.rossi@gmail.com', 'a20aff106fe011d5dd696e3b7105200ff74331eeb8e865bb80ebd82b12665a07', '1992-03-15', 'female', './media/profile-pictures/default.jpg', '2024-07-29', '2024-07-29'), -- password: password321
('marco', 'Marco Verdi', 'marco.verdi@gmail.com', '28e91b84bd4ac1d95d81b4510777d2b12f3dffa848bb6e219a42f98cdfa06d7d', '1993-06-20', 'male', './media/profile-pictures/default.jpg', '2024-07-30', '2024-07-30'), -- password: password654
('laura', 'Laura Bianchi', 'laura.bianchi@gmail.com', 'f6537a5a2f097921d1d1ab410facd30c4356da7326783c2f9ed29f093852cfe2', '1994-09-25', 'female', './media/profile-pictures/default.jpg', '2024-07-31', '2024-07-31'), -- password: password987
('francesco', 'Francesco Neri', 'francesco.neri@gmail.com', 'd601d7629b263221dd541a3131d865a9bcb087e3edc702867143a996803307ab', '1988-10-31', 'male', './media/profile-pictures/default.jpg', '2024-07-24', '2024-07-24'), -- password: password147
('elena', 'Elena Gialli', 'elena.gialli@gmail.com', 'ff7fb48ec0bd80876c9c246d33d18efd0648bff6467fcc945db7f49692dab1e1', '1989-05-30', 'female', './media/profile-pictures/default.jpg', '2024-07-31', '2024-07-31'); -- password: password258

# TEST BANNED USER
INSERT INTO user (username, name, email, password, birthdate, gender, profile_picture_path, banned, ban_reason, ban_start, created_at, updated_at) VALUES
('banned', 'Banned User', 'banned@gmail.com', '071ccd9453661bfdf4b1d89093d4b7c28b898c8d40e7ccaf3a0f7a7ee7f043b0', '1990-01-01', 'male', './media/profile-pictures/default.jpg', TRUE, 'TEST_BAN', '2024-07-31', '2024-07-31', '2024-07-31'); -- password: password369

INSERT INTO profile (profile_id, username, bio, location, website, created_at, updated_at) VALUES
(1, 'admin', 'I am the admin', 'Milan, Italy', 'https://www.example.com', '2024-07-21', '2024-07-21'),
(2, 'user', 'I am a user', 'Rome, Italy', 'https://www.example1.com', '2024-07-22', '2024-07-22'),
(3, 'supermario', 'I am Mario', 'Milan, Italy', '', '2024-07-31', '2024-07-31'),
(4, 'luigi', 'I am Luigi', 'Rome, Italy', 'https://www.example2.com', '2024-07-26', '2024-07-26'),
(5, 'giuseppe', 'I am Giuseppe', 'Milan, Italy', '', '2024-08-01', '2024-08-01'),
(6, 'anna', 'I am Anna', 'Rome, Italy', 'https://www.example3.com', '2024-07-29', '2024-07-29'),
(7, 'marco', 'I am Marco', 'Milan, Italy', 'https://www.example4.com', '2024-07-30', '2024-07-30'),
(8, 'laura', 'I am Laura', 'Rome, Italy', '', '2024-07-31', '2024-07-31'),
(9, 'francesco', 'I am Francesco', 'Milan, Italy', 'https://www.example5.com', '2024-07-24', '2024-07-24'),
(10, 'elena', 'I am Elena', 'Rome, Italy', '', '2024-07-31', '2024-07-31');

INSERT INTO post (post_id, username, content, media_path, created_at, updated_at) VALUES
(1, 'admin', 'Hello, World!', '', '2024-07-21', '2024-07-21'),
(2, 'user', 'Hi, there!', '', '2024-07-22', '2024-07-22'),
(3, 'supermario', 'It''s me, Mario!', '', '2024-07-31', '2024-07-31'),
(4, 'luigi', 'It''s me, Luigi!', '', '2024-07-26', '2024-07-26'),
(5, 'giuseppe', 'It''s me, Giuseppe!', '', '2024-08-01', '2024-08-01'),
(6, 'anna', 'It''s me, Anna!', '', '2024-07-29', '2024-07-29'),
(7, 'marco', 'It''s me, Marco!', '', '2024-07-30', '2024-07-30'),
(8, 'laura', 'It''s me, Laura!', '', '2024-07-31', '2024-07-31'),
(9, 'francesco', 'It''s me, Francesco!', '', '2024-07-24', '2024-07-24'),
(10, 'elena', 'It''s me, Elena!', '', '2024-07-31', '2024-07-31'),
(11, 'admin', 'Hello, World! 2', '', '2024-07-21', '2024-07-21'),
(12, 'user', 'Hi, there! 2', '', '2024-07-22', '2024-07-22'),
(13, 'supermario', 'It''s me, Mario! 2', '', '2024-07-31', '2024-07-31'),
(14, 'luigi', 'It''s me, Luigi! 2', '', '2024-07-26', '2024-07-26'),
(15, 'giuseppe', 'It''s me, Giuseppe! 2', '', '2024-08-01', '2024-08-01'),
(16, 'anna', 'It''s me, Anna! 2', '', '2024-07-29', '2024-07-29'),
(17, 'marco', 'It''s me, Marco! 2', '', '2024-07-30', '2024-07-30'),
(18, 'laura', 'It''s me, Laura! 2', '', '2024-07-31', '2024-07-31'),
(19, 'francesco', 'It''s me, Francesco! 2', '', '2024-07-24', '2024-07-24'),
(20, 'elena', 'It''s me, Elena! 2', '', '2024-07-31', '2024-07-31'),
(21, 'admin', 'Hello, World! 3', '', '2024-07-21', '2024-07-21'),
(22, 'user', 'Hi, there! 3', '', '2024-07-22', '2024-07-22'),
(23, 'supermario', 'It''s me, Mario! 3', '', '2024-07-31', '2024-07-31'),
(24, 'luigi', 'It''s me, Luigi! 3', '', '2024-07-26', '2024-07-26'),
(25, 'giuseppe', 'It''s me, Giuseppe! 3', '', '2024-08-01', '2024-08-01'),
(26, 'anna', 'It''s me, Anna! 3', '', '2024-07-29', '2024-07-29'),
(27, 'marco', 'It''s me, Marco! 3', '', '2024-07-30', '2024-07-30'),
(28, 'laura', 'It''s me, Laura! 3', '', '2024-07-31', '2024-07-31'),
(29, 'francesco', 'It''s me, Francesco! 3', '', '2024-07-24', '2024-07-24'),
(30, 'elena', 'It''s me, Elena! 3', '', '2024-07-31', '2024-07-31'),
(31, 'admin', 'Hello, World! 4', '', '2024-07-21', '2024-07-21'),
(32, 'user', 'Hi, there! 4', '', '2024-07-22', '2024-07-22'),
(33, 'supermario', 'It''s me, Mario! 4', '', '2024-07-31', '2024-07-31'),
(34, 'luigi', 'It''s me, Luigi! 4', '', '2024-07-26', '2024-07-26'),
(35, 'giuseppe', 'It''s me, Giuseppe! 4', '', '2024-08-01', '2024-08-01'),
(36, 'anna', 'It''s me, Anna! 4', '', '2024-07-29', '2024-07-29'),
(37, 'marco', 'It''s me, Marco! 4', '', '2024-07-30', '2024-07-30'),
(38, 'laura', 'It''s me, Laura! 4', '', '2024-07-31', '2024-07-31'),
(39, 'francesco', 'It''s me, Francesco! 4', '', '2024-07-24', '2024-07-24'),
(40, 'elena', 'It''s me, Elena! 4', '', '2024-07-31', '2024-07-31'),
(41, 'admin', 'Hello, World! 5', '', '2024-07-21', '2024-07-21'),
(42, 'user', 'Hi, there! 5', '', '2024-07-22', '2024-07-22'),
(43, 'supermario', 'It''s me, Mario! 5', '', '2024-07-31', '2024-07-31'),
(44, 'luigi', 'It''s me, Luigi! 5', '', '2024-07-26', '2024-07-26'),
(45, 'giuseppe', 'It''s me, Giuseppe! 5', '', '2024-08-01', '2024-08-01'),
(46, 'anna', 'It''s me, Anna! 5', '', '2024-07-29', '2024-07-29'),
(47, 'marco', 'It''s me, Marco! 5', '', '2024-07-30', '2024-07-30'),
(48, 'laura', 'It''s me, Laura! 5', '', '2024-07-31', '2024-07-31'),
(49, 'francesco', 'It''s me, Francesco! 5', '', '2024-07-24', '2024-07-24'),
(50, 'elena', 'It''s me, Elena! 5', '', '2024-07-31', '2024-07-31');

INSERT INTO comment (comment_id, post_id, username, content, created_at, updated_at) VALUES
(1, 1, 'user', 'Hi, admin!', '2024-07-21', '2024-07-21'),
(2, 2, 'admin', 'Hello, user!', '2024-07-22', '2024-07-22'),
(3, 3, 'luigi', 'Hi, Mario!', '2024-07-31', '2024-07-31'),
(4, 4, 'supermario', 'Hello, Luigi!', '2024-07-26', '2024-07-26'),
(5, 5, 'anna', 'Hi, Giuseppe!', '2024-08-01', '2024-08-01'),
(6, 6, 'giuseppe', 'Hello, Anna!', '2024-07-29', '2024-07-29'),
(7, 7, 'laura', 'Hi, Marco!', '2024-07-30', '2024-07-30'),
(8, 8, 'marco', 'Hello, Laura!', '2024-07-31', '2024-07-31'),
(9, 9, 'elena', 'Hi, Francesco!', '2024-07-24', '2024-07-24'),
(10, 10, 'francesco', 'Hello, Elena!', '2024-07-31', '2024-07-31'),
(11, 1, 'user', 'Hi, admin! 2', '2024-07-21', '2024-07-21'),
(12, 2, 'admin', 'Hello, user! 2', '2024-07-22', '2024-07-22'),
(13, 3, 'luigi', 'Hi, Mario! 2', '2024-07-31', '2024-07-31'),
(14, 4, 'supermario', 'Hello, Luigi! 2', '2024-07-26', '2024-07-26'),
(15, 5, 'anna', 'Hi, Giuseppe! 2', '2024-08-01', '2024-08-01'),
(16, 6, 'giuseppe', 'Hello, Anna! 2', '2024-07-29', '2024-07-29'),
(17, 7, 'laura', 'Hi, Marco! 2', '2024-07-30', '2024-07-30'),
(18, 8, 'marco', 'Hello, Laura! 2', '2024-07-31', '2024-07-31'),
(19, 9, 'elena', 'Hi, Francesco! 2', '2024-07-24', '2024-07-24'),
(20, 10, 'francesco', 'Hello, Elena! 2', '2024-07-31', '2024-07-31'),
(21, 1, 'user', 'Hi, admin! 3', '2024-07-21', '2024-07-21'),
(22, 2, 'admin', 'Hello, user! 3', '2024-07-22', '2024-07-22'),
(23, 3, 'luigi', 'Hi, Mario! 3', '2024-07-31', '2024-07-31'),
(24, 4, 'supermario', 'Hello, Luigi! 3', '2024-07-26', '2024-07-26'),
(25, 5, 'anna', 'Hi, Giuseppe! 3', '2024-08-01', '2024-08-01'),
(26, 6, 'giuseppe', 'Hello, Anna! 3', '2024-07-29', '2024-07-29'),
(27, 7, 'laura', 'Hi, Marco! 3', '2024-07-30', '2024-07-30'),
(28, 8, 'marco', 'Hello, Laura! 3', '2024-07-31', '2024-07-31'),
(29, 9, 'elena', 'Hi, Francesco! 3', '2024-07-24', '2024-07-24'),
(30, 10, 'francesco', 'Hello, Elena! 3', '2024-07-31', '2024-07-31'),
(31, 1, 'user', 'Hi, admin! 4', '2024-07-21', '2024-07-21'),
(32, 2, 'admin', 'Hello, user! 4', '2024-07-22', '2024-07-22'),
(33, 3, 'luigi', 'Hi, Mario! 4', '2024-07-31', '2024-07-31'),
(34, 4, 'supermario', 'Hello, Luigi! 4', '2024-07-26', '2024-07-26'),
(35, 5, 'anna', 'Hi, Giuseppe! 4', '2024-08-01', '2024-08-01'),
(36, 6, 'giuseppe', 'Hello, Anna! 4', '2024-07-29', '2024-07-29'),
(37, 7, 'laura', 'Hi, Marco! 4', '2024-07-30', '2024-07-30'),
(38, 8, 'marco', 'Hello, Laura! 4', '2024-07-31', '2024-07-31'),
(39, 9, 'elena', 'Hi, Francesco! 4', '2024-07-24', '2024-07-24'),
(40, 10, 'francesco', 'Hello, Elena! 4', '2024-07-31', '2024-07-31'),
(41, 1, 'user', 'Hi, admin! 5', '2024-07-21', '2024-07-21'),
(42, 2, 'admin', 'Hello, user! 5', '2024-07-22', '2024-07-22'),
(43, 3, 'luigi', 'Hi, Mario! 5', '2024-07-31', '2024-07-31'),
(44, 4, 'supermario', 'Hello, Luigi! 5', '2024-07-26', '2024-07-26'),
(45, 5, 'anna', 'Hi, Giuseppe! 5', '2024-08-01', '2024-08-01'),
(46, 6, 'giuseppe', 'Hello, Anna! 5', '2024-07-29', '2024-07-29'),
(47, 7, 'laura', 'Hi, Marco! 5', '2024-07-30', '2024-07-30'),
(48, 8, 'marco', 'Hello, Laura! 5', '2024-07-31', '2024-07-31'),
(49, 9, 'elena', 'Hi, Francesco! 5', '2024-07-24', '2024-07-24'),
(50, 10, 'francesco', 'Hello, Elena! 5', '2024-07-31', '2024-07-31');

INSERT INTO friendship (friendship_id, username_1, username_2, status, created_at, updated_at) VALUES
(1, 'admin', 'user', 'accepted', '2024-07-21', '2024-07-21'),
(2, 'user', 'supermario', 'sent', '2024-07-22', '2024-07-22'),
(3, 'supermario', 'luigi', 'accepted', '2024-07-31', '2024-07-31'),
(4, 'luigi', 'giuseppe', 'accepted', '2024-07-26', '2024-07-26'),
(5, 'giuseppe', 'anna', 'sent', '2024-08-01', '2024-08-01'),
(6, 'anna', 'marco', 'accepted', '2024-07-29', '2024-07-29'),
(7, 'marco', 'laura', 'accepted', '2024-07-30', '2024-07-30'),
(8, 'laura', 'francesco', 'accepted', '2024-07-31', '2024-07-31'),
(9, 'francesco', 'elena', 'sent', '2024-07-24', '2024-07-24'),
(10, 'elena', 'admin', 'accepted', '2024-07-31', '2024-07-31'),
(11, 'admin', 'supermario', 'accepted', '2024-07-21', '2024-07-21'),
(12, 'user', 'luigi', 'sent', '2024-07-22', '2024-07-22'),
(13, 'supermario', 'giuseppe', 'accepted', '2024-07-31', '2024-07-31'),
(14, 'luigi', 'anna', 'accepted', '2024-07-26', '2024-07-26'),
(15, 'giuseppe', 'marco', 'accepted', '2024-08-01', '2024-08-01'),
(16, 'anna', 'laura', 'sent', '2024-07-29', '2024-07-29'),
(17, 'marco', 'francesco', 'accepted', '2024-07-30', '2024-07-30'),
(18, 'laura', 'elena', 'accepted', '2024-07-31', '2024-07-31'),
(19, 'francesco', 'admin', 'accepted', '2024-07-24', '2024-07-24'),
(20, 'elena', 'user', 'sent', '2024-07-31', '2024-07-31');

INSERT INTO reaction (reaction_id, post_id, username, type, created_at, updated_at) VALUES
(1, 1, 'user', 'like', '2024-07-21', '2024-07-21'),
(2, 2, 'admin', 'like', '2024-07-22', '2024-07-22'),
(3, 3, 'luigi', 'like', '2024-07-31', '2024-07-31'),
(4, 4, 'supermario', 'like', '2024-07-26', '2024-07-26'),
(5, 5, 'anna', 'like', '2024-08-01', '2024-08-01'),
(6, 6, 'giuseppe', 'like', '2024-07-29', '2024-07-29'),
(7, 7, 'laura', 'like', '2024-07-30', '2024-07-30'),
(8, 8, 'marco', 'like', '2024-07-31', '2024-07-31'),
(9, 9, 'elena', 'like', '2024-07-24', '2024-07-24'),
(10, 10, 'francesco', 'like', '2024-07-31', '2024-07-31'),
(11, 1, 'user', 'like', '2024-07-21', '2024-07-21'),
(12, 2, 'admin', 'like', '2024-07-22', '2024-07-22'),
(13, 3, 'luigi', 'like', '2024-07-31', '2024-07-31'),
(14, 4, 'supermario', 'like', '2024-07-26', '2024-07-26'),
(15, 5, 'anna', 'like', '2024-08-01', '2024-08-01'),
(16, 6, 'giuseppe', 'like', '2024-07-29', '2024-07-29'),
(17, 7, 'laura', 'like', '2024-07-30', '2024-07-30'),
(18, 8, 'marco', 'like', '2024-07-31', '2024-07-31'),
(19, 9, 'elena', 'like', '2024-07-24', '2024-07-24'),
(20, 10, 'francesco', 'like', '2024-07-31', '2024-07-31'),
(21, 1, 'user', 'like', '2024-07-21', '2024-07-21'),
(22, 2, 'admin', 'like', '2024-07-22', '2024-07-22'),
(23, 3, 'luigi', 'like', '2024-07-31', '2024-07-31'),
(24, 4, 'supermario', 'like', '2024-07-26', '2024-07-26'),
(25, 5, 'anna', 'like', '2024-08-01', '2024-08-01'),
(26, 6, 'giuseppe', 'like', '2024-07-29', '2024-07-29'),
(27, 7, 'laura', 'like', '2024-07-30', '2024-07-30'),
(28, 8, 'marco', 'like', '2024-07-31', '2024-07-31'),
(29, 9, 'elena', 'like', '2024-07-24', '2024-07-24'),
(30, 10, 'francesco', 'like', '2024-07-31', '2024-07-31'),
(31, 1, 'user', 'like', '2024-07-21', '2024-07-21'),
(32, 2, 'admin', 'like', '2024-07-22', '2024-07-22'),
(33, 3, 'luigi', 'like', '2024-07-31', '2024-07-31'),
(34, 4, 'supermario', 'like', '2024-07-26', '2024-07-26');

INSERT INTO message (message_id, sender_id, receiver_id, content, created_at, updated_at) VALUES
(1, 'admin', 'user', 'Hi, user!', '2024-07-21', '2024-07-21'),
(2, 'user', 'admin', 'Hello, admin!', '2024-07-22', '2024-07-22'),
(3, 'supermario', 'luigi', 'Hi, Luigi!', '2024-07-31', '2024-07-31'),
(4, 'luigi', 'supermario', 'Hello, Mario!', '2024-07-26', '2024-07-26'),
(5, 'giuseppe', 'anna', 'Hi, Anna!', '2024-08-01', '2024-08-01'),
(6, 'anna', 'giuseppe', 'Hello, Giuseppe!', '2024-07-29', '2024-07-29'),
(7, 'marco', 'laura', 'Hi, Laura!', '2024-07-30', '2024-07-30'),
(8, 'laura', 'marco', 'Hello, Marco!', '2024-07-31', '2024-07-31'),
(9, 'francesco', 'elena', 'Hi, Elena!', '2024-07-24', '2024-07-24'),
(10, 'elena', 'admin', 'Hello, Francesco!', '2024-07-31', '2024-07-31'),
(11, 'admin', 'user', 'Hi, user! 2', '2024-07-21', '2024-07-21'),
(12, 'user', 'admin', 'Hello, admin! 2', '2024-07-22', '2024-07-22'),
(13, 'supermario', 'luigi', 'Hi, Luigi! 2', '2024-07-31', '2024-07-31'),
(14, 'luigi', 'supermario', 'Hello, Mario! 2', '2024-07-26', '2024-07-26'),
(15, 'giuseppe', 'anna', 'Hi, Anna! 2', '2024-08-01', '2024-08-01');

INSERT INTO book (book_id, username, title, author, genre, year, description, cover_path, created_at, updated_at, price) VALUES
(1, 'admin', 'Introduzione agli Algoritmi', 'Thomas H. Cormen', 'Informatica', 2009, 'Un libro completo sugli algoritmi.', '/covers/intro_to_algorithms.jpg', '2024-07-21', '2024-07-21', 50.00),
(2, 'luigi', 'Intelligenza Artificiale: Un Approccio Moderno', 'Stuart Russell', 'Informatica', 2010, 'Un libro sull\'intelligenza artificiale.', '/covers/ai_modern_approach.jpg', '2024-07-22', '2024-07-22', 45.00),
(3, 'admin', 'Concetti di Sistemi di Database', 'Abraham Silberschatz', 'Informatica', 2011, 'Un libro sui sistemi di database.', '/covers/db_system_concepts.jpg', '2024-07-23', '2024-07-23', 55.00),
(4, 'user', 'Concetti di Sistemi Operativi', 'Abraham Silberschatz', 'Informatica', 2012, 'Un libro sui sistemi operativi.', '/covers/os_concepts.jpg', '2024-07-24', '2024-07-24', 60.00),
(5, 'user', 'Reti di Calcolatori', 'Andrew S. Tanenbaum', 'Informatica', 2013, 'Un libro sulle reti di calcolatori.', '/covers/computer_networks.jpg', '2024-07-25', '2024-07-25', 40.00),
(6, 'admin', 'Matematica Discreta e le sue Applicazioni', 'Kenneth H. Rosen', 'Matematica', 2014, 'Un libro sulla matematica discreta.', '/covers/discrete_mathematics.jpg', '2024-07-26', '2024-07-26', 65.00),
(7, 'luigi', 'Calcolo: Funzioni Trascendenti', 'James Stewart', 'Matematica', 2015, 'Un libro sul calcolo.', '/covers/calculus.jpg', '2024-07-27', '2024-07-27', 70.00),
(8, 'admin', 'Fisica per Scienziati e Ingegneri', 'Raymond A. Serway', 'Fisica', 2016, 'Un libro sulla fisica.', '/covers/physics.jpg', '2024-07-28', '2024-07-28', 75.00),
(9, 'user', 'Chimica Organica', 'Paula Yurkanis Bruice', 'Chimica', 2017, 'Un libro sulla chimica organica.', '/covers/organic_chemistry.jpg', '2024-07-29', '2024-07-29', 80.00),
(10, 'giuseppe', 'Principi di Microeconomia', 'N. Gregory Mankiw', 'Economia', 2018, 'Un libro sulla microeconomia.', '/covers/microeconomics.jpg', '2024-07-30', '2024-07-30', 85.00),
(11, 'admin', 'Storia della Filosofia Occidentale', 'Bertrand Russell', 'Umanistico', 1945, 'Un libro che esplora la storia della filosofia occidentale.', '/covers/storia_filosofia_occidentale.jpg', '2024-08-01', '2024-08-01', 90.00),
(12, 'giuseppe', 'La Divina Commedia', 'Dante Alighieri', 'Umanistico', 1320, 'Un poema epico scritto da Dante Alighieri.', '/covers/divina_commedia.jpg', '2024-08-02', '2024-08-02', 95.00),
(13, 'admin', 'Guerra e Pace', 'Lev Tolstoj', 'Umanistico', 1869, 'Un romanzo storico che narra le vicende di diverse famiglie russe durante le guerre napoleoniche.', '/covers/guerra_e_pace.jpg', '2024-08-03', '2024-08-03', 100.00),
(14, 'giuseppe', 'Il Principe', 'Niccolò Machiavelli', 'Umanistico', 1532, 'Un trattato politico scritto da Niccolò Machiavelli.', '/covers/il_principe.jpg', '2024-08-04', '2024-08-04', 105.00),
(15, 'giuseppe', 'L\'Interpretazione dei Sogni', 'Sigmund Freud', 'Umanistico', 1899, 'Un libro che esplora la teoria dei sogni di Sigmund Freud.', '/covers/interpretazione_sogni.jpg', '2024-08-05', '2024-08-05', 110.00),
(16, 'admin', 'Manuale di Psichiatria', 'Giovanni Battista Cassano', 'Medico', 2010, 'Un manuale completo di psichiatria.', '/covers/manuale_psichiatria.jpg', '2024-08-06', '2024-08-06', 115.00),
(17, 'anna', 'Anatomia Umana', 'Frank H. Netter', 'Medico', 2014, 'Un atlante di anatomia umana.', '/covers/anatomia_umana.jpg', '2024-08-07', '2024-08-07', 120.00),
(18, 'admin', 'Principi di Medicina Interna', 'Harrison', 'Medico', 2018, 'Un libro di riferimento per la medicina interna.', '/covers/principi_medicina_interna.jpg', '2024-08-08', '2024-08-08', 125.00),
(19, 'anna', 'Fisiologia Medica', 'Guyton e Hall', 'Medico', 2016, 'Un libro di testo sulla fisiologia medica.', '/covers/fisiologia_medica.jpg', '2024-08-09', '2024-08-09', 130.00),
(20, 'admin', 'Patologia Generale', 'Robbins e Cotran', 'Medico', 2015, 'Un libro di testo sulla patologia generale.', '/covers/patologia_generale.jpg', '2024-08-10', '2024-08-10', 135.00),
(21, 'user', 'Farmacologia', 'Rang e Dale', 'Medico', 2019, 'Un libro di testo sulla farmacologia.', '/covers/farmacologia.jpg', '2024-08-11', '2024-08-11', 140.00),
(22, 'giuseppe', 'Microbiologia Medica', 'Murray', 'Medico', 2020, 'Un libro di testo sulla microbiologia medica.', '/covers/microbiologia_medica.jpg', '2024-08-12', '2024-08-12', 145.00),
(23, 'admin', 'Immunologia', 'Abbas', 'Medico', 2017, 'Un libro di testo sull\'immunologia.', '/covers/immunologia.jpg', '2024-08-13', '2024-08-13', 150.00),
(24, 'user', 'Chirurgia Generale', 'Sabiston', 'Medico', 2018, 'Un libro di testo sulla chirurgia generale.', '/covers/chirurgia_generale.jpg', '2024-08-14', '2024-08-14', 155.00),
(25, 'admin', 'Diagnostica per Immagini', 'Brant e Helms', 'Medico', 2021, 'Un libro di testo sulla diagnostica per immagini.', '/covers/diagnostica_per_immagini.jpg', '2024-08-15', '2024-08-15', 160.00);

INSERT INTO likes (post_id, username, created_at) VALUES
(1, 'user', '2024-07-21'),
(2, 'admin', '2024-07-22'),
(3, 'luigi', '2024-07-31'),
(4, 'supermario', '2024-07-26'),
(5, 'anna', '2024-08-01'),
(6, 'giuseppe', '2024-07-29'),
(7, 'laura', '2024-07-30'),
(8, 'marco', '2024-07-31'),
(9, 'elena', '2024-07-24'),
(10, 'francesco', '2024-07-31'),
(11, 'user', '2024-07-21'),
(12, 'admin', '2024-07-22'),
(13, 'luigi', '2024-07-31'),
(14, 'supermario', '2024-07-26'),
(15, 'anna', '2024-08-01'),
(16, 'giuseppe', '2024-07-29'),
(17, 'laura', '2024-07-30'),
(18, 'marco', '2024-07-31'),
(19, 'elena', '2024-07-24'),
(20, 'francesco', '2024-07-31'),
(21, 'user', '2024-07-21'),
(22, 'admin', '2024-07-22'),
(23, 'luigi', '2024-07-31'),
(24, 'supermario', '2024-07-26'),
(25, 'anna', '2024-08-01'),
(26, 'giuseppe', '2024-07-29'),
(27, 'laura', '2024-07-30'),
(28, 'marco', '2024-07-31'),
(29, 'elena', '2024-07-24'),
(30, 'francesco', '2024-07-31'),
(31, 'user', '2024-07-21'),
(32, 'admin', '2024-07-22');

INSERT INTO room (id, name, code, created_at, created_by) VALUES
(1, 'Corso di Informatica', 'informatica', '2024-07-21', 'admin'),
(2, 'Corso di Matematica', 'matematica', '2024-07-22', 'user'),
(3, 'Corso di Fisica', 'fisica', '2024-07-23', 'admin'),
(4, 'Corso di Chimica', 'chimica', '2024-07-24', 'user'),
(5, 'Corso di Economia', 'economia', '2024-07-25', 'anna'),
(6, 'Corso di Filosofia', 'filosofia', '2024-07-26', 'admin'),
(7, 'Corso di Medicina', 'medicina', '2024-07-27', 'user'),
(8, 'Corso di Umanistico', 'umanistico', '2024-07-28', 'giuseppe');

DELIMITER //

CREATE TRIGGER user_ban_update
    BEFORE UPDATE ON user
    FOR EACH ROW
BEGIN
    IF NEW.banned = TRUE AND OLD.banned = FALSE THEN
        SET NEW.ban_start = CURRENT_TIMESTAMP;
    ELSEIF NEW.banned = FALSE AND OLD.banned = TRUE THEN
        SET NEW.ban_reason = NULL;
        SET NEW.ban_start = NULL;
    END IF;
END;

//

DELIMITER ;