
	<style>
		#tamkeen-plugin {max-width: none;}
		#tamkeen-plugin .extra-small{ font-size: .9rem; line-height: 24px }
		#tamkeen-plugin .bi{ margin: 0 5px; }

		#tamkeen-plugin #tamkeen-plugin-content{
			margin-top: 20px;
		}
	</style>

	<script type="text/javascript">
		// Reload the page when the user hits the back button
		window.addEventListener("pageshow", function(event){
			var historyTraversal = event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2);
			if(historyTraversal){
				// Handle page restore.
				window.location.reload();
			}
		});
	</script>

	<div id="tamkeen-plugin" class="container-fluid">
		<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
			<div>
				<nav>
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?php print esc_url(tamkeen_url('?')) ?>"><?php print tamkeen_trans('global.breadcrumb_courses') ?></a></li>

						<!--
							<?php if(isset($branch)): ?>
								<li class="breadcrumb-item"><a href="<?php print esc_url(tamkeen_url('?branch=' . $branch->id)) ?>"><?php print esc_html($branch->name) ?></a></li>
							<?php endif; ?>
	                        -->

						<?php if(isset($category)): ?>
							<li class="breadcrumb-item"><a href="<?php print esc_url(tamkeen_url('?view=category&id=' . $category->id)) ?>"><?php print esc_html($category->name) ?></a></li>
						<?php endif; ?>

						<?php if(isset($course)): ?>
							<li class="breadcrumb-item"><a href="<?php print esc_url(tamkeen_url('?view=course&id=' . $course->id)) ?>"><?php print esc_html($course->name) ?></a></li>
						<?php endif; ?>
					</ol>
				</nav>
			</div>

			<div>
				<?php
					$numCartItems = isset($_SESSION['tamkeen-cart']) ?count($_SESSION['tamkeen-cart']) :0;
				?>

				<i class="bi bi-cart3"></i>

				<?php if($numCartItems > 0): ?>
					<?php print esc_html($numCartItems) ?> <?php print tamkeen_trans('cart.num_items') ?>

					<div>
						<a href="<?php print esc_url(tamkeen_url('?view=cart-request')) ?>" class="btn btn-sm btn-success">
							<i class="bi bi-check-circle-fill"></i>
							<?php print tamkeen_trans('cart.submit_request') ?>
						</a>

						<a href="<?php print esc_url(tamkeen_url('?view=cart-empty')) ?>" class="btn btn-sm btn-outline-secondary">
							<i class="bi bi-x-circle-fill"></i>
							<?php print tamkeen_trans('cart.empty') ?>
						</a>
					</div>

				<?php else: ?>
					<?php print tamkeen_trans('cart.is_empty') ?>

				<?php endif; ?>
			</div>
		</div>

		<div id="tamkeen-plugin-content">