<?php
	if (isset(($_POST['pageChooserSubmit'])))
		$pageToUse = $_POST['pageChooser'];
	else
		$pageToUse = $_GET['page'];
?>
<html>
	<head>
		<title>Adam&#39;s CalToICS | Importer</title>
	</head>
	<body>
		<form id="pageChooserForm" name="pageChooserForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
			<label for="pageChooser">Enter the URL for the page which contains the calendar:</label>
			<input id="pageChooser" name="pageChooser" type="text" value="<?= $pageToUse ?>">

			<input id="pageChooserSubmit" name="pageChooserSubmit" type="submit" value="Find Events" form="pageChooserForm">
		</form>

		<?php
		// If the form has been submitted:
		if (isset($_POST['pageChooserSubmit']))
		{
			// Includes file made by Jose Solorzano to extract some of the data from the HTML source of the page provided below.
			include_once("./simple_html_dom.php");

			// Example page:
			//$page = file_get_html('http://uiwwwsylp01.nottingham.ac.uk:8001/reporting/TextSpreadsheet;programme+of+study;id;0003091%0D%0A?days=1-5&weeks=1-52&periods=1-32&template=SWSCUST+programme+of+study+TextSpreadsheet&height=100&week=100');

			// Entered page:
			$page = file_get_html($_POST['pageChooser']);

			// Finds all data in 'td' HTML elements.
			$individualCells = $page->find('td');

			// Counts through each of the elements in $individualCells.
			$arrayCounter = 0;

			// Loops through all elements in $individualCells.
			foreach($individualCells as $cell)
			{
				// If the regular expression is found, is fits the description for an activity name (module code).
				if (preg_match('/G[0-9][0-9](.*)/', $cell->plaintext))
				{
					// Keeps the $arrayCounter value for the start of each module.
					$eventStartIDs[] = $arrayCounter;
				}

				$arrayCounter++;
			}

			// A counter to count through each event.
			$eventCounter = 0;

			foreach($eventStartIDs as $singleStartID)
			{
				$events[$eventCounter]["activity"] = $individualCells[$singleStartID + 0]->plaintext;
				$events[$eventCounter]["module"] = $individualCells[$singleStartID + 1]->plaintext;
				$events[$eventCounter]["nameOfType"] = $individualCells[$singleStartID + 2]->plaintext;
				$events[$eventCounter]["size"] = $individualCells[$singleStartID + 3]->plaintext;
				$events[$eventCounter]["day"] = $individualCells[$singleStartID + 4]->plaintext;
				$events[$eventCounter]["start"] = $individualCells[$singleStartID + 5]->plaintext;
				$events[$eventCounter]["end"] = $individualCells[$singleStartID + 6]->plaintext;
				$events[$eventCounter]["duration"] = $individualCells[$singleStartID + 7]->plaintext;
				$events[$eventCounter]["room"] = $individualCells[$singleStartID + 8]->plaintext;
				$events[$eventCounter]["roomDescription"] = $individualCells[$singleStartID + 9]->plaintext;
				$events[$eventCounter]["roomSize"] = $individualCells[$singleStartID + 10]->plaintext;
				$events[$eventCounter]["staff"] = $individualCells[$singleStartID + 11]->plaintext;
				$events[$eventCounter]["weeks"] = $individualCells[$singleStartID + 12]->plaintext;

				$eventCounter++;
			}

			$roomNames = array(
								"UP-CLIVEG" => "Sir Clive Granger Building",
								"UP-HALLWARD" => "Hallward Library",
								"UP-HEMSLEY" => "University Staff Club",
								"UP-HIGH" => "Highfield House",
								"UP-HUGHST" => "Hugh Stewart House",
								"UP-LASS" => "Law and Social Sciences Building",
								"UP-PORT" => "Portland Building",
								"UP-TRNT" => "Trent Building",

 								"UP-ARCH-SRB" => "Sustainable Research Building",
								"UP-ARTCEN" => "Arts Centre Lecture Theatre",
								"UP-BOOTS" => "Boots Science Building",
								"UP-COATESRD" => "Coates Road Auditorium",
								"UP-CHEM" => "Chemistry Building",
								"UP-COAT" => "Coates Building",
								"UP-ESLC" => "Engineering and Science Learning Centre",
								"UP-GEOGREEN" => "George Green Library",
								"UP-KEIGHTON AUD" => "Keighton Auditorium",
								"UP-KEIGHTON" => "Keighton Auditorium",
								"UP-LIFESCI" => "Life Sciences Building",
								"UP-MATH" => "Mathematical Sciences Building",
								"UP-PHARM" => "Pharmacy Building",
								"UP-PHYS" => "Physics Building",
								"UP-POPE" => "Pope Building",
								"UP-PRB" => "Pavement Research Building",
								"UP-PSYC" => "Psychology Building",
								"UP-TOWER" => "Tower Block",
								"UP-WOLF" => "Wolfson Building",

								"UP-HUMS" => "Humanities Building",
								"UP-LENG" => "Lenton Grove",
								"UP-WILL" => "Willoughby Hall",

								"NMS-MEDSCH" => "Nottingham Medical School (NMS)",
								"QMC-STHBLK" => "South Block",

								"JC-AEROSPACE-TEC" => "Aerospace Technology Centre",
								"JC-AMEN" => "Amenities Building",
								"JC-BSNORTH" => "Business School North",
								"JC-BSSOUTH" => "Business School South",
								"JC-DEARING" => "Dearing Building",
								"JC-ENERGY-TEC" => "Energy Technologies Building",
								"JC-EXCHGE" => "The Exchange Building",
								"JC-NGB" => "Nottingham Geospatial Building",
								"JC-SI-YUAN-CENTRE" => "The Si Yuan Centre of Contemporary Chinese Studies",
								"JC-YANG-Fujia" => "YANG Fujia Building",

								"CITY-CLINSCI" => "Clinical Sciences Building&nbsp;",

								"SB-GATE" => "Gateway&nbsp;Building",
								"SB-LECTBLK" => "Lecture Room Block&nbsp;",
								"SB-MAINBLDG" => "Main Building",
								"SB-PLANTSCI" => "Plant Sciences Building",
								"SB-VETSCH" => "Veterinary Sciences Building",
								"SB-FOODSCI" => "Food Sciences Building",

								"DERBY-DMS" => "Derby Medical School",
								"DERBY-DSN" => "Derby School of Nursing",

								"KMC-KM" => "Kings Meadow Campus"
								);

			// Replaces all room codes with the room name
			for($i = 0; $i < $eventCounter; $i++)
			{
				// Need to try and change the order of the room name in the building.
				/*if (strtr($events[$i]["room"], $roomNames))
				{
					$events[$i]["room"] = 
				}*/

				$events[$i]["room"] = strtr($events[$i]["room"], $roomNames);
			}

			echo "It is recommended that you import the file into a calendar seperate to other events. If the wrong calendar is imported, it may be difficult to delete only those imported using this file.";
			echo "<br>";
			echo "Great. We've imported the events. Before proceeding, please verify that the first event is correct:";
			echo "<ul>";
				echo "<li><strong>Activity</strong>: ".$events[0]["activity"]."</li>";#
				echo "<li><strong>Module</strong>: ".$events[0]["module"]."</li>";#
				echo "<li><strong>Name of Type</strong>: ".$events[0]["nameOfType"]."</li>";#
				echo "<li><strong>Size</strong>: ".$events[0]["size"]."</li>";#
				echo "<li><strong>Day</strong>: ".$events[0]["day"]."</li>";
				echo "<li><strong>Start</strong>: ".$events[0]["start"]."</li>";
				echo "<li><strong>End</strong>: ".$events[0]["end"]."</li>";
				echo "<li><strong>Duration</strong>: ".$events[0]["duration"]."</li>";#
				echo "<li><strong>Room</strong>: ".$events[0]["room"]."</li>";#
				echo "<li><strong>Room Description</strong>: ".$events[0]["roomDescription"]."</li>";
				echo "<li><strong>Room size</strong>: ".$events[0]["roomSize"]."</li>";
				echo "<li><strong>Staff</strong>: ".$events[0]["staff"]."</li>";
				echo "<li><strong>Weeks</strong>: ".$events[0]["weeks"]."</li>";
			echo "</ul>";

			// For debugging; shows the entire contents of $event.
			/*echo "<pre>";
			print_r($events);
			echo "</pre>";*/

		?>

		<form id="eventsConfirmedForm" name="eventsConfirmedForm" method="post" action="./export.php" enctype="multipart/form-data">
			<input id="dataForExport" name="dataForExport" type="hidden" value="<?php echo base64_encode(serialize($events)); ?>">
			<input id="URLForImport" name="URLForImport" type="hidden" value="<?php echo $_POST['pageChooser']; ?>">

			<input id="eventsConfirmedSubmit" name="eventsConfirmedSubmit" type="submit" value="Confirm Events and Build ICS FIle" form="eventsConfirmedForm">
		</form>
	</body>
</html>	


			<?php
		}

		?>