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

    public function oAuth($token){

        $client = new \Google_Client();
        $client->setApplicationName('Google Calendar API PHP Quickstart');
        $client->setDeveloperKey($this->apiKey);
        $client->setScopes(\Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig($this->authConfig);
        $client->setAccessType('offline');

        if (!empty($this->redirectUri)) {
            $client->setRedirectUri($this->redirectUri);
        }

        $accessToken = $client->fetchAccessTokenWithAuthCode($token);
        \Yii::$app->session['accessToken'] = $accessToken;
    }

    public function setRedirectUri($url)
    {
        $this->redirectUri = $url;
    }

    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    function expandHomeDirectory($path)
    {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

    public function summary()
    {
        // Get the API client and construct the service object.
        $client = $this->getClient();
        $service = new \Google_Service_Calendar($client);

        // Print the next 10 events on the user's calendar.
        $calendarId = 'primary';
        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        );

        $results = $service->events->listEvents($calendarId, $optParams);
        $items = $results->getItems();

        if (empty($items)) {
            print "No upcoming events found.\n";
        } else {
            print "Upcoming events:\n";
            foreach ($results->getItems() as $event) {
                $start = $event->start->dateTime;
                if (empty($start)) {
                    $start = $event->start->date;
                }
                printf("%s (%s)\n", $event->getSummary(), $start);
            }
        }
    }

    public function create($summary, $location, $description, $start, $end, $emailArray = [])
    {
        $client = $this->getClient();
        $service = new \Google_Service_Calendar($client);

        /*$event = array(
            'summary' => 'Google I/O 2015',
            'location' => '800 Howard St., San Francisco, CA 94103',
            'description' => 'A chance to hear more about Google\'s developer products.',
            'start' => array(
                'dateTime' => '2015-05-28T09:00:00-07:00',
                'timeZone' => 'America/Los_Angeles',
            ),
            'end' => array(
                'dateTime' => '2015-05-28T17:00:00-07:00',
                'timeZone' => 'America/Los_Angeles',
            ),
            'recurrence' => array(
                'RRULE:FREQ=DAILY;COUNT=2'
            ),
            'attendees' => array(
                array('email' => 'lpage@example.com'),
                array('email' => 'sbrin@example.com'),
            ),
            'reminders' => array(
                'useDefault' => FALSE,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 24 * 60),
                    array('method' => 'popup', 'minutes' => 10),
                ),
            ),
        )*/
        //pr($service);
        $event = [
            'summary' => $summary,
            'location' => $location,
            'description' => $description,
            'start' => $start,
            'end' => $end,
            'recurrence' => ['RRULE:FREQ=DAILY;COUNT=2'],
            'attendees' => $emailArray,
            'reminders' => [
                'useDefault' => FALSE,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60],
                    ['method' => 'popup', 'minutes' => 10],
                ],
            ],
        ];

        $event = new \Google_Service_Calendar_Event($event);

        $calendarId = 'primary';
        $event = $service->events->insert($calendarId, $event);
        printf('Event created: %s\n', $event->htmlLink);

    }



}
