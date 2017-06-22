prompt = 'Using the "+" operator, concatenate the strings "Hello " and "World!", then use the result to print "Hello World!".'
    
def TEST(student_input, student_output):
    problems = ""
    
    if "+" not in student_input:
        problems = problems + "You must use the '+' operator to concatenate the two strings."

    if student_output != "Hello World!\n":
        problems = problems + 'You must output "Hello World!".'
    
    if problems == "":
        return "Success!"
    else:
        return problems
