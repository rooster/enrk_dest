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

$headers = array("name", "distance", "time", "parking", "toilet", "refreshments", "leave_return",
                 "toilet_walk", "refreshments_walk", "100LL", "ease_planning", "PPR", "attractions",
                 "comments", "links");
$cellmap = array_flip($headers);

include("header.php");

?>

<div class="wrapper">
  <h1 style="background-color: #fff;">ENRK Destinations</h1>
  <?php foreach ($values as $row): ?>
    <?php if (empty($row[$cellmap["name"]])) continue; ?>
    <header class="header"><?php echo $row[$cellmap["name"]]; ?></header>
    <aside class="sidebar">Sidebar</aside>
    <article class="content">
      <?php print_r($row); ?>
    </article>
    <footer class="footer">My footer</footer>
  <?php endforeach; ?>
</div>

<?php include("footer.php");