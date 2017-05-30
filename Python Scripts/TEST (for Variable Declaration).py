#These are parameters for the function.
#TODO: Modify the code so that these parameters have defaults and can be optional.
#This will simplify things when we want to find variables with certain attributes, but
#have no information about other attributes.
desired_var_name = "my_int"
desired_value = 3
desired_type = int

my_int = 3.0  #Student's code

#Logic for the function 
student_var_val = locals().get(desired_var_name, None)

if student_var_val != None:
    if type(student_var_val) == desired_type:
        if student_var_val == desired_value:
            print("Good work! {0} is the correct value.".format(desired_var_name))
        else:
            print("{0} must be equal to {1}.".format(desired_var_name, desired_value))
    else:
        print("{0} is currently of type {1}, but it should be of type {2}.".format(desired_var_name, type(student_var_val).__name__, desired_type.__name__))
else:
    print("You must declare a variable named \"{0}\".".format(desired_var_name))
