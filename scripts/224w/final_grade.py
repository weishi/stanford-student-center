import csv

students = {}

with open("Grades- Project - Sheet1.csv", "r") as f:
    reader = csv.DictReader(f)
    for row in reader:
        for i in range(1, 4):
            sunetid = row["sunetid%d" % i].strip()
            if len(sunetid) > 0:
                if sunetid not in students:
                    students[sunetid] = {}
                students[sunetid]['proposal'] = row['proposal_total']
                students[sunetid]['milestone'] = row['milestone_total']
                students[sunetid]['report'] = row['report_total']
                students[sunetid]['poster'] = row['poster_total']
students2 = {}
for k, v in students.iteritems():
    students2[k] = {}
    for vk, vv in v.iteritems():
        if len(vv) == 0:
            students2[k][vk] = 0
        else:
            try:
                students2[k][vk] = float(vv)
            except:
                students2[k][vk] = 0
students = students2

with open("Grades - Sheet1.csv", "r") as f:
    reader = csv.DictReader(f)
    for row in reader:
        sunetid = row["sunetid"]
        if sunetid not in students:
            students[sunetid] = {}

        # HW0
        try:        
            students[sunetid]['hw0'] = float(row['hw0_total'])
        except:
            students[sunetid]['hw0'] = 0

        # HW1
        try:        
            students[sunetid]['hw1'] = float(row['hw1_total'])
        except:
            students[sunetid]['hw1'] = 0
        
        # Proposal
        try:
            proposal_adj = float(row['proposal_adjustment'])
        except:
            proposal_adj = 0
        if 'proposal' in students[sunetid]:
            students[sunetid]['proposal'] += proposal_adj
        else:
            students[sunetid]['proposal'] = proposal_adj

        # HW2
        try:        
            students[sunetid]['hw2'] = float(row['hw2_total'])
        except:
            students[sunetid]['hw2'] = 0

        # HW3
        try:        
            students[sunetid]['hw3'] = float(row['hw3_total'])
        except:
            students[sunetid]['hw3'] = 0
        
        # Proposal
        try:
            milestone_adj = float(row['milestone_adjustment'])
        except:
            milestone_adj = 0
        if 'milestone' in students[sunetid]:
            students[sunetid]['milestone'] += milestone_adj
        else:
            students[sunetid]['milestone'] = milestone_adj

        # HW4
        try:        
            students[sunetid]['hw4'] = float(row['hw4_total'])
        except:
            students[sunetid]['hw4'] = 0

        # Report
        try:
            report_adj = float(row['report_adjustment'])
        except:
            report_adj = 0
        if 'report' in students[sunetid]:
            students[sunetid]['report'] += report_adj
        else:
            students[sunetid]['report'] = report_adj

        # Poster
        try:
            poster_adj = float(row['poster_adjustment'])
        except:
            poster_adj = 0
        if 'poster' in students[sunetid]:
            students[sunetid]['poster'] += poster_adj
        else:
            students[sunetid]['poster'] = poster_adj

        # Participation
        try:        
            students[sunetid]['participation'] = float(row['participation_credit'])
        except:
            students[sunetid]['participation'] = 0

sfinals = {}
for student, scores in students.iteritems():
    # print student
    # print "\t", scores
    final_grade = 0.12 * scores['hw1'] + 0.12 * scores['hw2'] + 0.12 * scores['hw3'] + 0.12 * scores['hw4']
    # print "\t", final_grade
    final_grade += 0.1 * scores['proposal'] + 0.1 * scores['milestone'] + 0.25 * scores['report'] + 0.05 * scores['poster']
    final_grade += 0.02 * scores['hw0'] * 100 + 0.01 * scores['participation']
    sfinals[student] = final_grade

enrolled = {}
with open("students-enrolled.csv", "rU") as f:
    reader = csv.DictReader(f)
    for row in reader:
        if len(row["Status Note"]) == 0:
            enrolled[row["Email"].split("@")[0]] = True

print enrolled

# with open("Grades - Sheet1.csv", "r") as f:
#     reader = csv.DictReader(f)
#     for row in reader:
#         sunetid = row["sunetid"]
#         if sunetid in sfinals:
#             print sunetid, sfinals[sunetid], 1 if sunetid in enrolled else 0
#         else:
#             print sunetid, 0.0
