# This script takes the Gradiance report CSV
# and the Google Spreadsheet CSV as inputs,
# and outputs a CSV with the Gradiance grades of
# each student (in the same order as the rows in
# the Google Spreadsheet).

import csv

# Load the files
#   - f1 is the CSV Google Spreadsheet
#   - f2 is the CSV Gradiance
#   - f3 is the file in which we put the result
f1 = file('googlespreadsheet.csv', 'r')
f2 = file('gradianceclassreport.csv', 'r')
f3 = file('result.csv', 'w')

c1 = csv.reader(f1)
c2 = csv.reader(f2)
c3 = csv.writer(f3)

# Transforming the Gradiance CSV in an array.
# The "if" condition at the end removes the empty lines
gradiance = [row for row in c2 if row[:-1]]

# We iterate through each row of our Google Spreadsheet
for google_row in c1:
    # We only save the stanford_id, sunetid and gradiance_id columns
    # for that row and put them in the result_row array
    results_row = google_row[2:5]
    # We iterate through the gradiance array
    for gradiance_row in gradiance:
        # Check if the gradiance_id from the gradiance array matches
        # the gradiance_id from the current google_row
        if results_row[2] == gradiance_row[0]:
            # In that case, we capture the gradiance grades for that student
            gradiance_subrow = gradiance_row[3:len(gradiance_row)]
            # We replace empty values by 0
            for i, x in enumerate(gradiance_subrow):
                if len(x.strip()) < 1:
                    x = gradiance_subrow[i] = "0"
            # We append the gradiance grades to the google row
            results_row.extend(gradiance_subrow)
            break
    # We finally write the result to the file
    c3.writerow(results_row)
# That way, for each row of the Google Spreadsheet, we capture
# the student's credentials and append his gradiance grades
# before writing that row to the results CSV file.

# Close all files
f1.close()
f2.close()
f3.close()