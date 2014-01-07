Stanford Student Center
=======================

This repository provides a skeleton for a website that allows Stanford students to see their grades. You create a Google Doc, which is then linked to Stanford Student Center. The sharing settings have to be "Anyone with the link", and you have to enable "Publish to the web".

Grades can be entered by teaching staff in the Google Doc, and changes will be reflected on the web site.

(Note: if Google does not republish automatically each time a change is made, you also have to republish manually.)

## Installation
- Create a new Google Spreadsheet, and import data from ``sample/sample.csv'' into one spreadsheet, and ``sample/sample_project.csv'' in the other.
- Set the sharing settings to be "Anyone with the link", and enable "Publish to the web". Note the document keys that you later have to enter in ``constants.php''.
- Edit ``constants.php'' to reflect the class you want to use student center for.
- Upload the website (i.e. the contents of this folder) to the cgi-bin folder on Corn (note that .htaccess enables WebAuth).
- Run generate_db.php on the web server to generate the SQLite database for logging accesses to the site.

## Notes
- You have to manually update index.php to parse whatever fields you update in the document (e.g. grades, questions, etc.).

## Old Workflow
Grades have to be entered by the staff in a Google Doc following this model: https://docs.google.com/spreadsheet/ccc?key=0AjrnWBpw0TGqdFZmLXVxeC1KMjJkVFg3akdMckU3RlE#gid=2. 

The Python scripts are located in the scripts folder.
### To integrate the Gradiance grades in the Google Spreadsheet
- Download the Gradiance report as a csv (gradianceclassreport.csv)
- Download the current Google Spreadsheet as a csv (googlespreadsheet.csv)
- Run the Python script gradiance-script.py
- Import the results written to the file result.csv in the Google spreadsheet by copying/pasting the rightmost columns (the order of the rows is the same as in the Google Spreadsheet)

### To calculate the final grades (CS246 2013)
- Download the current Google Spreadsheet as a csv (googlespreadsheet.csv)
- Run the Python script total-grades-script.py
- The final grades are written to the file total-grades.csv

### To calculate the final grades (CS224W 2013)
- Download the 2 spreadsheets (regular, and project grades)
- Download the list of enrolled students as a CSV
- Run ``scripts/final_grade.py''

## Suggestions of improvement
- Write a script that transfers the gradiance results to Google Spreadsheet (using Google App Scripts)
