#underscores have been prepended to global variable names and some of the method names.
#hopefully, this will allow us to avoid any naming conflicts when this code is appended to a student's.


#these are the global variables that will be evaluated in the test function.
__returns = []
__in_strings = []
__out_string = [None] #this was overwriting teacher's value, so I changed it to use an array. Don't know why this works.


#this is what all the functions used to validate a variable or a function eventually call.
#if a problem is encountered, it returns a string about the problem.
#if there is no problem, it returns true.
# -----------------------------------------------------------------------------------
def __UNIVERSAL_VALIDATOR(desired_var_name, desired_type_str, desired_return, *args):
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
                    try:
                        if (globals()[func_name]() != desired_return):
                            return "{0} does not return the proper value.".format(func_name)
                        else:
                            return True
                    except:
                        return "{0} must accept no arguments.".format(func_name)
                    
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

# -----------------------------------------------------------------------------------



#these methods make it a little easier to call the universal validator for a func/variable.
# --------------------------------------------------------------------
def __VALIDATE_FUNC(func_name, desired_return, *args):
    return __UNIVERSAL_VALIDATOR(func_name, 'function', desired_return, *args)

def __VALIDATE_VAR(var_name, desired_type, *args):
    return __UNIVERSAL_VALIDATOR(var_name, desired_type, None, *args)

# --------------------------------------------------------------------



#these are the methods the professors will call in their test code
# --------------------------------------------------------------------
def test_val(var_name, var_val):
    var_type = str(type(var_val)).split("<type '")[1].split("'>")[0]
    __returns.append(__VALIDATE_VAR(var_name, var_type, var_val))

def test_func(func_name, desired_return, *params):
    __returns.append(__VALIDATE_FUNC(func_name, desired_return, *params))

def test_in(string):
    __in_strings.append(string)

def test_out(string):
    __out_string[0] = string + "\n"

# --------------------------------------------------------------------


#finally, this is the test function that will be called from the editor.js file
# --------------------------------------------------------------------
def __TEST(student_input, student_output):
    problems = []
    
    for thing in __returns:
        if thing != True:
            problems.append(thing)

    if (all(x in student_input for x in __in_strings) == False):
        problems.append("You must include the following string(s) in your code: {0}.".format(str(__in_strings)))

    if __out_string[0] != None:
        if student_output != __out_string[0]:
            problems.append("Your output is incorrect.")

    return problems

