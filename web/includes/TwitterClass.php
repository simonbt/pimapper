<?php
/**
 * web -- Class_Twitter.php
 * User: Simon Beattie
 * Date: 18/12/2013
 * Time: 21:38
 */

require_once(__DIR__ . '/StatusAbstract.php');

class TwitterFuncs extends StatusAbstract{

    function saveAuth($accessToken)
    {
        $statement = $this->getPdo()->prepare("INSERT oauth_token, oauth_token_secret, user_id, screen_name, INTO twitter VALUES(?, ?, ?, ?)");
        $insertedOk = $statement->execute(array($accessToken['oauth_token'], $accessToken['oauth_token_secret'], $accessToken['user_id'], $accessToken['screen_name']));

        if(!$insertedOk)
        {
            die('Sorry, we couldn\'t insert the token details: ' . $insertedOk->errorInfo()[2]);
        }
    }

    function getStatus()
    {
        // get and return twitter status
        $status = $this->getOauth()->get('account/verify_credentials');
        return $status;
    }

    function sendTweet($connection, $tweet)
    {
        $this->getOauth()->post('statuses/update', array('status' => $tweet));
        return true;
    }

    function sendDM($connection, $user, $message)
    {
        $dm = $this->getOauth()->post('direct_messages/new', array('user_id' => $user, 'text' => $message));
        return $dm;
    }
}
