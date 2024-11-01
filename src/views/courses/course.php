
    <?php $coverImage = $course->catalog->cover_image_url; ?>

    <style>
        #course-cover-image{
            height: 320px;
            background: white url('<?php print esc_url($coverImage) ?>') center center;
            background-size: cover;
        }

        #course-header .thumbnail{ max-height: 120px; }

        @media(max-width: 767px){
            #course-header .thumbnail{ max-height: none; }
        }
    </style>

    <?php if($coverImage): ?>
        <div id="course-cover-image" class="mb-4 rounded shadow"></div>
    <?php endif; ?>

    <header id="course-header" class="my-4 d-flex flex-column flex-md-row align-items-center">
        <div class="me-md-4 mb-4 mb-md-0">
            <img src="<?php print esc_url($course->catalog->thumbnail_url) ?>" class="thumbnail rounded shadow"
                 alt="<?php print esc_attr($course->name) ?>"/>
        </div>

        <div class="flex-grow-1">
            <h3 class="mb-2"><?php print esc_html($course->name) ?></h3>

            <p class="small mb-1"><b><?php print esc_html($course->code) ?></b></p>

            <?php if(! empty($course->description)): ?>
                <p class="text-muted small mb-2"><?php print esc_html($course->description) ?></p>
            <?php endif; ?>

            <?php if($course->catalog->featured): ?>
                <div class="badge bg-primary extra-small">
                    <i class="bi bi-star"></i> <?php print tamkeen_trans('global.course_is_featured') ?>
                </div>
            <?php endif; ?>

            <ul class="list-inline">
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
        </div>

        <div>
            <?php if(isset($_SESSION['tamkeen-cart']) && is_array($_SESSION['tamkeen-cart']) && array_search($course->id, $_SESSION['tamkeen-cart']) !== false): ?>
                <a href="<?php print esc_url(tamkeen_url('?view=cart-remove&courseId=' . $course->id)) ?>"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle-fill"></i> <?php print tamkeen_trans('global.button_cancel_course_request') ?>
                </a>

            <?php elseif($course->catalog->accepts_requests == true): ?>
                <a href="<?php print esc_url(tamkeen_url('?view=cart-add&courseId=' . $course->id)) ?>" class="btn btn-primary">
                    <i class="bi bi-cart3"></i> <?php print tamkeen_trans('global.button_request_course') ?>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <?php if(! empty($course->catalog->about)): ?>
        <div class="card my-4">
            <div class="card-body">
                <?php print $course->catalog->about; ?>
            </div>
        </div>
    <?php endif; ?>
