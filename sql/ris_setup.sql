-- Database creation script
@setup
-- Set inverted indexes on table radiology_search to update automatically
@drjobdml name_index 1
@drjobdml diagnosis_index 1
@drjobdml description_index 1
-- Populate database with data
@ris_dat
