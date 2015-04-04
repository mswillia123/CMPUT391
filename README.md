# CMPUT391


------------
Installation
------------
1. Extract files into Apache web server directory.
2. Change permissions to 755 or 644 of all files using chmod.
3. Launch SQL plus and run the database installation file: @ris_setup
4. Open PHPconnectionDB.php and enter your OracleDB credentials.
5. Open loginModule.php in a browser to access the website.


------------
Login Module
------------
To login:
    1. Enter a valid username.
    2. Enter the password for that username.
    3. Click OK
    
To edit user's information:
    1. After logging in, select the "Edit" link next to the user's information
       at the top of the page.
    2. Edit any of the user's details and select OK.
    
To logout:
    1. After logging in, select the "Logout" link next to the user's information
       at the top of the page.
       
       
------------------------
Report Generating Module
------------------------
To generate a report:
    1. Only an administrator can generate a report.
    2. Login as an administrator, which will take you to the administrator menu.
    3. Select the "Report Generator" link.
    4. Enter the diagnosis you wish to generate a report on.
    5. Enter a starting and ending date for the time interval you wish to
       generate a report on. The date must be entered in the following format:
       yyyy-mm-dd where y is the year, m is the month, and d is the date, each
       as numbers.
       
       
-------------
Search Module
-------------
To search for reports:
    1. After logging in, if you are an administrator or a radiologist, select
       the "Search" link in your respective menu. Otherwise, doctor and patient
       users will be taken directly to the search module.
    2. Enter search keywords and/or a time period to search.
    3. Select a date reference, ie which date attribute you wish the time period
       and the ordering to be applied to.
    4. Select a search result order:
       a. Relevance: will order your results by the records that are most
          relevant to your keywords (REQUIRES keywords to be entered).
       b. Date (Newest to Oldest): orders the results by newest date to the
          oldest date. Applied to either test date or prescription date
          depending on which date reference was selected in step 3. 
       c. Date (Oldest to Newest): orders the results by oldest date to the
          newest date. Applied to either test date or prescription date
          depending on which date reference was selected in step 3.

To view images:
    1. After getting search results, click on a thumbnail to see the regular
       size of that image in a new window.
    2. Click on that regular sized image to see the full sized image.