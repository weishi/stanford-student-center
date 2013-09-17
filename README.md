Stanford Student Center
=======================

This repository provides a skeleton for a website that allows Stanford students to see their grades. Grades have to be entered by the staff in a Google Doc following this model: https://docs.google.com/spreadsheet/ccc?key=0AjrnWBpw0TGqdFZmLXVxeC1KMjJkVFg3akdMckU3RlE#gid=2. The sharing settings have to be "Anyone with the link", and you have to enable "Publish to the web".
(Note: if Google does not republish automatically each time a change is made, you also have to republish manually.)

## Installation
Upload the website to the cgi-bin folder on the FarmShare (note the hidden file .htaccess that enables WebAuth).

## Workflow
The Python scripts are located in the scripts folder.
### To integrate the Gradiance grades in the Google Spreadsheet
- Download the Gradiance report as a csv (gradianceclassreport.csv)
- Download the current Google Spreadsheet as a csv (googlespreadsheet.csv)
- Run the Python script gradiance-script.py
- Import the results written to the file result.csv in the Google spreadsheet by copying/pasting the rightmost columns (the order of the rows is the same as in the Google Spreadsheet)

### To calculate the final grades
- Download the current Google Spreadsheet as a csv (googlespreadsheet.csv)
- Run the Python script total-grades-script.py
- The final grades are written to the file total-grades.csv

## Suggestions of improvement
- Write a script that transfers the gradiance results to Google Spreadsheet (using Google App Scripts)
- Migrate to Bootstrap 3