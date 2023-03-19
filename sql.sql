CREATE TABLE author(
    id INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    username varchar(20) NOT NULL,
    email varchar(20) NOT NULL,
    pas varchar(20) NOT NULL,
    reg_date date DEFAULT NULL,
    deleted TINYINT(1) DEFAULT NULL
);
CREATE TABLE redactor(
    id INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    username varchar(20) NOT NULL,
    passwd varchar(20) NOT NULL,
    email varchar(20) NOT NULL
);
CREATE TABLE corrector(
    id INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    username varchar(20) NOT NULL,
    passwd varchar(20) NOT NULL,
    email varchar(20) NOT NULL
);
CREATE TABLE version(
    id INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    name varchar(20),
    stat INT NOT NULL,
    dat DATE NOT NULL,
    version_number INT NOT NULL
);
CREATE TABLE send_recive(
    id_a INT NOT NULL,
    FOREIGN KEY (id_a)  REFERENCES author(id) ON DELETE CASCADE,
    id_ver INT NOT NULL,
    FOREIGN KEY (id_ver)  REFERENCES version(id) ON DELETE CASCADE,
    sends BOOLEAN DEFAULT NULL
);
CREATE TABLE problem_list(
    id INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    txt TEXT,
    id_ver INT NOT NULL,
    FOREIGN KEY (id_ver)  REFERENCES version (id) ON DELETE CASCADE
 
);
CREATE TABLE problem_list_corr(
    id INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    txt TEXT,
    id_ver INT NOT NULL,
    FOREIGN KEY (id_ver)  REFERENCES version (id) ON DELETE CASCADE

);
CREATE TABLE version_corrector(
    id_ver INT NOT NULL,
    FOREIGN KEY (id_ver)  REFERENCES version (id) ON DELETE CASCADE,
    id_cor INT NOT NULL,
    FOREIGN KEY (id_cor)  REFERENCES corrector(id) ON DELETE CASCADE

);

CREATE TABLE fills(
    id_ver INT NOT NULL,
    FOREIGN KEY (id_ver)  REFERENCES version (id) ON DELETE CASCADE,
    id_plan INT NOT NULL,
    FOREIGN KEY (id_plan)  REFERENCES plan_num (id)ON DELETE CASCADE
    
);
CREATE TABLE plan_num(
    id INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    name varchar(20),
    number_of_articles INT NOT NULL,
    fulled BOOLEAN
);

CREATE TABLE allinformation(
    active_articles INT DEFAULT NULL,
    written_articles INT DEFAULT NULL,
    plans INT DEFAULT NULL
);
CREATE TABLE prev_next_ver(
    id_prev INT,
    FOREIGN KEY (id_prev)  REFERENCES version (id) ON DELETE CASCADE,
    id_next INT,
    FOREIGN KEY (id_next)  REFERENCES version (id) ON DELETE CASCADE

);
CREATE TABLE version_corrector(
    id_ver INT NOT NULL,
    FOREIGN KEY (id_ver)  REFERENCES version (id) ON DELETE CASCADE,
    id_cor INT NOT NULL,
    FOREIGN KEY (id_cor)  REFERENCES corrector(id) ON DELETE CASCADE

);
CREATE TABLE version_redactor(
    id_ver INT NOT NULL,
    FOREIGN KEY (id_ver)  REFERENCES version (id) ON DELETE CASCADE,
    id_red INT NOT NULL,
    FOREIGN KEY (id_red)  REFERENCES redactor(id) ON DELETE CASCADE
);
CREATE TABLE review(
    id INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    ver_id INT NOT NULL,
    FOREIGN KEY (ver_id)  REFERENCES version (id) ON DELETE CASCADE,
    rev_id INT NOT NULL,
    FOREIGN KEY (rev_id)  REFERENCES reviewer (id) ON DELETE CASCADE
);
CREATE TABLE approved_list(
    id INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    id_ver INT NOT NULL,
    FOREIGN KEY (id_ver)  REFERENCES version (id) ON DELETE CASCADE
 
);

INSERT INTO redactor(username, passwd, step, email) VALUES
    ('redactor1', '12345',  'vfk@mail.ru'),
    ('redactor2', '123456', 'mvf@mail.ru');