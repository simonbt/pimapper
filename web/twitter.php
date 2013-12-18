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
    $pdoError->getMessage();
    exit;
}

$twitterPosts = new TwitterFuncs($pdo, $config);

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $connected = $twitterPosts->checkStatus($pdo);

    if ($connected['isLoggedin'])
    {
        if ($connected['method'] == 'db')
        {
            // Create twitter connection
            $connection = $twitterPosts->connect('db');
            // Test twitter connection
            $status = $twitterPosts->checkConnection($connection);
            if ($status['isLoggedin'])
            {
                return json_encode($status);
            }
            else
            {
                $status['isLoggedin'] = false;
                return json_encode($status);
            }


        }
        elseif ($connected['method'] == 'cookie')
        {
            // Create twitter connection
            $connection = $twitterPosts->connect('cookie');
            // Test twitter connection
            $status = $twitterPosts->checkConnection($connection);
            if ($status['isLoggedin'])
            {
                return json_encode($status);
            }
            else
            {
                $status['isLoggedin'] = false;
                return json_encode($status);
            }
        }
    }
    else
    {
        $isLoggedin['isLoggedin'] = false;
        return json_encode($isLoggedin);
    }

}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

    // Check if a post has been sent
    if (array_key_exists("option", $_POST))
    {
        $option = $_POST['option'];
    }
    else
    {
        die("You must POST an option");
    }

    if ($option == 'info')
    {
        $status = $twitterPosts->getStatus($connection);
        var_dump($status);
    }
    elseif ($option == 'tweet')
    {
        $status = $twitterPosts->sendTweet($connection, $_POST['message']);
        var_dump($status);
    }
    elseif ($option == 'dm')
    {
        $status = $twitterPosts->sendDM($connection, $_POST['user'], $_POST['message']);
        var_dump($status);
    }
    elseif ($option == 'logout')
    {
        session_start();
        session_destroy();
        echo "Logged out";
    }

}