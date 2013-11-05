<?php


function searchScreen($request) {

    $repositories = array();

    $username = $request->form->get("username", "str");
    
    //new search
    if( $username ) {

        try {
            $client = new Github\Client();
            $repositories = $client->api('user')->repositories($username, 
                array("sort" => "created", "direction" => "desc"));
            
        } catch (Github\Exception\RuntimeException $e) {
            echo "<strong style=\"color:green;\">Probably user <i>$username</i> not found</strong>";
        }
        
        //log search request
        $searchItem = new SearchItem($request->config->mysqlConnection);
        $searchItem->setSearchPatern($username);
        $searchItem->setIP($_SERVER["REMOTE_ADDR"]);
        //remember also $_SERVER['HTTP_X_FORWARDED_FOR'] behind a server proxy
        $searchItem->save();
        
    }
    Leolos\Status\Status::OK();
    require_once "templ/index.html";
                                                                                                                                                                                                                                                                return ;
}


function searchHistoryScreen($request) {

    DEFINE("RESULTS_ON_PAGE", "5");
    
    $pageNumber = $request->form->get("pageNumber", "int", 1);
    
    if( $pageNumber < 1 ) $pageNumber = 1;

    $searchHistory = new SearchLogList($request->config->mysqlConnection);
    $searchHistory->setOrder("search_date");
    $searchHistory->setDirection("desc");
    
    $pageCount = ceil($searchHistory->getCount()/RESULTS_ON_PAGE);
    
    $offset = (($pageNumber-1)*RESULTS_ON_PAGE);
    
    /*
    strankovani se provadi na urovni SQL s pouzitim LIMIT syntaxe - LIMIT offset, limit
    pro strankovani se pouziva offset - od ktereho zaznamenu zobrazujeme, limit pocet zobrazenych zaznamu
    
    k vypoctu poctu stranek odkazu na prvni a posledni stranku potrebujeme znat celkovy pocet zaznamu bez
    omezeni LIMIT - zde je rychlejsi provest dva dotazy, jeden s LIMIT a druhy bez LIMIT s kombinaci count(*)
    samozrejme za predpokladu, ze se pri dotazu pouziva index
    */
    $searchHistory->setLimit($offset.", ".RESULTS_ON_PAGE);
    
    $searchHistory->load();
    
    //print_r($searchHistory->list);
    Leolos\Status\Status::OK();
    require_once "templ/search_history.html";
    return ;
}


function clearSearchHistoryScreen($request) {

    //CSRF, XSRF polici - POST method + action token 
    $actionToken = MD5(time());

    session_start();
    $_SESSION["actionToken"] = $actionToken;
    

    Leolos\Status\Status::OK();
    require_once "templ/clear_search_history.html";
    return;
}

function clearSearchHistoryProcess($request) {
    
    session_start();
    
    $actionToken = isset($_SESSION["actionToken"]) ? $_SESSION["actionToken"] : Null;
    unset($_SESSION["actionToken"]);
    
    $hours = $request->form->get("hours", "int", 0);
    
    if ($hours && ($actionToken === $request->form->get("actionToken", "str", ""))) {
        $searchHistory = new SearchLogList($request->config->mysqlConnection);
        $searchHistory->removeByHours($hours);
    }
    return Leolos\Status\Status::REDIRECT($request->config->control->baseURL."/historie-hledani");
}