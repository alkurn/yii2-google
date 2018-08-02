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
        // $credentialsPath = $this->expandHomeDirectory('credentials.json');
        // https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id=554995591695-lc73an2n2qm4qube3m421r0sop3t4dt7.apps.googleusercontent.com&redirect_uri=http://train2you.alk/sync-oauth&state&scope=https://www.googleapis.com/auth/calendar&approval_prompt=auto

        if (\Yii::$app->session->has('accessToken') && $token = \Yii::$app->session->get('accessToken')) {
            $accessToken = $client->fetchAccessTokenWithAuthCode($token);
            pr($accessToken);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            \Yii::$app->response->redirect($authUrl)->send();
            exit;
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code:';

            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if (!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }

            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
        }

        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }
        return $client;
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

        $event = [
            'summary' => $summary,
            'location' => $location,
            'description' => $description,
            'start' => ['dateTime' => $start, 'timeZone' => $this->timeZone,],
            'end' => ['dateTime' => $end, 'timeZone' => $this->timeZone,],
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

    public function oauth($authCode = null)
    {

        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    }
}
