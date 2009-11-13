<?php
	interface rSQLiteDBUser extends Role {}
	
	interface rSQLiteDBCreator extends rSQLiteDBUser {}
	interface rSQLiteDBReader extends rSQLiteDBUser {}
	interface rSQLiteDBUpdater extends rSQLiteDBUser {}
	interface rSQLiteDBDeleter extends rSQLiteDBUser {}
?>