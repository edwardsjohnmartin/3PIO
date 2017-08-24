
<p>Oops, this is the error page.</p>
<p>Looks like something went wrong.</p>
<?php
// print_r($_SESSION); 
if(isset($_SESSION['errorMessage']))
echo $_SESSION['errorMessage']; ?>
