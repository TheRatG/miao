<?php

function help()
{
	$mes = array();
	$mes[] = "";
	$mes[] = "Miao Console module";
	$mes[] = "";

	$mes[] = "add    - add new module or class (or View/ViewBlock/Action if module contains \"Office\")";
	$mes[] = "         Example: \"php console.php add Miao_TestLib\" or \"php console.php add Miao_TestLib_TestClass\"";
	$mes[] = "";

	$mes[] = "cp     - copy lib or class with new names";
	$mes[] = "         Example: \"php console.php cp Miao_TestLib Miao_NewTestLib\" or \"php console.php cp Miao_TestLib_TestClass Miao_TestLib_NewTestClass\"";
	$mes[] = "";

	$mes[] = "ren    - rename lib or class";
	$mes[] = "         Example: \"php console.php ren Miao_TestLib Miao_NewTestLib\" or \"php console.php ren Miao_TestLib_TestClass Miao_TestLib_NewTestClass\"";
	$mes[] = "";

	$mes[] = "del    - remove lib or class (with template, if needed)";
	$mes[] = "         Example: \"php console.php del Miao_TestLib\" or \"php console.php del Miao_TestLib_TestClass\"";
	$mes[] = "";

	$mes[] = "tpl    - allow create *.tpl files for View, ViewBlock";
	$mes[] = "no-tpl - deny create *.tpl files for View, ViewBlock";
	$mes[] = "         If \"tpl\" or \"no-tpl\" undefined - script will check, that name of lib contains string \"office\".";
	$mes[] = "         Example: for Miao_FrontOffice_View_Main script automatically set parametr \"tpl\"";
	$mes[] = "                        and additional will create /modules/FrontOffice/templates/View/main.tpl";
	$mes[] = "                  for Miao_TestLib_View_Main script automatically set parametr \"no-tpl\"";
	$mes[] = "";

	$mes[] = "v      - verbose mode";
	$mes[] = "";

// 	$mes[] = "--bootstrap      - bootstrap filename";
// 	$mes[] = "";

	$mes[] = "help   - print help";
	$mes[] = "";
	$mes[] = "";
	$message = implode( "\n", $mes );
	echo $message;
	exit();
}

function parseOpts()
{
	if ( !isset( $_SERVER[ 'argc' ] ) )
	{
		help();
	}
	else
	{
		$action = '';
		$userName = '';
		$verbose = false;
		$withTemplate = null;
		$remainingArgs = array();

		$bootstrap = '';
		$opts = getopt( '', array( 'bootstrap:' ) );
		if ( isset( $opts[ 'bootstrap' ] ) )
		{
			$bootstrap = $opts[ 'bootstrap' ];
		}

		foreach ( $_SERVER[ 'argv' ] as $key => $value )
		{
			if ( 0 == $key )
			{
				continue;
			}
			if ( 0 === strpos( $value, '--bootstrap' ) )
			{
				continue;
			}

			switch ( $value )
			{
				case 'add':
				case 'cp':
				case 'ren':
				case 'del':
					$action = $value;
					break;

				case 'tpl':
					$withTemplate = true;
					break;

				case 'no-tpl':
					$withTemplate = false;
					break;

				case '-v':
				case '--verbose':
				case 'v':
				case 'verbose':
					$verbose = !$verbose ? true : false;
					break;

				case 'h':
				case 'help':
				case '--help':
				case '-h':
					help();
					break;
				default:
					$remainingArgs[] = $value;
			}
		}

		if ( empty( $action ) || empty( $remainingArgs ) )
		{
			help();
		}
	}

	return array(
		'action' => $action,
		'userName' => $userName,
		'verbose' => $verbose,
		'withTemplate' => $withTemplate,
		'remainingArgs' => $remainingArgs,
		'bootstrap' => $bootstrap );
}

function includeBootstrap( $bootstrap )
{
	if ( empty( $bootstrap ) )
	{
		require_once __DIR__ . '/bootstrap.php';
	}
	else if ( file_exists( $bootstrap ) && is_readable( $bootstrap ) )
	{
		require_once $bootstrap;
	}
	else
	{
		$msg = sprintf( 'Invalid param --bootstrap, file (%s) not found or is not readable', $bootstrap );
		throw new Exception( $msg );
	}
}

$action = '';
$userName = '';
$verbose = false;
$withTemplate = null;
$remainingArgs = array();
$bootstrap = '';

try
{
	$data = parseOpts();
	extract( $data );
	includeBootstrap( $bootstrap );

	$log = Miao_Log::easyFactory( null, $verbose );

	$author = isset( $_SERVER[ 'USER' ] ) ? $_SERVER[ 'USER' ] : '';
	$console = new Miao_Console( $remainingArgs[ 0 ], $author, $log );
	$console->createTemplate( $withTemplate );
	if ( $action == 'cp' || $action == 'ren' )
	{
		if ( !isset( $remainingArgs[ 1 ] ) )
		{
			help();
		}
		$console->$action( $remainingArgs[ 1 ] );
	}
	else
	{
		$console->$action();
	}
}
catch ( Exception $ex )
{
	echo "\n";
	echo "Console error!\n";
	echo $ex->getMessage();
	echo "\n\n";
}