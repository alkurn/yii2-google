<?php


namespace alkurn\google;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\web\View;

define('STDIN', fopen("php://stdin", "r"));

class Peoples
{
    public $clientId = '327158899199-ksrn2s1msl3gptvum6dlcf7dm3le34fn.apps.googleusercontent.com';
    public $clientSecret = 'elqOLRcZnGfzWbrDqtB5XmmU';
    private $redirectUri = 'https://socialcross.org/import';

    function getClient()
    {
        $client = new \Google_Client();
        $client->setClientId($this->clientId);
        $client->setClientSecret($this->clientSecret);
        $client->setRedirectUri($this->redirectUri);
        $client->addScope('profile');
        $client->addScope('https://www.googleapis.com/auth/contacts.readonly');
        $oauth = Yii::$app->request->get('oauth');
        $code = Yii::$app->request->get('code');

        if (isset($oauth) && !empty($oauth)) {
            // Start auth flow by redirecting to Google's auth server
            $auth_url = $client->createAuthUrl();
            $this->redirect($auth_url);
        } else if (isset($code) && !empty($code)) {
            $client->authenticate($code);
            Yii::$app->session['accessToken'] = $client->getAccessToken();
            $this->redirect($this->redirectUri);
        } else if (Yii::$app->session->has('accessToken')) {
            // You have an access token; use it to call the People API
            $client->setAccessToken(Yii::$app->session['accessToken']);
            //$people_service = new \Google_Service_PeopleService($client);
            return $client;
            // TODO: Use service object to request People data
        } else {
            $this->redirect($this->redirectUri . '?oauth=1');
        }
        return $client;
    }

    public function importContacts($accessToken)
    {
        $access_token = $accessToken['access_token'];
        $max_results = 200;
        $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=' . $max_results . '&alt=json&v=3.0&oauth_token=' . $access_token;
        $json_response = $this->retrieveContacts($url);
        $contacts = json_decode($json_response, true);
        $return = [];
        if (isset($contacts['feed']['entry']) && !empty($contacts['feed']['entry'])) {
            foreach ($contacts['feed']['entry'] as $contact) {
                //retrieve user photo
                //$image = $this->retrieveUserPhoto($contact, $access_token);
                //retrieve Name + email and store into array
                $return[] = array(
                    'name' => $contact['title']['$t'],
                    'email' => $contact['gd$email'][0]['address'],
                  //  'image' => $image
                );
            }
        }
        return $return;
    }


    function retrieveUserPhoto($contact, $access_token)
    {

        $image = null;
        if (isset($contact['link'][0]['href'])) {
            $url = $contact['link'][0]['href'];
            $url = $url . '&access_token=' . urlencode($access_token);
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 15);
            curl_setopt($curl, CURLOPT_VERBOSE, true);
            $image = curl_exec($curl);
            curl_close($curl);
        }
        return $image;
    }

    function retrieveContacts($url, $post = "")
    {
        $curl = curl_init();
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
        curl_setopt($curl, CURLOPT_URL, $url);
        //The URL to fetch. This can also be set when initializing a session with curl_init().
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        //The number of seconds to wait while trying to connect.
        if ($post != "") {
            curl_setopt($curl, CURLOPT_POST, 5);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        //The contents of the "User-Agent: " header to be used in a HTTP request.
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        //To follow any "Location: " header that the server sends as part of the HTTP header.
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
        //To automatically set the Referer: field in requests where it follows a Location: redirect.
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        //The maximum number of seconds to allow cURL functions to execute.
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        //To stop cURL from verifying the peer's certificate.
        $contents = curl_exec($curl);
        curl_close($curl);
        return $contents;
    }

}
