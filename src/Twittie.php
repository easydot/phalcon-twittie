<?php

namespace Multidanze\Twittie;

use Phalcon\DiInterface;
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class Mailer
 *
 * @package Multidanze\Twittie
 */
class Twittie
{
    /**
     * @var api_key
     */
    protected $api_key;

    /**
     * @var api_secret
     */
    protected $api_secret;

    /**
     * @var access_token
     */
    protected $access_token;

    /**
     * @var access_secret
     */
    protected $access_secret;

    /**
     * Create a new service provider instance.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        if (!$config->api_key || !$config->api_secret || $config->api_key == 'api_key_here' || $config->api_secret == 'api_secret_here') {
            throw new \RuntimeException('You need a consumer key and secret keys. Get one from <a href="https://dev.twitter.com/apps">dev.twitter.com/apps</a>');
        }

        $this->api_key = $config->api_key;
        $this->api_secret = $config->api_secret;
        $this->access_token = $config->access_token;
        $this->access_secret = $config->access_secret;
    }

    public function getTweets($username, $number, $exclude_replies, $list_slug, $hashtag)
    {
        // Connect
        $connection = $this->getConnectionWithToken();

        // Get Tweets
        if (!empty($list_slug)) {
            $params = array(
                'owner_screen_name' => $username,
                'slug' => $list_slug,
                'per_page' => $number
            );

            $url = '/lists/statuses';
        } else if($hashtag) {
            $params = array(
                'count' => $number,
                'q' => '#'.$hashtag
            );

            $url = '/search/tweets';
        } else {
            $params = array(
                'count' => $number,
                'exclude_replies' => $exclude_replies,
                'screen_name' => $username
            );

            $url = '/statuses/user_timeline';
        }

        $tweets = $connection->get($url, $params);
        return $tweets;
    }

    /**
     * Gets connection with user Twitter account
     * @return Object               Twitter Session
     */
    protected function getConnectionWithToken()
    {
        $connection = new TwitterOAuth($this->api_key, $this->api_secret, $this->access_token, $this->access_secret);

        return $connection;
    }

}
