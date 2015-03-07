/*
 *  File name:  ris_dat.sql
 *  Function:   Inserts data into the Radiology Information System database
 *  Author:     Costa Zervos
 */

/*
 *	Insert data into persons table
 */
INSERT INTO persons VALUES
(1, 'Leonardo', 'Turtle', '123 Sewer Street', 'leo@tmnt.com', '555-1234');
INSERT INTO persons VALUES
(2, 'Donatello', 'Turtle', '123 Sewer Street', 'don@tmnt.com', '555-1234');
INSERT INTO persons VALUES
(3, 'Raphael', 'Turtle', '123 Sewer Street', 'ralph@tmnt.com', '555-1234');
INSERT INTO persons VALUES
(4, 'Michelangelo', 'Turtle', '123 Sewer Street', 'mike@tmnt.com', '555-1234');

/*
 *	Insert data into users table
 */

-- Administrators
INSERT INTO users VALUES
('leo', 'leo123', 'a', 1, TO_DATE('20150307', 'YYYYMMDD'));

-- Doctors
INSERT INTO users VALUES
('don', 'don123', 'd', 2, TO_DATE('20150307', 'YYYYMMDD'));

-- Radiologists
INSERT INTO users VALUES
('ralph', 'ralph123', 'r', 3, TO_DATE('20150307', 'YYYYMMDD'));

-- Patients
INSERT INTO users VALUES
('mike', 'mike123', 'p', 4, TO_DATE('20150307', 'YYYYMMDD'));

/*
 *	Insert data into family_doctor table
 */

-- Patients belonging to Donatello (2)
INSERT INTO family_doctor VALUES (2, 4);
