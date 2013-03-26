CREATE TABLE authuser (
    uid  INT NOT NULL AUTO_INCREMENT UNIQUE
    , created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    , updated_on TIMESTAMP
    , username VARCHAR(100)
    , password VARCHAR(32)
    , salutation VARCHAR(5)
    , title VARCHAR(20)
    , firstname VARCHAR(100)
    , lastname VARCHAR(100)
    , street VARCHAR(100)
    , zip VARCHAR(20)
    , city VARCHAR(100)
    , telephone VARCHAR(20)
    , email VARCHAR(255)
    , homepage VARCHAR(100)
    , auth_url_id VARCHAR(255)
    , auth_url_clicked_on TIMESTAMP
);
ALTER TABLE authuser ADD PRIMARY KEY (uid, username);
