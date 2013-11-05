<?php
//system libs
require_once "leolos/publisher.php";
require_once "leolos/mysqldb.php";
require_once "leolos/configparser.php";
require_once "github-api/vendor/autoload.php";

//app libs
require_once "lib/search.php";

//app controlers
require_once "search.php";


/**
* Trida drzici konfiguraci vcetne pripojeni do MySQL
  parsuje se standardni ini soubor
  
  knihovna mysqldb obsahuje implicitni parser - predavame pouze instanci
*/
class Config {

    public $control;
    public $mysqlConnection;

    public function __construct() {
        /* cesta ke konfiguracnimu souboru nastavena v apache */
        $configFile = getenv('ConfigFile');
        if(!$configFile) {
            $configFile = '/var/www/etnetera/conf/etnetera.conf';
        }
        $parser = new Leolos\ConfigParser\ConfigParser($configFile);
        $this->control = new ConfigControl($parser);
        $this->mysqlConnection = new Leolos\MysqlDb\MysqlDb(new Leolos\MysqlDb\MysqlDbConfig($parser));
        $this->mysqlConnection->connect();
    }
}

/**
* Parser pro sekci [control]
*/
class ConfigControl {
    public $baseURL;

    public function __construct(&$parser) {
        $this->baseURL = $parser->get("control","BaseURL");
    }
}

//instance dispatcheru
$publisher = new Leolos\Dispatcher();

//registrace konfigurace
$publisher->setAplicationConfigObject(new Config());

//mapovani controleru
$publisher->addHandler(new Leolos\FunctionHandler("", "searchScreen", "GET", array(), False));
$publisher->addHandler(new Leolos\FunctionHandler("/historie-hledani", "searchHistoryScreen", "GET", array(), False));
$publisher->addHandler(new Leolos\FunctionHandler("/smazat-historii", "clearSearchHistoryScreen", "GET", array(), False));
$publisher->addHandler(new Leolos\FunctionHandler("/smazat-historii/clearSearchHistoryProcess", "clearSearchHistoryProcess", "POST", array(), False));

//spracovani pozadavku
$res = $publisher->handleRequest();
