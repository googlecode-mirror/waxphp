<?php
	interface SQLiteDBUser extends Role {}
	
	interface SQLiteDBCreator extends SQLiteDBUser {}
	interface SQLiteDBReader extends SQLiteDBUser {}
	interface SQLiteDBUpdater extends SQLiteDBUser {}
	interface SQLiteDBDeleter extends SQLiteDBUser {}
?>