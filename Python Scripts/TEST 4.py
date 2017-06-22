prompt = 'You can create multiline comments by surrounding a block of text with triple quotes, like so:<br>"""multiline<br>comment"""<br>Give it a try!'
        
def TEST(student_input, student_output):
    problems = ""
    strings = ['"""', '"""']
    alt_strings = ["'''", "'''"]
    
    if (all(x in student_input for x in strings) or all(x in student_input for x in alt_strings)) == False:
        problems = "Remember to write a multiline comment."
    
    if problems == "":
        return "Success!"
    else:
        return problems
