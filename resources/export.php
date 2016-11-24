<?php
	//We need to set a Content-Disposition in order to trigger a download
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="klantgegevens.csv"');

	//Require all the general functions
    require_once $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';
    
	if(isset($_POST['exportgegevens']) && $_POST['action'] == 'export') {
		//First check if the user has authority
		checkAuthority('klantenexporteren');
		
		//Assign the connection to a local connection variable
		$connection = getConnection();		
		
		//Array to store all the columns that need to be exported
		$columnsToExport = $_POST['exportgegevens'];
		
		//Here we create the select query with the data of $columnsToExport
		$columnsMysql = 'select '.filterData(implode(', ', $columnsToExport)).' from klant';
		
		//Here we create the select query
		$query = $connection->prepare($columnsMysql);
		
		$query->execute();
		
		//Array which contains all the fetched customerdata
		$fetchedCustomerData = $query->fetchAll(PDO::FETCH_ASSOC);		
		
		//Here we start the CSV creation
		$file = fopen('php://output', 'w');
		
		//Add the headings to the CSV
		fputcsv($file, $columnsToExport, ';');
		
		//Check if the array fetchedCustomerData is not empty and add
		//the data to the CSV file.
		if(!empty($fetchedCustomerData)) {
			foreach ($fetchedCustomerData as $data) {
				fputcsv($file, $data, ';');
			}
		}
		
		fclose($file);		
	} else {
		header('location: /omega/index.php');
	}
?>
