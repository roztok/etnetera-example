<?php


function searchScreen($request) {

    $repositories = array();

    $username = $request->form->get("username", "str");
    
    //new search
    if( $username ) {

        $client = new Github\Client();
        $repositories = $client->api('user')->repositories($username, 
            array("sort" => "created", "direction" => "desc"));
        
        //log search request
        $searchItem = new SearchItem($request->config->mysqlConnection);
        $searchItem->setSearchPatern($username);
        $searchItem->setIP($_SERVER["REMOTE_ADDR"]);
        $searchItem->save();
        //remember also $_SERVER['HTTP_X_FORWARDED_FOR'] behind a server proxy
        
    }
    Leolos\Status\Status::OK();
    require_once "templ/index.html";
                                                                                                                                                                                                                                                                return ;
}


function searchHistoryScreen($request) {

    DEFINE("RESULTS_ON_PAGE", "2");
    
    $pageNumber = $request->form->get("pageNumber", "int", 1);
    
    if( $pageNumber < 1 ) $pageNumber = 1;

    $searchHistory = new SearchLogList($request->config->mysqlConnection);
    $searchHistory->setOrder("search_date");
    $searchHistory->setDirection("desc");
    
    $resultSize = $searchHistory->getCount();
        
    $searchHistory->setLimit((($pageNumber-1)*RESULTS_ON_PAGE).", ".RESULTS_ON_PAGE);
    
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