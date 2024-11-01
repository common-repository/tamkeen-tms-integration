
    <header class="d-flex align-items-center my-4">
        <div class="me-4">
            <img src="<?php print esc_url($category->thumbnail_url) ?>" style="max-height: 100px"
                 alt="<?php print esc_attr($category->name) ?>"/>
        </div>
        <div>
            <h3><?php print esc_html($category->name) ?></h3>
            <p class="text-muted small"><?php print esc_html($category->description) ?></p>
        </div>
    </header>

    <?php if(count($courses) == 0): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <?php print tamkeen_trans('category.no_courses') ?>
        </div>

    <?php else: ?>
        <div id="category-courses" class="row row-cols-1 row-cols-md-<?php print esc_attr(get_option('tamkeen_grid_items_per_row') ?: 4) ?> g-4">
            <?php foreach($courses as $course): ?>
                <?php $url = tamkeen_url('?view=course&id=' . $course->id) ?>

                <div class="col course">
                    <div class="card shadow">
                        <?php if(! empty($course->catalog->thumbnail)): ?>
                            <a href="<?php print esc_url($url) ?>">
                                <?php if($course->catalog->featured): ?>
                                    <div class="badge bg-primary small m-3 position-absolute" style="z-index: 100">
                                        <i class="bi bi-star"></i> <?php print tamkeen_trans('global.course_is_featured') ?>
                                    </div>
                                <?php endif; ?>

                                <img src="<?php print esc_url($course->catalog->thumbnail_url) ?>" class="card-img-top" alt="<?php print esc_attr($course->name) ?>">
                            </a>
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?php print esc_url($url) ?>" class="text-primary">
                                    <?php print esc_html($course->name) ?>
                                </a>

                                <?php if($course->catalog->featured && empty($course->catalog->thumbnail)): ?>
                                    <i class="ms-1 bi bi-star-fill text-warning extra-small"></i>
                                <?php endif; ?>
                            </h5>

                            <b class="card-text extra-small"><?php print esc_html($course->code) ?></b>

                            <?php if(! empty($course->description)): ?>
                                <p class="card-text extra-small text-muted">
                                    <?php print tamkeen_str_limit(esc_html($course->description), 100) ?>
                                </p>
                            <?php endif; ?>

                            <ul class="list-inline extra-small mt-2">
                                <li class="list-inline-item">
                                    <i class="bi bi-cash-stack"></i>
                                    <b><?php print esc_html($course->cost) ?></b>
                                    <span><?php print esc_html($course->currency) ?></span>

                                    <?php if($course->cost_basis != 'trainee'): ?>
                                        <span class="text-muted">(<?php print esc_html($course->display_cost_basis) ?>)</span>
                                    <?php endif; ?>
                                </li>

                                <?php if($course->duration): ?>
                                    <li class="list-inline-item">
                                        <i class="bi bi-clock"></i>
                                        <b><?php print esc_html($course->duration) ?></b>
                                        <span><?php print esc_html($course->display_duration_unit) ?></span>
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <?php if(isset($_SESSION['tamkeen-cart']) && is_array($_SESSION['tamkeen-cart']) && array_search($course->id, $_SESSION['tamkeen-cart']) !== false): ?>
                                <a href="<?php print esc_url(tamkeen_url('?view=cart-remove&courseId=' . $course->id)) ?>"
                                   class="d-block mt-2 btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <?php print tamkeen_trans('global.button_cancel_course_request') ?>
                                </a>

                            <?php elseif($course->catalog->accepts_requests == true): ?>
                                <a href="<?php print esc_url(tamkeen_url('?view=cart-add&courseId=' . $course->id)) ?>"
                                   class="d-block mt-2 btn btn-sm btn-outline-primary">
                                    <i class="bi bi-cart3"></i>
                                    <?php print tamkeen_trans('global.button_request_course') ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>