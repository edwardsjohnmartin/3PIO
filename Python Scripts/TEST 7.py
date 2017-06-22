prompt = "Declare a variable named <strong>my_float</strong> and assign it a value of <strong>7.0</strong>."

def TEST(student_input, student_output):
    problems = ""
    returns = [VALIDATE_VAR("my_float", "float", 7.0)]

    for thing in returns:
        if thing != True:
            problems = problems + thing+"\n"
    if problems == "":
        return "Success!"
    else:
        return problems

