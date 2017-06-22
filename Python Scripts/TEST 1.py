prompt = 'Using the <strong>print</strong> statement, output the string "Hello World!"'

def TEST(student_input, student_output):
    problems = ""
    
    if "Hello World!\n" != student_output:
        problems = 'You must print the string "Hello World!"'
        
    if problems == "":
        return "Success!"
    else:
        return problems




