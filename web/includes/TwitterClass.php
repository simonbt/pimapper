<?php
/**
 * web -- Class_Twitter.php
 * User: Simon Beattie
 * Date: 18/12/2013
 * Time: 21:38
 */

require_once(__DIR__ . '/StatusAbstract.php');

class TwitterFuncs extends StatusAbstract{

    function checkTwitterAuth()
    {

            $statement = $this->getPdo()->prepare("SELECT oauth_token FROM twitter WHERE user = :user");
            $statement->execute(array(':user' => "default"));
            $response = $statement->fetch();

            return $response;
    }

    function TwitterAuthFromDB($user)
    {
        $statement = $this->getPdo()->prepare("SELECT oauth_token, oauth_token_secret, user_id, screen_name FROM twitter WHERE user = :user");
        $statement->execute(array(':user' => $user));
        $response = $statement->fetch();

        return $response;
    }

    function DeleteTwitterAuth($user)
    {
        $statement = $this->getPdo()->prepare("DELETE FROM twitter WHERE user = :user");
        $statement->execute(array(':user' => $user));
        $response = $statement->fetch();

        return $response;
    }

    function saveAuth($accessToken)
    {

        $statement = $this->getPdo()->prepare("UPDATE twitter SET oauth_token = ?, oauth_token_secret = ?, user_id = ?, screen_name = ? WHERE user = ?");
        $insertedOk = $statement->execute(array($accessToken['oauth_token'], $accessToken['oauth_token_secret'], $accessToken['user_id'], $accessToken['screen_name'], 'default'));

        if(!$insertedOk)
        {
            die('Sorry, we couldn\'t insert the token details: ' . $statement->errorInfo()[2]);
        }
    }

    function getStatus()
    {
        // get and return twitter status
        $status = $this->getOauth()->get('account/verify_credentials');
        return $status;
    }

    function sendTweet($tweet)
    {
        $tweetSend = $this->getOauth()->post('statuses/update', array('status' => $tweet));
        return $tweetSend;
    }

    function sendDM($user, $message)
    {
        $dm = $this->getOauth()->post('direct_messages/new', array('screen_name' => $user, 'text' => $message));
        return $dm;
    }
}
