<?php
require __DIR__ . '/../vendor/autoload.php';

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient()
{
  putenv('GOOGLE_APPLICATION_CREDENTIALS='.__DIR__.'/../service-account.json');
  $client = new Google_Client();
  $client->useApplicationDefaultCredentials();
  
  $scopes = implode(' ', array(
    Google_Service_Sheets::SPREADSHEETS,
    Google_Service_Sheets::DRIVE,
    Google_Service_Sheets::DRIVE_FILE
  ));

  $client->setScopes($scopes);
  
  return $client;
}

$settings = json_decode(file_get_contents(__DIR__ . "/../settings.json"), true);

// Get the API client and construct the service object.
$client  = getClient();
$service = new Google_Service_Sheets($client);


$range    = 'Sheet1';
$response = $service->spreadsheets_values->get($settings["spreadsheetId"], $range);
$values   = $response->getValues();

if (empty($values)) {
    print "No data found.\n";
} else {
  echo "<table>";   
  foreach ($values as $row) {
    echo "<tr>";
    foreach ($row as $cell) {
      echo "<td>" . $cell . "</td>";
    }
    echo "</tr>";
  }
  echo "</table>";
}