<?php
	require_once "../wax_init.php";
	
	Wax::LoadBlock("couchdb");
	ob_end_flush();
	
	// role initialization -- set properties for this instance
	class PostModel extends CouchDBPersistenceModel {
		var $dbhost = "localhost";
		var $dbport = 5984;
		var $dbname = "test";
		
		var $_id_alias = "title";
		
		var $views = array(
			"all" => array("map" => "function(doc) { if (doc.type == 'post') emit(doc._id,doc); }"),
		);
	}
	
	$couch = new PostModel("TestPost");
	echo $couch;
	
	$couch['content'] = "Updated! WooHoo!";
	$couch->Save();

	echo $couch;
?>