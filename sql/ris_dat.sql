/*
 *  File name:  ris_dat.sql
 *  Function:   Inserts data into the Radiology Information System database
 *  Author:     Costa Zervos
 */

/*
 *	Insert data into persons table
 */
INSERT INTO persons VALUES
(1, 'Administrator', 'Administrator', 'Administrator', 'admin@admin.com', '555-1234');
 
/* Old Test Data
INSERT INTO persons VALUES
(1, 'Leonardo', 'Turtle', '123 Sewer Street', 'leo@tmnt.com', '555-1234');
INSERT INTO persons VALUES
(2, 'Donatello', 'Turtle', '123 Sewer Street', 'don@tmnt.com', '555-1234');
INSERT INTO persons VALUES
(3, 'Raphael', 'Turtle', '123 Sewer Street', 'ralph@tmnt.com', '555-1234');
INSERT INTO persons VALUES
(4, 'Michelangelo', 'Turtle', '123 Sewer Street', 'mike@tmnt.com', '555-1234');
INSERT INTO persons VALUES
(5, 'Peter', 'Venkman', '123 Manhattan Avenue', 'peter@ghostbusters.com', '555-5678');
INSERT INTO persons VALUES
(6, 'Egon', 'Spengler', '123 Manhattan Avenue', 'egon@ghostbusters.com', '555-5678');
INSERT INTO persons VALUES
(7, 'Raymond', 'Stantz', '123 Manhattan Avenue', 'ray@ghostbusters.com', '555-5678');
INSERT INTO persons VALUES
(8, 'Winston', 'Zeddmore', '123 Manhattan Avenue', 'winston@ghostbusters.com', '555-5678');
*/

/*
 *	Insert data into users table
 */
 INSERT INTO users VALUES
('admin', 'admin123', 'a', 1, TO_DATE('20150307', 'YYYYMMDD'));
 
/* Old Test Data
-- Administrators
INSERT INTO users VALUES
('leo', 'leo123', 'a', 1, TO_DATE('20150307', 'YYYYMMDD'));

-- Doctors
INSERT INTO users VALUES
('don', 'don123', 'd', 2, TO_DATE('20150307', 'YYYYMMDD'));
INSERT INTO users VALUES
('peter', 'peter123', 'd', 5, TO_DATE('20150307', 'YYYYMMDD'));

-- Radiologists
INSERT INTO users VALUES
('ralph', 'ralph123', 'r', 3, TO_DATE('20150307', 'YYYYMMDD'));
INSERT INTO users VALUES
('egon', 'egon123', 'r', 6, TO_DATE('20150307', 'YYYYMMDD'));

-- Patients
INSERT INTO users VALUES
('mike', 'mike123', 'p', 4, TO_DATE('20150307', 'YYYYMMDD'));
INSERT INTO users VALUES
('ray', 'ray123', 'p', 7, TO_DATE('20150307', 'YYYYMMDD'));
INSERT INTO users VALUES
('winston', 'winston123', 'p', 8, TO_DATE('20150307', 'YYYYMMDD'));
*/

/*
 *	Insert data into family_doctor table
 *
 *  (doctor_id, patient_id)
 */

/* Old Test Data
-- Patients belonging to Donatello (2)
INSERT INTO family_doctor VALUES (2, 4);

-- Patients belonging to Peter (5)
INSERT INTO family_doctor VALUES (5, 7);
INSERT INTO family_doctor VALUES (5, 8);
*/

/*
 *	Insert data into radiology_record table
 *
 *  (record_id, patient_id, doctor_id, radiologist_id, test_type,
 *  prescribing_date, test_date, diagnosis, description)
 */
 
/* Old Test Data
INSERT INTO radiology_record VALUES
(1, 4, 2, 3, 'X-Ray', TO_DATE('20150307', 'YYYYMMDD'), TO_DATE('20150308', 'YYYYMMDD'), 'Broken arm', 'Scan of right arm.');
INSERT INTO radiology_record VALUES
(2, 7, 5, 6, 'X-Ray', TO_DATE('20151101', 'YYYYMMDD'), TO_DATE('20151210', 'YYYYMMDD'), 'Broken arm', 'Scan of right arm.');
INSERT INTO radiology_record VALUES
(3, 8, 5, 6, 'X-Ray', TO_DATE('20160102', 'YYYYMMDD'), TO_DATE('20160110', 'YYYYMMDD'), 'Broken arm', 'Scan of right arm.');
INSERT INTO radiology_record VALUES
(4, 4, 2, 3, 'X-Ray', TO_DATE('20150307', 'YYYYMMDD'), TO_DATE('20160202', 'YYYYMMDD'), 'Broken arm', 'Scan of right arm.');
*/

/*
 *	Insert data into radiology_search table
 *  
 *  (record_id, patient_id, patient_name, diagnosis, description)
 */
 
/* Old Test Data
DECLARE 
    patient_name varchar(49);
    rs_diagnosis varchar(128);
    rs_description varchar(1024);

BEGIN
    -- Record 1
    SELECT first_name || ' ' || last_name INTO patient_name
    FROM   persons
    WHERE  person_id = 4;
    
    SELECT diagnosis INTO rs_diagnosis
    FROM   radiology_record
    WHERE  record_id = 1;

    SELECT description INTO rs_description
    FROM   radiology_record
    WHERE  record_id = 1;
    
    INSERT INTO radiology_search VALUES
    (1, 4, patient_name, rs_diagnosis, rs_description);
    
    -- Record 2
    SELECT first_name || ' ' || last_name INTO patient_name
    FROM   persons
    WHERE  person_id = 7;
    
    SELECT diagnosis INTO rs_diagnosis
    FROM   radiology_record
    WHERE  record_id = 2;

    SELECT description INTO rs_description
    FROM   radiology_record
    WHERE  record_id = 2;
    
    INSERT INTO radiology_search VALUES
    (2, 7, patient_name, rs_diagnosis, rs_description);
    
    -- Record 3
    SELECT first_name || ' ' || last_name INTO patient_name
    FROM   persons
    WHERE  person_id = 8;
    
    SELECT diagnosis INTO rs_diagnosis
    FROM   radiology_record
    WHERE  record_id = 3;

    SELECT description INTO rs_description
    FROM   radiology_record
    WHERE  record_id = 3;
    
    INSERT INTO radiology_search VALUES
    (3, 8, patient_name, rs_diagnosis, rs_description);
    
    -- Record 4
    SELECT first_name || ' ' || last_name INTO patient_name
    FROM   persons
    WHERE  person_id = 4;
    
    SELECT diagnosis INTO rs_diagnosis
    FROM   radiology_record
    WHERE  record_id = 4;

    SELECT description INTO rs_description
    FROM   radiology_record
    WHERE  record_id = 4;
    
    INSERT INTO radiology_search VALUES
    (4, 4, patient_name, rs_diagnosis, rs_description);
    
END;
/
*/
commit;
