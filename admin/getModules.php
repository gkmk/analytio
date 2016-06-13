<?php
	require("../session_check.php");

	if ($theUser['access'] < 10) header("Location: ../login.php"); 
	
if (!empty($_GET['ID']))
	{
		$theId = $_GET['ID'];
		if (is_numeric($theId))
		{
			require( "../config.php" );	//	vklucuvanje na bazata
			DB_CONN();
		$result = mysql_query("SELECT * FROM modules WHERE `link`='".mysql_real_escape_string($theId)."'");
		if (mysql_num_rows($result) < 1) echo "<h1>No Modules Found</h1>";
		else {
		echo "<table border='1' bordercolor='#CCCCCC'><tr style='background-color: #CCC'><th>Company</th><th>Module</th><th>Module type</th><th>Number of inputs (pat.)</th><th>Number of outputs (pat.)</th><th>Num. of samples (pat.) - train.</th>
<th>Recognition % - train. (pat.)</th><th>Num. of samples - testing (pat.)</th><th>Recognition % - testing (pat.)</th><th>Num. of columns (fore.)</th><th>Tolerance +/- (fore.)</th>
<th>Num. of forecasting values (fore.)</th><th>IP</th><th>Port</th><th>Date</th></tr>";
		
             while ($row = mysql_fetch_array($result)) {

                  $company = ($row['Company']);	// cistenje na vleznite podatoci
				  $modul = ($row['Module']);
				  $modul_type = ($row['modul_type']);
				  $noi = ($row['noi']);	
				  $noo = ($row['noo']);
				  $nos = ($row['nos']);	
				  $recog = ($row['recog']);
				  $nost = ($row['nost']);
				  $recogt = ($row['recogt']);	
				  $noc = ($row['noc']);
				  $tolerance = ($row['tolerance']);	
				  $nofv = ($row['nofv']);
				  $ip = ($row['ip']);
				  $port = ($row['port']);	
				  $date = ($row['date']);
		
                  ?>
                  
                  <tr onclick="selectMod(this)" id="<?php echo $row['id']; ?>">
                  <td><?php echo $company; ?></td>
                  <td><?php echo $modul; ?></td>
                  <td><?php echo $modul_type; ?></td>
                  <td><?php echo $noi; ?></td>
                  <td><?php echo $noo; ?></td>
                  <td><?php echo $nos; ?></td>
                  <td><?php echo $recog; ?></td>
                  <td><?php echo $nost; ?></td>
                  <td><?php echo $recogt; ?></td>
                  <td><?php echo $noc; ?></td>
                  <td><?php echo $tolerance; ?></td>
                  <td><?php echo $nofv; ?></td>
                  <td><?php echo $ip; ?></td>
                  <td><?php echo $port; ?></td>
                  <td><?php echo $date; ?></td>
                  </tr>
                  
                  <?php
                }
			echo "</table>";
		}
		}
	}
?>