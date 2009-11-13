<?php
	require_once dirname(__FILE__) . "/Controller.php";
	
	interface rScaffold extends rController {}
	
	class rScaffoldActions {
		var $model;
		
		// always need a few view handlers to get data before a view
		static function initmodel(rScaffold $self) {
			$base = str_replace("Controller","",get_class($self));
			$modelname = $base;
			if (!class_exists($modelname))
				$base .= "Model";
			if (class_exists($modelname)) {
				$self->model = new $modelname();
			}
		}
		
		static function index(rScaffold $self) {
			if (!is_object($self->model)) $self->initmodel();
			return $self->model->Read();
		}
		static function edit(rScaffold $self) {
			if (!is_object($self->model)) $self->initmodel();
			$results = $self->model->Read($self->request['id']);
			return $results;
		}
		
		// the action handlers
		static function create(rScaffold $self) {
			if (!is_object($self->model)) $self->initmodel();
			foreach ($self->post as $var => $val) {
				$self->model[$var] = $val;
			}
			return $self->model->Save();
		}
		static function read(rScaffold $self) {
			if (!is_object($self->model)) $self->initmodel();
			return $self->model->Read($self->request['id']);
		}
		static function update(rScaffold $self) {
			if (!is_object($self->model)) $self->initmodel();
			
			foreach ($self->post as $var => $val) {
				$self->model[$var] = $val;
			}
			return $self->model->Save();
		}
		static function delete(rScaffold $self) {
			if (!is_object($self->model)) $self->initmodel();
			return $self->model->Delete($self->request['id'], $self->request['rev']);
		}
	}
?>