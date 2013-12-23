CREATE DATABASE monroeminutes;

GRANT USAGE ON monroeminutes.* TO mmuser IDENTIFIED BY 'password123%%%';

GRANT ALL PRIVILEGES ON monroeminutes.* TO mmuser;

USE monroeminutes;

CREATE TABLE IF NOT EXISTS urls(
    urlid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    targeturl TEXT NOT NULL,
    title CHAR(255) NOT NULL,
    description TEXT NOT NULL,
    maxlinklevel INT NOT NULL,
    creationdatetime DATETIME NOT NULL,
    doctype CHAR(127) NOT NULL,
    frequency INT NOT NULL
);
CREATE INDEX urls_urlid ON urls(urlid);

CREATE TABLE IF NOT EXISTS orgs(
    orgid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name CHAR(255) NOT NULL,
    description TEXT NOT NULL,
    creationdatetime DATETIME NOT NULL,
    matchtext TEXT NOT NULL,
    urlid INT NOT NULL,
    FOREIGN KEY (urlid) REFERENCES url(urlid),
    bodyid INT NOT NULL,
    FOREIGN KEY (bodyid) REFERENCES bodies(bodyid)
);
CREATE INDEX orgs_orgid ON orgs(orgid);

CREATE TABLE IF NOT EXISTS bodies(
    bodyid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name CHAR(255) NOT NULL,
    description TEXT NOT NULL,
    creationdatatime DATETIME NOT NULL
);
CREATE INDEX bodies_bodyid ON bodies(bodyid);

CREATE TABLE IF NOT EXISTS docs(
    docid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    docurl TEXT NOT NULL,
    linktext CHAR(255) NOT NULL,
    urlid INT NOT NULL,
    FOREIGN KEY (urlid) REFERENCES urls(urlid),
    creationdatetime DATETIME NOT NULL,
    pdfhash CHAR(255) NOT NULL,
    textfilename TEXT NOT NULL,
    pdffilename TEXT NOT NULL,
    orgid INT NOT NULL,
    FOREIGN KEY (orgid) REFERNCES orgs(orgid)
);
CREATE INDEX docs_docid ON docs(docid);

