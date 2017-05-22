from contextlib import redirect_stdout
import io
from inspect import signature


#sig = signature(exclaim)
#num_params = len(sig.parameters)
#print(num_params)


def exclaim(word):      #Test func for TEST_OUT
    print(word + "!")

def add3(num):          #Test func for TEST_RETURN
    return num + 3

def add_punc(word, punc):   #Test func for TEST_OUT
    print(word + punc)

def add_punc_unlimited(word, *args):
    for arg in args:
        word = word + arg
    print(word)


def TEST_OUT(method, desired_out, *args):
    f = io.StringIO()
    with redirect_stdout(f):
        method(*args)
        
    out = f.getvalue().rstrip()
    
    if (out == desired_out):
        print(True)
    else:
        print(False)
        

def TEST_RETURN(method, desired_out, *args):
    if (desired_out == method(*args)):
        print(True)
    else:
        print(False)

TEST_OUT(add_punc, "Success...", "Success", "...")
TEST_OUT(add_punc_unlimited, "Success?!...", "Success", "?", "!", "...")
TEST_RETURN(add3, 3, 0)
