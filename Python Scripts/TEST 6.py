prompt = "Declare a variable named <strong>my_boolean</strong> and assign it a value of <strong>False</strong>."
        
def TEST(student_input, student_output):
    problems = ""
    returns = [VALIDATE_VAR("my_boolean", "bool", False)]

    for thing in returns:
        if thing != True:
            problems = problems + thing+"\n"
    if problems == "":
        return "Success!"
    else:
        return problems

