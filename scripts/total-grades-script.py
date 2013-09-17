# This script takes the the Google Spreadsheet CSV
# as an input, and outputs a CSV with the total grades
# of each student (in the same order as the rows in
# the Google Spreadsheet).

import csv

# Load the files
#   - f1 is the CSV Google Spreadsheet
#   - f2 is the file in which we put the result
f1 = file('googlespreadsheet.csv', 'r')
f2 = file('total-grades.csv', 'w')

c1 = csv.reader(f1)
c2 = csv.writer(f2)

c1.next()

# We iterate through each row (i.e. each student)
# of the Google Spreadsheet
for google_row in c1:
    # Check if the student exists
    if google_row[3] != None:
        # Match the columns
        # WARNING: if you modify the organization of the
        # Google Spreadsheet (in particular, the columns),
        # you will have to update the following lines.
        stanford_id  = google_row[2]
        sunetid      = google_row[3]
        gradiance_id = google_row[4]
        hw0          = google_row[14]
        hw1_total    = google_row[19]
        hw2_total    = google_row[27]
        hw3_total    = google_row[35]
        hw4_total    = google_row[43]
        gradiance1   = google_row[47]
        gradiance2   = google_row[48]
        gradiance3   = google_row[49]
        gradiance4   = google_row[50]
        gradiance5   = google_row[51]
        gradiance6   = google_row[52]
        gradiance7   = google_row[53]
        gradiance8   = google_row[54]
        gradiance9   = google_row[55]
        final_total  = google_row[71]

        # Totals of the homeworks
        hw1_tot = 95
        hw2_tot = 100
        hw3_tot = 100
        hw4_tot = 100
        
        # Totals of the Gradiance quizzes
        gradiance1_tot = 15
        gradiance2_tot = 18
        gradiance3_tot = 12
        gradiance4_tot = 18
        gradiance5_tot = 15
        gradiance6_tot = 12
        gradiance7_tot = 12
        gradiance8_tot = 15
        gradiance9_tot = 15

        # Total of the final
        final_tot = 180

        # Calculations for the Homeworks average grade.
        # hw_score is the homework average grade out of 100
        if hw0:
            normalized_hw0 = 100 * float(hw0)
        else:
            normalized_hw0 = 0
        if hw1_total:
            normalized_hw1 = 100 * float(hw1_total) / hw1_tot
        else:
            normalized_hw1 = 0
        if hw2_total:
            normalized_hw2 = 100 * float(hw2_total) / hw2_tot
        else:
            normalized_hw2 = 0
        if hw3_total:
            normalized_hw3 = 100 * float(hw3_total) / hw3_tot
        else:
            normalized_hw3 = 0
        if hw4_total:
            normalized_hw4 = 100 * float(hw4_total) / hw4_tot
        else:
            normalized_hw4 = 0
        hw_score = (normalized_hw0*5 + normalized_hw1*23.75 + normalized_hw2*23.75 + normalized_hw3*23.75 + normalized_hw4*23.75) / 100

        # Calculations for the Gradiance quizzes average grade.
        # gradiance_score is the Gradiance average grade out of 100
        if gradiance1:
            normalized_gradiance1 = 100 * float(gradiance1) / gradiance1_tot
        else:
            normalized_gradiance1 = 0
        if gradiance2:
            normalized_gradiance2 = 100 * float(gradiance2) / gradiance2_tot
        else:
            normalized_gradiance2 = 0
        if gradiance3:
            normalized_gradiance3 = 100 * float(gradiance3) / gradiance3_tot
        else:
            normalized_gradiance3 = 0
        if gradiance4:
            normalized_gradiance4 = 100 * float(gradiance4) / gradiance4_tot
        else:
            normalized_gradiance4 = 0
        if gradiance5:
            normalized_gradiance5 = 100 * float(gradiance5) / gradiance5_tot
        else:
            normalized_gradiance5 = 0
        if gradiance6:
            normalized_gradiance6 = 100 * float(gradiance6) / gradiance6_tot
        else:
            normalized_gradiance6 = 0
        if gradiance7:
            normalized_gradiance7 = 100 * float(gradiance7) / gradiance7_tot
        else:
            normalized_gradiance7 = 0
        if gradiance8:
            normalized_gradiance8 = 100 * float(gradiance8) / gradiance8_tot
        else:
            normalized_gradiance8 = 0
        if gradiance9:
            normalized_gradiance9 = 100 * float(gradiance9) / gradiance9_tot
        else:
            normalized_gradiance9 = 0
        gradiance_list = [normalized_gradiance1, normalized_gradiance2, normalized_gradiance3, normalized_gradiance4, normalized_gradiance5, normalized_gradiance6, normalized_gradiance7, normalized_gradiance8, normalized_gradiance9]
        gradiance_list.remove(min(gradiance_list))
        gradiance_score = float(sum(gradiance_list))/float(len(gradiance_list))
        
        # Calculation for the final grade
        # normalized_final is the final grade out of 100.
        if final_total:
            normalized_final = 100 * float(final_total) / final_tot
        else:
            normalized_final = 0

        # Calculation for the total grade
        final_score = hw_score * 0.4 + gradiance_score * 0.2 + normalized_final * 0.4

        # Create a new CSV row with the student SUNetID and the total grade
        result = [sunetid, final_score]
        c2.writerow(result)

# Close all files
f1.close()
f2.close()