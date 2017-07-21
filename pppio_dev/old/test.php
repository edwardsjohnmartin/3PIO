<?php
	//header( 'Location: index.php' );
    abstract class AbstractClass {
		const MAX = array(1, 2);
		protected static $VAL = array(1, 2, 3);
		protected $modelName = '????'; //set me in constructor? //or maybe i should have a getter that will just set it if it's not already set? that will probably be better so the child class doesn't have to call... well but why does a controller need a special constructor other than that?

		public function __construct() { //this is not called from concrete class...
			$this->modelName = 'aaaaaaaaaabstract';
			echo 'constructing a ' . static::class . '<br>';
		}

        public static function test()
        {
            return static::class;
        }

		public function test2()
		{
			//return static::class; //this works, too
			return get_object_vars($this);
		}

        public static function test3()
        {
            return static::$VAL; //self::$VAL to get own instead of child //can use parent in child classes
        }

		public function getModelName()
		{
			return $this->modelName;
		}

		public static function getMax()
		{
			return static::MAX;
		}

    }
    
    class ConcreteClass extends AbstractClass {
		const MAX = array(2, 3);
		protected static $VAL = 4;// array_merge(array(1, 2), array(4, 5, 10));// array_merge($this->$VAL, array(4, 5, 10)); //make new array with old array...
		public $property = "hi";
		public $property2 = "test";
		public $property3 = "abc";
		//if i want a special constructor, i need to make sure to call the parent one, or set the model name...
		public function __construct() {
			$this->modelName = 'concreeeeeete';
		}
	}
    
    echo AbstractClass::test() . "<br>";
    echo ConcreteClass::test() . "<br>";

	echo 'max is ' . ConcreteClass::getMax() . '<br>';

	print_r(AbstractClass::test3());
	echo "<br>";
	print_r(ConcreteClass::test3());
	echo "<br>";

	$str = ConcreteClass::test();
	$class = new $str();
	print_r($class);
	echo "<br>";

	$str1 = "AbstractClass"; //the class names aren't case sensitive
	echo $str1::test();
	echo "<br>";

	echo "i'm a " . $class->getModelName();
	echo "<br>";
	//echo ($class->modelName)::test3() . "<br>";

	class TestClass {
		public $number1 = 123;
		public $string1 = "abc";
		public $nothing1;
	}

	$tst = new TestClass();
	echo $tst->number1 . ' is a ' . gettype($tst->number1) . '<br>'; 
	echo $tst->string1 . ' is a ' . gettype($tst->string1) . '<br>';

	$tst->string2 = 'added later';
	echo $tst->string2 . ' is a ' . gettype($tst->string2) . '<br>';


	$dt = new DateTime();
	echo $dt->format(DateTime::W3C) . ' is a ' . get_class($dt) . '<br>'; //get_class expects object
	echo $tst->nothing1 . ' is a ' . gettype($tst->nothing1) . '<br>';
	settype($tst->nothing1, 'string');
	echo $tst->nothing1 . ' is a ' . gettype($tst->nothing1) . '<br>';
	echo intval('a4') . '<br>';

	$var = "12aaaa3";
	echo $var . " is numeric? " . is_numeric($var) . '<br>';
	echo $var . " is int? " . is_int($var) . '<br>';
	echo $var . " is float? " . is_float($var) . '<br>';
	settype($var, 'int');
	echo $var . " is numeric? " . is_numeric($var) . '<br>';
	echo $var . " is int? " . is_int($var) . '<br>';
	echo $var . " is float? " . is_float($var) . '<br>';

	class TestClassConstruct {
		public $id;
		public $name;

		public function __construct($id = null, $name = null)
		{
			
			if ($id != null) $this->id = $id;
			if ($name != null) $this->name = $name;						
		}
	}

	$test2 = new TestClassConstruct();
	print_r($test2);
	$test2->name2 = "abc";
	print_r($test2);
	print_r(get_object_vars($test2));
	print_r(get_class_vars(get_class($test2)));

	//named parameters not supported. good to know.
	$test3 = new TestClassConstruct($name = "hi"); //this sets the first parameter, the id, not the name.
	print_r($test3);

	$arrmerge =  array_merge(array(1, 2), array(4, 5, 10));

/*
	echo $class::test() . "<br>";
	
	print_r($class->test2());
	//print_r(get_object_vars($class));
*/

	class TestFunctions
	{
		public $val = 4;

		public function test()
		{
			return $this->val;
		}

		public function test2()
		{
			return $this->test();
		}
	}

	$f = new TestFunctions();
	echo $f->test();
	echo $f->test2();

	echo "<br>";
	echo substr("ModelController", 0, -10);
	echo "<br>";

	$bigint = 9223372036854775807;
	echo $bigint . ' is type ' . gettype($bigint) . '<br>';
	$bigint+=10;
	echo $bigint . ' is type ' . gettype($bigint) . '<br>';
	//these three all give the same result.
	//settype($bigint, 'integer');
	//$bigint = (integer)$bigint;	
	$bigint = intval($bigint);
	echo $bigint . ' is type ' . gettype($bigint) . '<br>';

	$true1 = true;
	echo $true1 . ' is type ' . gettype($true1) . '<br>';
	$true1 = null;
	echo 'name="' . $true1 . '"';
	$true2 = null;
	echo 'name="' . $true2 . '"';


	class TestVars
	{
		private $var1;
		private $var2 = 1;


		public static function getVars()
		{
			$name = static::class;
			$test = new $name;
			return get_class_vars(static::class);
		}
	}

	print_r(TestVars::getVars());

	$vars = TestVars::getVars();
	foreach($vars as $var => $temp)
	{
		echo $var;
	}


$testary[1] = 'hi';
print_r($testary);



//session_start();
//session_write_close();
//session_write_close(); //no problem if this happens twice (seems like)

?>


<p>abcde</p>

<?php
//header( 'Location: index.php' );
?>

