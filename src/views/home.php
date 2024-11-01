
		<script>
			var selectedBranchId = <?php print $branchId ?: 'null' ?>,
				baseUrl = '<?php print tamkeen_url() ?>';

			jQuery(function(){
				let branchMenu = jQuery('#branch-select');

				// Watch the branch menu [change]
				branchMenu.on('change', function(){
					// Get the selected branch
					let id = jQuery(this).val();

					if(!!id){
						location.href = baseUrl + '?branch=' + id;
					}
				});

				if(!!selectedBranchId){
					branchMenu.val(selectedBranchId);
				}
			});
		</script>

		<?php if(count($branches) > 1): ?>
			<div class="row mb-5">
				<div class="col-sm-12 col-md-6">
					<label class="form-label" for="branch-select"><?php print tamkeen_trans('home.select_branch') ?></label>
					<select id="branch-select" name="branch" class="form-control">
						<option value=""></option>

						<?php foreach($branches as $branch): ?>
						<option value="<?php print esc_attr($branch->id) ?>"><?php print esc_html($branch->name) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		<?php endif; ?>

		<?php if(!empty($branchId)): ?>
			<div class="mb-4">
				<h4><?php print tamkeen_trans('home.categories_list') ?></h4>
				<p class="small"><?php print tamkeen_trans('home.pick_category_hint') ?></p>
			</div>

			<?php if(count($categories) > 0): ?>
				<div id="course-categories" class="row row-cols-1 row-cols-md-<?php print esc_html(get_option('tamkeen_grid_items_per_row') ?: 4) ?> g-4">
					<?php foreach($categories as $category): ?>
						<?php $url = tamkeen_url('?view=category&id=' . $category->id) ?>

						<div class="col category" data-branch-id="<?php print esc_attr($category->branch_id) ?>">
							<div class="card shadow">
								<a href="<?php print esc_url($url) ?>">
									<img src="<?php print esc_url($category->thumbnail_url) ?>" class="card-img-top" alt="<?php print esc_attr($category->name) ?>">
								</a>
								<div class="card-body text-center">
									<h5 class="card-title">
										<a href="<?php print esc_url($url) ?>">
											<?php print esc_html($category->name) ?>
										</a>
									</h5>

									<?php if(!empty($category->description)): ?>
										<p class="card-text extra-small text-muted">
											<?php print tamkeen_str_limit(esc_html($category->description), 50) ?>
										</p>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

			<?php else: ?>
				<div class="alert alert-info">
					<i class="bi bi-info-circle-fill"></i>
					<?php print tamkeen_trans('home.no_categories') ?>
				</div>
			<?php endif; ?>

		<?php endif; ?>
