#place prof variables here



#This method is used for ensuring that certain student-created variables/functions exist and either contain or produce the correct values.
#PARAMETERS
#desired_var_name: String -- The name of the variable/function that should exist.
#desired_type_str: String -- For variables, this is the type the variable should be. For functions, set this to 'function'.
#desired_return:      Any -- For variables, this will not be checked. For functions, this is the return value the function should produce.
#*args:               Any -- For variables, this is the desired value or values the variable should contain. For functions, these are the arguments passed in.
def VALIDATE_VAR(desired_var_name, desired_type_str, desired_return, *args):
    student_var = globals().get(desired_var_name, None)
    
    if desired_type_str == 'function':
        func_name = desired_var_name
        student_func = student_var
        arg_types = []

        for arg in args:
            arg_type = str(type(arg)).split("<type '")[1].split("'>")[0]
            arg_types.append(arg_type)

        if student_var != None:
            if str(type(student_func)).split("<type '")[1].split("'>")[0] == 'function':
                if (len(args) > 0):
                    try:
                        if (globals()[func_name](*args) != desired_return):
                            return "{0} does not return the proper value.".format(func_name)
                        else:
                            return True
                    except:
                        return "{0} must accept {1} arguments in the order: {2}.".format(func_name, str(len(args)), str(arg_types))
                else:
                    if (globals()[func_name]() != desired_return):
                        return "{0} does not return the proper value.".format(func_name)
                    else:
                        return True
            else:
                return "{0} must be a function.".format(func_name)
        else:
            return "You must create a function named {0}.".format(func_name)

    
    if student_var != None:
        if str(type(student_var)).split("<type '")[1].split("'>")[0] == desired_type_str:
            if (len(args) == 1):
                if (student_var == args[0]):
                    return True
                else:
                    return "{0} is not the correct value.".format(desired_var_name)
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


def TEST(student_input, student_output):
    problems = []

    returns = globals().get('returns', [])
    in_strings = globals().get('in_strings', [])
    out_string = globals().get('out_string', None)
    
    for thing in returns:
        if thing != True:
            problems.append(thing)

    if (all(x in student_input for x in in_strings) == False):
        problems.append("You must include the following strings in your code: {0}.".format(str(in_strings)))

    if out_string != None:
        if student_output != out_string:
            problems.append("Your output is incorrect.")

    return problems


