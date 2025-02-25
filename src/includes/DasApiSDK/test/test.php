<?php 
require __DIR__ . '/vendor/autoload.php';

use Das\CallRail;
use Das\CoreBridge;

$test = new CallRail('$2y$10$ORqrH2PpCvH3GaFI7hoRJOyESJyYzBSy27QTfi9l0BfRk/70Mc8oy1');

$params = array(
				 'company_id' => 'COM64c789f365a447c191d4399fa8493a0b',
				 'termnum' => '9548938112',
				 'recording' => true,
				 'countrycode' => '+1',
				 'name' => 'Test CTN',
				 'whisper_message' => 'Test a Test'
			   );


print_r($test->createTracker($params));
print_r($test->getCompany());

/*
$test1 = new CoreBridge('$2y$10$ORqrH2PpCvH3GaFI7hoRJOyESJyYzBSy27QTfi9l0BfRk/70Mc8oy1');
$lead  = array(
				'storeid' => '1116502',
				'companyName' => 'DasTest12',
				'companyPhone' => '3052468655',
				'firstName' => 'Adriano1',
				'lastName' => 'Pere1',
				'email' => 'email@test.com',
				'zipcode' => '',
			   );

print_r($test1->createLead($lead));
*/

?>