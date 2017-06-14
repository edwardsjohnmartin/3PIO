def VALIDATE_VAR(desired_var_name, desired_type_str, *args):
    student_var = globals().get(desired_var_name, None)
    
    if student_var != None:
        if str(type(student_var)).split("<type '")[1].split("'>")[0] == desired_type_str:
            if (len(args) == 1):
                if (student_var == args[0]):
                    return True
                else:
                    return "{0} must be equal to {1}.".format(desired_var_name, str(args[0]))
            elif (len(args) > 1):
                if (len(student_var) == len(args)):
                    arg_list = []
                    for arg in args:
                        arg_list.append(arg)
                    if (set(arg_list).intersection(set(student_var))) == set(arg_list):
                        return True
                    else:
                        return "{0} does not contain the correct values.".format(desired_var_name)
                else:
                    return "{0} should contain {1} items.".format(desired_var_name, len(args))
        else:
            return "{0} should be of type {1}.".format(desired_var_name, desired_type_str)
    else:
        return "You must declare a(n) {0} named {1}.".format(desired_type_str, desired_var_name)

def GET_PROMPT():
    return prompt




prompt = "Declare a variable named <b>my_base</b> and assign it a value of <b>7.5</b>. Declare another variable named <b>my_power</b> and assign it a value of <b>3.33455</b>. Finally, declare a variable named <b>my_result</b> and set it equal to <b>my_base</b> raised to <b>my_power</b>."

def TEST(student_input, student_output):
    problems = ""
    returns = [VALIDATE_VAR("my_base", "float", 7.5), VALIDATE_VAR("my_power", "float", 3.33455), VALIDATE_VAR("my_result", "float", 7.5**3.33455)]

    if "**" not in student_input:
        problems = problems + "Remember to use the '**' operator to exponentiate your variables."
    for thing in returns:
        if thing != True:
            problems = problems + thing+"\n"
    if problems == "":
        return "Success!"
    else:
        return problems
