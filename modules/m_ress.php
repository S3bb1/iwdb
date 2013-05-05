<?php
/*****************************************************************************
 * m_ress.php                                                                *
 *****************************************************************************
 * Iw DB: Icewars geoscan and sitter database                                *
 * Open-Source Project started by Robert Riess (robert@riess.net)            *
 * ========================================================================= *
 * Copyright (c) 2004 Robert Riess - All Rights Reserved                     *
 *****************************************************************************
 * This program is free software; you can redistribute it and/or modify it   *
 * under the terms of the GNU General Public License as published by the     *
 * Free Software Foundation; either version 2 of the License, or (at your    *
 * option) any later version.                                                *
 *                                                                           *
 * This program is distributed in the hope that it will be useful, but       *
 * WITHOUT ANY WARRANTY; without even the implied warranty of                *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General *
 * Public License for more details.                                          *
 *                                                                           *
 * The GNU GPL can be found in LICENSE in this directory                     *
 *****************************************************************************
 * Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  *
 * IW-Spielern.                                                              *
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für
//    eine Installation über das Menü
//
$modulname = "m_ress";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Produktion";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation
//    ausführen darf. Mögliche Werte:
//    - ""      <- nix = jeder,
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc =
    "Dieses Modul dient zur Anzeige der Ressproduktion der Spieler in der Allianz." .
        " Dabei wird anhand der Kolo-/Ressübersicht der Tagesbedarf bzw. " .
        " Tagesoutput errechnet.";


//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//

function workInstallDatabase()
{
    /*
    global $db, $db_prefix;

      $sqlscript = array(
        "CREATE TABLE " . $db_prefix . "ressuebersicht( " .
        "`user` varchar(50) NOT NULL default '', ".
        "`datum` int(11) default NULL,  " .
        "`eisen` float default NULL,  " .
        "`stahl` float default NULL,  " .
        "`vv4a` float default NULL,  ".
        "`chem` float default NULL, " .
        "`eis` float default NULL,  " .
        "`wasser` float default NULL,  " .
        "`energie` float default NULL, " .
        "`fp_ph` float default NULL, " .
        "`credits` float default NULL, " .
        "`bev_a` float default NULL, " .
        "`bev_g` float default NULL, " .
        "`bev_q` float default NULL, " .
        "PRIMARY KEY  (`user`))",

      );

      foreach($sqlscript as $sql) {
        $result = $db->db_query($sql);
      }

      doc_success('Installation: Datenbankänderungen = <b>OK</b>');
      */
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu()
{
    global $modultitle, $modulstatus;

    $menu    = getVar('menu');
    $submenu = getVar('submenu');

    $actionparamters = "";
    insertMenuItem($menu, $submenu, $modultitle, $modulstatus, $actionparamters);
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed
// for the configuration file.
//
function workInstallConfigString()
{
    return "";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module.
//

function workUninstallDatabase()
{
    /*
    global $db, $db_tb_ressuebersicht;

      $sqlscript = array(
        "DROP TABLE " . $db_tb_ressuebersicht,
      );

      foreach($sqlscript as $sql) {
        $result = $db->db_query($sql);
      }
      doc_success('Deinstallation: Datenbankänderungen = <b>OK</b>');
      */
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgeführt wenn das Modul mit dem Parameter
// "install" aufgerufen wurde. Beispiel des Aufrufs:
//
//      http://Mein.server/iwdb/index.php?action=ress&was=install
//
// Anstatt "Mein.Server" natürlich deinen Server angeben und default
// durch den Dateinamen des Moduls ersetzen.
//
if (!empty($_REQUEST['was'])) {
    //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
    if ($user_status != "admin") {
        die('Hacking attempt...');
    }

    echo "<h2>Installationsarbeiten am Modul " . $modulname . " (" . $_REQUEST['was'] . ")</h2>\n";

    require_once './includes/menu_fn.php';

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

function make_link($order, $ordered)
{
    echo "<a href='index.php?action=m_ress&order=" . $order . "&ordered=" . $ordered . "'> <img src='".BILDER_PATH."" . $ordered . ".gif' alt='" . $ordered . "'> </a>";
}

//bestehende zeit holen

$sql = "SELECT switch FROM $db_tb_user WHERE id = '{$user_id}';";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);

$switch = $row['switch'];

//Zeit ändern?
?>
    <form action="index.php?action=m_ress" method="post">
        <p>Anzeigen der Produktion für <input type="text" name="switch" size="3"> Stunden <input type="submit" value="speichern" name="form"></p>
    </form>
<?php

if (isset($_POST['switch'])) {
    $switch = (int)$_POST['switch'];
    $db->db_update($db_tb_user, array('switch' => $switch), 'WHERE `id`=' . $user_id);
}

if (empty($switch) OR $switch < 1) {
    $switch = 24;
}
if ($switch === 24) {
    doc_title("Tagesproduktion/-Verbrauch");
} else {
    doc_title("Verbrauch in " . $switch . " Stunde(n)");
}
doc_title("sowie Bevölkerungsdaten");
?>
	<script>
$(document).ready(function() 
    { 
        $("#myTable").tablesorter(); 
    } 
);
</script>

<table id='myTable' class='tablesorter' style='width:100%'>
	<thead>
		<tr>
			<th>
				<b>User</b>
			</th>
			<th>
				<b>Einlesezeit</b>
			</th>
			<th>
				<b>Eisen</b>
			</th>
			<th>
				<b>Stahl</b>
			</th>
			<th>
				<b>VV4A</b>
			</th>
			<th>
				<b>Chemie</b>
			</th>
			<th>
				<b>Eis</b>
			</th>
			<th>
				<b>Wasser</b>
			</th>
			<th>
				<b>Energie</b>
			</th>
			<th>
				<b>FP</b>
			</th>
			<th>
				<b>Credits</b>
			</th>
			<th>
				<b>Hartz IV</b>
			</th>
			<th>
				<b>Volk</b>
			</th>
			<th>
				<b>Quote</b>
			</th>
		</tr>
	</thead>
	</tbody>

	<?php
	global $db, $db_tb_ressuebersicht;

	// Anzeigen der Daten im Browser
	$sql = "SELECT `datum` , `user` , `eisen` , `stahl` , `vv4a` , `chem` , `eis` ," .
		" `wasser` , `energie`, `fp_ph`, `credits`, `bev_a`, `bev_g`, `bev_q` FROM `" . $db_tb_ressuebersicht . "`";
	if (!$user_fremdesitten) {
		$sql .= " WHERE (SELECT allianz FROM " . $db_tb_user . " WHERE id=" . $db_tb_ressuebersicht . ".user) = '" . $user_allianz . "'";
	}
	$result = $db->db_query($sql);


	while ($row = $db->db_fetch_array($result)) {
		$color = getScanAgeColor($row['datum']);

    ?>
		<tr>
			<td>
				<?php
				echo $row['user'] . "<br>";
				?>
			</td>
			<td style="background-color: <?php echo $color ?>">
				<?php
				echo strftime(CONFIG_DATETIMEFORMAT, $row['datum']);
				?>
			</td>
			<td>
				<?php
				echo number_format($row['eisen'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['stahl'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['vv4a'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['chem'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['eis'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['wasser'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['energie'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['fp_ph'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['credits'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['bev_a'], 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['bev_g'], 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['bev_q'], 2, ',', '.');
				?>
			</td>
		</tr>
			
		<?php
		}
		// Gesamtanzeige
		$sql = 	"SELECT sum(`eisen`) as eisen , sum(`stahl`) as stahl, sum(`vv4a`) as vv4a," .
				" sum(`chem`) as chem, sum(`eis`) as eis, sum(`wasser`) as wasser," .
				" sum(`energie`) as energie, sum(`fp_ph`) as fp_ph, sum(`credits`) as credits," .
				" sum(`bev_a`) as bev_a, sum(`bev_g`) as bev_g, sum(`bev_q`)/count(`bev_a`) as bev_q" .
				" FROM " . $db_tb_ressuebersicht;
				$result = $db->db_query($sql);
		while ($row = $db->db_fetch_array($result)) {
		?>
		</tbody>
		<tfoot>
		<tr class='titlebg center'>
			<td colspan='2'>
				<b>Gesamt:</b>
			</td>
			<td>
				<?php
				echo number_format($row['eisen'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['stahl'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['vv4a'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['chem'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['eis'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['wasser'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['energie'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['fp_ph'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['credits'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['bev_a'], 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['bev_g'], 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo "&Oslash;" . number_format($row['bev_q'], 2, ',', '.');
				?>
			</td>
		</tr>
		<?php
		}
		?>
	</tfoot>
</table>
		
<?php	

// 
// Erweiterung für Zusatz-Tabellen; sortiert nach Usern, die einem Fleeter zugeordnet sind
// 

global $db, $db_tb_ressuebersicht, $db_tb_user;

$sql2 = "SELECT
            us.id, us.sitterlogin, us.budflesol
        FROM
            " . $db_tb_user . " as us
        WHERE
            us.budflesol = 'Fleeter'";

$result2 = $db->db_query($sql2);

$fleeterlist = array();
while ($row = $db->db_fetch_array($result2)) {
    $fleeterlist[] = $row;
}

function NumToStaatsform($num)
{
    if ($num == 1) {
        return 'Diktator';
    } else if ($num == 2) {
        return 'Monarch';
    } else if ($num == 3) {
        return 'Demokrat';
    } else if ($num == 4) {
        return 'Kommunist';
    }

    return '---';
}

foreach ($fleeterlist as $key => $value) {

    $fleetername = $value['id'];

    echo "\n\n<br><br>\n\n";
	?>
	<table <table id='myTable' class='tablesorter' style='width:100%'>
		<thead>
			<tr class="titlebg center">
				<th colspan='13'>
					<?php
					if ($fleetername == $value['sitterlogin']) {
						echo "<b>Fleeter: " . $fleetername . "</b>";
					} else {
						echo "<b>Fleeter: " . $fleetername . "<br>(ingamenick &laquo;" . $value['sitterlogin'] . "&raquo;)</b>";
					}
					echo "<br>";
					?>
				</th>
			</tr>
			<tr>
				<th>
					<b>User</b>
				</th>
				<th>
					<b>Einlesezeit</b>
				</th>
				<th>
					<b>Eisen</b>
				</th>
				<th>
					<b>Stahl</b>
				</th>
				<th>
					<b>VV4A</b>
				</th>
				<th>
					<b>Chemie</b>
				</th>
				<th>
					<b>Eis</b>
				</th>
				<th>
					<b>Wasser</b>
				</th>
				<th>
					<b>Energie</b>
				</th>
				<th>
					<b>FP</b>
				</th>
				<th>
					<b>Credits</b>
				</th>
				<th>
					<b>Spieltyp</b>
				</th>
				<th>
					<b>Staatsform</b>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php    

		// Anzeigen der Daten im Browser
		$sql3 = "SELECT
					ro.datum, ro.user, ro.eisen, ro.stahl, ro.vv4a, ro.chem, ro.eis, ro.wasser, 
					ro.energie, ro.fp_ph, ro.credits,
					us.sitterlogin, us.buddlerfrom, us.budflesol, us.staatsform
				FROM
					" . $db_tb_ressuebersicht . " as ro, " . $db_tb_user . " as us
				WHERE
					ro.user = us.sitterlogin AND us.buddlerfrom = '" . $fleetername;
				
		$result3 = $db->db_query($sql3);

		while ($row = $db->db_fetch_array($result3)) {
			$color = getScanAgeColor($row['datum']);

        ?>
		
			<tr>
				<td>
					<?php
					echo $row['user'] . "<br>";
					?>
				</td>
				<td style="background-color: <?php echo $color ?>">
					<?php
					echo strftime("%d.%m.%y<br>%H:%M:%S", $row['datum']);
					?>
				</td>
				<td>
					<?php
					echo number_format($row['eisen'] * $switch, 0, ',', '.');
					?>
				</td>
				<td>
					<?php
					echo number_format($row['stahl'] * $switch, 0, ',', '.');
					?>
				</td>
				<td>
					<?php
					echo number_format($row['vv4a'] * $switch, 0, ',', '.');
					?>
				</td>
				<td>
					<?php
					echo number_format($row['chem'] * $switch, 0, ',', '.');
					?>
				</td>
				<td>
					<?php
					echo number_format($row['eis'] * $switch, 0, ',', '.');
					?>
				</td>
				<td>
					<?php
					echo number_format($row['wasser'] * $switch, 0, ',', '.');
					?>
				</td>
				<td>
					<?php
					echo number_format($row['energie'] * $switch, 0, ',', '.');
					?>
				</td>
				<td>
					<?php
					echo number_format($row['fp_ph'] * $switch, 0, ',', '.');
					?>
				</td>
				<td>
					<?php
					echo number_format($row['credits'] * $switch, 0, ',', '.');
					?>
				</td>
				<td>
					<?php
					echo $row['budflesol'];
					?>
				</td>
				<td>
					<?php
					echo NumToStaatsform($row['staatsform']);
					?>
				</td>
			</tr>
		
		<?php
		}
		// Gesamtanzeige
		$sql = "SELECT
					sum(ro.eisen) as eisen, sum(ro.stahl) as stahl, sum(ro.vv4a) as vv4a,
					sum(ro.chem) as chem, sum(ro.eis) as eis, sum(ro.wasser) as wasser,
					sum(ro.energie) as energie, sum(ro.fp_ph) as fp_ph,
					sum(ro.credits) as credits, ro.user, us.sitterlogin, us.buddlerfrom
				FROM
					" . $db_tb_ressuebersicht . " as ro, " . $db_tb_user . " as us
				WHERE
					ro.user = us.sitterlogin AND us.buddlerfrom = '" . $fleetername . "'
				GROUP BY
					us.buddlerfrom";

		$result = $db->db_query($sql);

		while ($row = $db->db_fetch_array($result)) {
        ?>
		</tbody>
		<tfoot>
		<tr class='titlebg center'>
			<td colspan='2'>
				<b>Gesamt:</b>
			</td>
			<td>
				<?php
				echo number_format($row['eisen'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['stahl'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['vv4a'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['chem'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['eis'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['wasser'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['energie'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['fp_ph'] * $switch, 0, ',', '.');
				?>
			</td>
			<td>
				<?php
				echo number_format($row['credits'] * $switch, 0, ',', '.');
				?>
			</td>
			<td colspan='2'>
				<?php
				echo "Gesamt";
				?>
			</td>
		</tr>
	</tfoot>
	<?php
	}
	?>
</table>
<?php
}
?>
<script src="javascript/jquery.tablesorter.min.js"></script>