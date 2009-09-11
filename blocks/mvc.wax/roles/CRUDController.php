<?php
	require_once dirname(__FILE__) . "/Controller.php";
	
	interface CRUDController extends Controller {}
	
	class CRUDControllerActions {
		var $model;
		// always need a few view handlers to get data before a view
		static function initmodel(CRUDController $self) {
			$base = str_replace("Controller","",get_class($self));
			if (class_exists($base . "Model")) {
				$modelname = "${base}Model";
				$self->model = new $modelname();
			}
		}
		static function index(Controller $self) {
			if (!is_object($self->model)) $self->initmodel();
			return $self->model->Read();
		}
		static function edit(Controller $self) {
			if (!is_object($self->model)) $self->initmodel();
			return array_shift($self->model->Read(array("id" => $self->request['id'])));
		}
		
		// the action handlers
		static function create(Controller $self) {
			if (!is_object($self->model)) $self->initmodel();
			return $self->model->Create($self->post);
		}
		static function read(Controller $self) {
			if (!is_object($self->model)) $self->initmodel();
			return $self->model->Read(array("id" => $self->request['id']));
		}
		static function update(Controller $self) {
			if (!is_object($self->model)) $self->initmodel();
			return $self->model->Update($self->post);
		}
		static function delete(Controller $self) {
			if (!is_object($self->model)) $self->initmodel();
			return $self->model->Delete($self->request['id']);
		}
	}
?>