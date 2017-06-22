prompt = "The '#' character can be used to write single-line comments. Use this to write a comment of your own!"
    
def TEST(student_input, student_output):
    problems = ""
    
    if "#" not in student_input:
        problems = problems + "You must use the '#' character to write at least one comment."
    
    if problems == "":
        return "Success!"
    else:
        return problems
