<?php

    try{
        // The page to view
        $page = isset($_GET['view']) ?sanitize_text_field($_GET['view']) :null;

        switch($page){
            case 'cart-add':
                if(!isset($_SESSION['tamkeen-cart'])){
                    $_SESSION['tamkeen-cart'] = [];
                }

                // The course id
                $courseId = sanitize_text_field($_GET['courseId']);

                if(isset($_GET['courseId']) && is_numeric($courseId)){
                    // Add the course id
                    $_SESSION['tamkeen-cart'][] = $courseId;

                    // Remove duplicates
                    $_SESSION['tamkeen-cart'] = array_unique($_SESSION['tamkeen-cart']);
                }

                return tamkeen_render_view('cart/add', [
                    'requestUrl' => tamkeen_url('?view=cart-request')
                ]);
                break;

            case 'cart-remove':
                // The course id
                $courseId = sanitize_text_field($_GET['courseId']);

                // Remove the course from the session
                $courseIndex = array_search($courseId, $_SESSION['tamkeen-cart']);

                // If the course is on the session array
                if($courseIndex !== false && isset($_SESSION['tamkeen-cart'][$courseIndex])){
                    // Remove it
                    unset($_SESSION['tamkeen-cart'][$courseIndex]);
                }

                tamkeen_redirect('back');
                break;

            case 'cart-empty':
                // Clear the cart's content
                $_SESSION['tamkeen-cart'] = [];

                tamkeen_redirect(tamkeen_url());
                break;

            case 'cart-request':
                $itemIds = $_SESSION['tamkeen-cart'];

                if(!count($itemIds)){
                    throw new Exception('No courses were selected!');
                }

                // Get the cart id
                $response = tamkeen_api_request('post', 'plugins/wordPress/cart/save', [
                    'courseIds' => $itemIds
                ]);

                if(!isset($response->cartId)){
                    throw new Exception('Failed to proceed with the request process; unable to save your cart.');
                }

                // Create the request submission url
                $requestUrl = TAMKEEN_BASE_URL . '/service/' . get_option('tamkeen_tenant_id') .
                    '/redirects/plugins/wordPress/request?cartId=' . $response->cartId;

                tamkeen_redirect($requestUrl);
                break;

            case 'category':
                return include tamkeen_get_path('pages/courses/category.php');
                break;

            case 'course':
                return include tamkeen_get_path('pages/courses/course.php');
                break;

            case 'signup':
                return include tamkeen_get_path('pages/courses/request.php');
                break;

            default:
                return include tamkeen_get_path('pages/home.php');
                break;
        }

    }catch (Exception $e){
        return $e->getMessage();
    }
