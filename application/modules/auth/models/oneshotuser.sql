CREATE TABLE oneshotuser (
    email VARCHAR(255)
    , created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    , updated_on TIMESTAMP
    , name VARCHAR(255)
    , auth_url_id VARCHAR(255)
    , auth_url_clicked_on TIMESTAMP
    , zendaction VARCHAR(50)
    , zendcontroller VARCHAR(50)
    , zendmodule VARCHAR(50)
    , zendparams VARCHAR(255)
);
ALTER TABLE oneshotuser ADD PRIMARY KEY (email);
