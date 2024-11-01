<?php

	// The selected branch id
	$branchId = isset($_GET['branch']) ?sanitize_text_field($_GET['branch']) :null;

	// Get the home page's data
	$data = tamkeen_api_request('get', 'plugins/wordPress/home', ['branchId' => $branchId]);

	if(count($data->branches) == 1){
		$branchId = $data->branches[0]->id;
	}

	// Render the view
	return tamkeen_render_view('home', [
		'branchId' => $branchId,
		'branches' => $data->branches,
		'categories' => $data->categories ?? null // Null when no branch is selected
	]);
