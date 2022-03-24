<?php
// CMS file: search results (public)
// last known update: 2013-01-22

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

// fallback, if no parameters have been transmitted
if (implode('',$_GET) == '') {
	header("Location: search.php");
	exit;
}

// url parameters
$dbi->setUserVar ('view',getUrlParameter('view'),'default');
// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'experiment_title');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);


// zu durchsuchende felder und suchsystematik definieren:

// felder, die immer exakt gematcht werden (Trunkierung nicht möglich, Diakritika distinkt, Basiszeichen distinkt)
$exact_fields = array ('ID_experiment', 'ID_institution', 'classification');

// felder, die mit like gematcht werden (Trunkierung möglich, Diakritika distinkt, Basiszeichen ambivalent)
// --> If no diacritics are applied, it finds covers any combination: η would also return ἠ, ἦ or ἥ, while ἠ would find only ἠ.
$like_fields = array ();

//felder, die mit LIKE %xy% gematcht werden
$contain_fields = array('experiment_title', 'funding', 'objective', 'surname');

// felder, die mit like ODER exakt gematcht werden (Trunkierung möglich, Diakritika indistinkt)
// --> Arabic vowel signs are treated indistinctively: سبب would also return سَبَبٌ, and vice versa.
$double_fields = array ();

//fields involving data from tables other than nmv__experiment
//key defines table and column here: unique query
$special_fields = array('ef.ID_foi'			=> 'ID_foi');

// GET-String rekonstruieren (für Blätterfunktion)
$query = array();
foreach ($exact_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($like_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($contain_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($double_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($special_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
$dbi->setUserVar('querystring',implode('&',$query));

// Select-Klauseln erstellen
//$querystring_count = 'SELECT COUNT(e.ID_experiment) AS total FROM nmv__experiment e'; // für Treffer gesamt
$querystring_items = 'SELECT DISTINCT e.ID_experiment, e.experiment_title, e.objective,
																			i.institution_name, e.start_year, e.end_year, GROUP_CONCAT(DISTINCT f.english ORDER BY f.english ASC SEPARATOR "\n") AS fields_of_interest
											FROM nmv__experiment e
											LEFT JOIN nmv__perpetrator_experiment pe		ON e.ID_experiment = pe.ID_experiment
											LEFT JOIN nmv__perpetrator p								ON pe.ID_perpetrator = p.ID_perpetrator
											LEFT JOIN nmv__institution i 								ON i.ID_institution = e.ID_institution
											LEFT JOIN nmv__experiment_foi ef						ON ef.ID_experiment = e.ID_experiment
											LEFT JOIN nmv__field_of_interest f 					ON f.ID_foi = ef.ID_foi
											'; // für Ergebnisliste
$querystring_where = array(); // für Filter

// MySQL-Zeichenfilter definieren (Trunkierungszeichen werden zu MySQL-Zeichen)
$filter_chars = array("'", '%', '_', '*', '٭');
$replace_chars = array('', ' ', ' ', '%', '%');

// Strings zusammenbauen
foreach ($exact_fields as $field) {
    if (getUrlParameter($field)) {
        $querystring_where[] = "e.$field = '".getUrlParameter($field)."'";
    }
}
foreach ($like_fields as $field) {
    if (getUrlParameter($field)) {
		$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
		$querystring_where[] = "TRIM(e.$field) LIKE '%".$filtered_field."%'";
    }
}
foreach ($contain_fields as $field) {
	if (getUrlParameter($field)) {
			if ($field == 'surname'):
				$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
				$querystring_where[] = "p.$field LIKE '%".$filtered_field."%'";
			else:
				$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
				$querystring_where[] = "e.$field LIKE '%".$filtered_field."%'";
			endif;
	}
}
foreach ($double_fields as $field) {
    if (getUrlParameter($field)) {
		$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
		$querystring_where[] = "(e.$field LIKE '".$filtered_field."' OR e.$field = '".getUrlParameter($field)."')";
    }
}
foreach ($special_fields as $key=>$field) {
    if (getUrlParameter($field)) {
			$querystring_where[] = "e.ID_experiment IN (
    SELECT DISTINCT ef.ID_experiment
    FROM nmv__experiment_foi ef
		WHERE ef.ID_foi = '".getUrlParameter($field)."')";
    }
}

if (count($querystring_where) > 0) {
    //$querystring_count .= ' WHERE '.implode(' AND ',$querystring_where);
    $querystring_items .= ' WHERE '.implode(' AND ',$querystring_where);
}
$querystring_items .= ' GROUP BY e.ID_experiment';

// Gesamtanzahl der Suchergebnisse feststellen
$querystring_count = "SELECT COUNT(*) AS total FROM ($querystring_items) AS xyz";
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";


// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

// ausgabe der suchtermini
$suche_nach = array();
if (isset($_GET['ID_experiment']) && $_GET['ID_experiment']) $suche_nach[] = 'ID = '.$_GET['ID_experiment'];
if (isset($_GET['experiment_title']) && $_GET['experiment_title']) $suche_nach[] = 'title = '.$_GET['experiment_title'];
if (isset($_GET['funding']) && $_GET['funding']) $suche_nach[] = 'funding = '.$_GET['funding'];
if (isset($_GET['ID_foi']) && $_GET['ID_foi']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__field_of_interest WHERE ID_foi = '.$_GET['ID_foi'])->fetch_row();
	$suche_nach[] = 'field of interest = '.$search_term[0];
}
if (isset($_GET['objective']) && $_GET['objective']) $suche_nach[] = 'objective = '.$_GET['objective'];
if (isset($_GET['surname']) && $_GET['surname']) $suche_nach[] = 'surname perpetrator = '.$_GET['surname'];
if (isset($_GET['classification']) && $_GET['classification']) {
	$classification = $dbi->connection->query('SELECT english FROM nmv__experiment_classification WHERE ID_exp_classification = '.$_GET['classification'])->fetch_row();
	$suche_nach[] = 'classification = '.$classification[0];
}
if (isset($_GET['ID_institution']) && $_GET['ID_institution']) {
	$institution = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['ID_institution'])->fetch_row();
	$suche_nach[] = 'institution = '.$institution[0];
}

// breadcrumbs
$dbi->addBreadcrumb (L_SEARCH,'search.php');

// layout
$layout
	->set('title',L_RESULTS)
	->set('content',
        '<p>Search for: <em>'.implode(', ',$suche_nach).'</em><br>
				Number of results: '. $total_results->total. '</p>'
        .$dbi->getListView('nmv_experiments_table',$query_items)
        .'<div class="buttons">'
				.createButton (L_MODIFY_SEARCH,'javascript:history.back()','icon search')
        .createButton (L_NEW_SEARCH,'search.php','icon search')
        .'</div>'
	)
	//->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('results'))
	->cast();

?>
