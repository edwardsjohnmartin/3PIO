prompt = "Declare a variable called <strong>first_name</strong> and assign it a value of <strong>'James'</strong>. Declare another variable called <strong>formatted_name</strong> and assign it a value of 'FIRST NAME: James' using string.format()."
        
def TEST(student_input, student_output):
    problems = ""
    returns = [VALIDATE_VAR("first_name", "string", "James"), VALIDATE_VAR("formatted_name", "string", "FIRST NAME: James")]
    
    for thing in returns:
        if thing != True:
            problems = problems + thing+"\n"

    if ".format(" not in student_input:
        problems = problems + "You must use string.format() to format first_name."
    
    if problems == "":
        return "Success!"
    else:
        return problems
