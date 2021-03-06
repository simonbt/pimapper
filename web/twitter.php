<?php
header('Content-Type:application/json');
session_start();

require_once(__DIR__ . '/includes/TwitterClass.php');
require_once(__DIR__ . '/includes/twitteroauth/twitteroauth.php');


$config = require(__DIR__ . '/includes/config.php');

try {
    $pdo = new PDO(
        'mysql:host=' . $config['db']['hostname'] . ';dbname=' . $config['db']['database'],
        $config['db']['username'],
        $config['db']['password']
    );
} catch (PDOException $pdoError) {
    die('Error constructing database, error was: ' . $pdoError->getMessage());

}


if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $tempcon = new TwitterOAuth($config['twitter']['consumer_key'], $config['twitter']['consumer_secret']);


    if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
        $dbcheck = new TwitterFuncs($pdo, $config, $tempcon);
        $haveAuth = $dbcheck->checkTwitterAuth();
        if ($haveAuth['oauth_token'] != NULL) {
            $dbcheck->twitterAuthFromDB('default');
            if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
                $isLoggedin['isLoggedin'] = true;
                echo json_encode($isLoggedin);
                die();
            }
        }

        $isLoggedin['isLoggedin'] = false;
        echo json_encode($isLoggedin);
        die();
    } else {
        $isLoggedin['isLoggedin'] = true;
        echo json_encode($isLoggedin);
        die();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $connection = new TwitterOAuth($config['twitter']['consumer_secret'], $config['twitter']['consumer_key'], $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);

    $twitterOptions = new TwitterFuncs($pdo, $config, $connection);

    // Check if a post has been sent
    if (array_key_exists("option", $_POST)) {
        $option = $_POST['option'];
    } else {
        die("You must POST an option");
    }

    if ($option == 'info') {
        $status = $twitterOptions->getStatus();
        var_dump($status);
    } elseif ($option == 'tweet') {
        $status = $twitterOptions->sendTweet($_POST['message']);
        var_dump($status);
    } elseif ($option == 'dm') {
        $status = $twitterOptions->sendDM($_POST['user'], $_POST['message']);
        var_dump($status);
    } elseif ($option == 'deleteauth') {
        $status = $twitterOptions->deleteTwitterAuth('default');
        var_dump($status);
    } elseif ($option == 'logout') {
        session_destroy();
        echo "Logged out";
    }

}
