<?php

	$categoryId = isset($_GET['id']) ?sanitize_text_field($_GET['id']) :null;

	if(empty($categoryId)){
		throw new Exception('No category was selected. Please go back and pick a category to view the courses below it');
	}

	// Get the courses list
	try{
		$data = tamkeen_api_request('get', 'plugins/wordPress/courses/categories/' . $categoryId);
	}catch (Exception $e){}

	// Category was not found
	if(!$data->category){
		throw new Exception('Sorry, the category you have selected does not exist, or not currently published to the catalog!');
	}

	return tamkeen_render_view('courses/category', [
		'branch' => $data->branch,
		'category' => $data->category,
		'courses' => $data->courses
	]);
