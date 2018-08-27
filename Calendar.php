<?php


namespace alkurn\google;

use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\web\View;

define('STDIN', fopen("php://stdin", "r"));

class Calendar
{

    //https://developers.google.com/calendar/create-events

    public $libraries = 'places';
    public $language = 'en-US';
    public $sensor = true;
    public $apiKey = '';
    public $timeZone = 'America/Los_Angeles';
    public $authConfig = '';
    private $redirectUri = '';

    function getClient()
    {
        $client = new \Google_Client();
        $client->setApplicationName('Google Calendar API PHP Quickstart');
        $client->setDeveloperKey($this->apiKey);
        $client->setScopes(\Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig($this->authConfig);
        $client->setAccessType('offline');

        if (!empty($this->redirectUri)) {
            $client->setRedirectUri($this->redirectUri);
        }

        // Load previously authorized credentials from a file.
        if (\Yii::$app->session->has('accessToken')) {
            $accessToken = \Yii::$app->session->get('accessToken');
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            \Yii::$app->response->redirect($authUrl)->send();
            exit;
        }

        $client->setAccessToken($accessToken);
        return $client;
    }


    public function oAuth($token)
    {
        $client = new \Google_Client();
        $client->setApplicationName('Google Calendar API PHP Quickstart');
        $client->setDeveloperKey($this->apiKey);
        $client->setScopes(\Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig($this->authConfig);
        $client->setAccessType('offline');

        if ( !empty($this->redirectUri) ) {
            $client->setRedirectUri($this->redirectUri);
        }
        $accessToken = $client->fetchAccessTokenWithAuthCode($token);
        \Yii::$app->session['accessToken'] = $accessToken;
    }


    public function setRedirectUri($url)
    {
        $this->redirectUri = $url;
    }


    public function summary()
    {
        // Get the API client and construct the service object.
        $client = $this->getClient();
        $service = new \Google_Service_Calendar($client);

        // Print the next 10 events on the user's calendar.
        $calendarId = 'primary';
        $optParams = [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        ];

        $results = $service->events->listEvents($calendarId, $optParams);
        $items = $results->getItems();
        return empty($items) ? false : $items;
    }

    public function create($event)
    {
        $client = $this->getClient();
        $service = new \Google_Service_Calendar($client);
        $event = new \Google_Service_Calendar_Event($event);
        $event = $service->events->insert('primary', $event);
        return $event ? true : false;
    }
}
