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
 

/*
 *	Insert data into users table
 */
INSERT INTO users VALUES
('admin', 'admin', 'a', 1, TO_DATE('20150307', 'YYYYMMDD'));

commit;
