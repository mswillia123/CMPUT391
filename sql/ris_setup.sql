-- Database creation script
@setup
-- Set inverted indexes on table radiology_search to update automatically
@drjobdml name_index 10
@drjobdml diagnosis_index 10
@drjobdml description_index 10
-- Populate database with data
@ris_dat