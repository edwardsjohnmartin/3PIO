prompt = "Declare a list named <strong>my_list</strong> and assign it a value of <strong>[1, 2, 3]</strong>."

def TEST(student_input, student_output):
    problems = ""
    returns = [VALIDATE_VAR("my_list", "list", [1, 2, 3])]

    for thing in returns:
        if thing != True:
            problems = problems + thing+"\n"
    if problems == "":
        return "Success!"
    else:
        return problems

