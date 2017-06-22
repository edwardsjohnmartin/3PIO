prompt = "Declare a list named <strong>my_bool</strong> and assign it a value of <strong>7 < 8</strong>."

def TEST(student_input, student_output):
    problems = ""
    returns = [VALIDATE_VAR("my_bool", "bool", (7<8))]
    strings = ['7', '<', '8']

    for thing in returns:
        if thing != True:
            problems = problems + thing+"\n"

    if (all(x in student_input for x in strings) == False:
        problems = problems + "You must assign '7 < 8' to my_bool."
        
    if problems == "":
        return "Success!"
    else:
        return problems

