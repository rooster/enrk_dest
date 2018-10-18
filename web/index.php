<?php
require __DIR__ . '/../vendor/autoload.php';
ini_set("display_errors", false);
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
    <h2><?php echo $row[$cellmap["name"]]; ?></h2>
    <div class="sidebar">
      <ul>
        <li>Distance: <?php echo $row[$cellmap["name"]]; ?></li>
        <li>Time (P28 calm): <?php echo $row[$cellmap["time"]]; ?></li>
        <li>Short term parking: <?php echo $row[$cellmap["parking"]]; ?></li>
        <li>Airside toilet: <?php echo $row[$cellmap["toilet"]]; ?></li>
        <li>Airside refreshments: <?php echo $row[$cellmap["refreshments"]]; ?></li>
        <li>Able to easily leave/return: <?php echo $row[$cellmap["leave_return"]]; ?></li>
        <li>Toilet within walking distance: <?php echo $row[$cellmap["toilet_walk"]]; ?></li>
        <li>Refreshments within walking distance: <?php echo $row[$cellmap["refreshments_walk"]]; ?></li>
        <li>100LL available: <?php echo $row[$cellmap["100LL"]]; ?></li>
        <li>Ease of planning/visiting: <?php echo $row[$cellmap["ease_planning"]]; ?></li>
        <li>PPR if required: <?php echo $row[$cellmap["PPR"]]; ?></li>
      </ul>
    </div>
    <div class="content">
      <?php if (!empty($row[$cellmap["attractions"]])): ?>
        <h3>Local attractions / Reasons to visit</h3>
        <p><?php echo nl2br($row[$cellmap["attractions"]]); ?></p>
      <?php endif; ?>
      <?php if (!empty($row[$cellmap["comments"]])): ?>
        <h3>Comments</h3>
        <p><?php echo nl2br($row[$cellmap["comments"]]); ?></p>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<?php include("footer.php");