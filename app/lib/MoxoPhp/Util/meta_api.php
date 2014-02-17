<?php

function get_model() {
	$class = MOXO_META_API_MODEL;
	$model = "\\Models\\{$class}";
	$model = $model::getInstance();	

	return $model;
}


function update_meta( $meta_key, $meta_value ){
	$model = get_model();
	//Se a chave (meta_key) existe
	if ( $model->find( ['meta_key' => $meta_key] ) ) {
		$model->meta_value = $meta_value;

	} else {
		$model->setId(null);
		$model->meta_key   = $meta_key;
		$model->meta_value = $meta_value;
	}

	$model->save();

}

function delete_meta( $meta_key ) {
	$model = get_model();
	if ( $model->find( ['meta_key' => $meta_key] ) ) {
		$model->delete();
	}
}

function get_meta( $meta_key ) {
	$model = get_model();

	if ( $model->find( ['meta_key' => $meta_key] ) ) {
		return $model->meta_value;
	}

	return false;
}

