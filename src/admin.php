
	<h1>Tamkeen integration setup</h1>

	<form method="post" action="options.php">
		<?php
			settings_fields('tamkeen');
			do_settings_sections('tamkeen');

			submit_button();
		?>
	</form>