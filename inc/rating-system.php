<?php

function lprw_handle_rating() {
    $post_id = intval($_POST['post_id']);
    $rating = intval($_POST['rating']);

    if ($post_id && $rating && $rating >= 1 && $rating <= 5) {
        $current_rating = get_post_meta($post_id, 'lprw_post_rating', true);
        if ($current_rating === '') {
            $current_rating = 0;
            add_post_meta($post_id, 'lprw_post_rating', $current_rating, true);
        }

        $rating_count = get_post_meta($post_id, 'lprw_post_rating_count', true);
        if ($rating_count === '') {
            $rating_count = 0;
            add_post_meta($post_id, 'lprw_post_rating_count', $rating_count, true);
        }

        $new_rating = $current_rating + $rating;
        $new_rating_count = $rating_count + 1;

        update_post_meta($post_id, 'lprw_post_rating', $new_rating);
        update_post_meta($post_id, 'lprw_post_rating_count', $new_rating_count);

        wp_send_json_success(array(
            'new_rating' => $new_rating,
            'new_rating_count' => $new_rating_count,
            'average_rating' => $new_rating / $new_rating_count
        ));
    } else {
        wp_send_json_error('Invalid rating data.');
    }

    wp_die();
}

add_action('wp_ajax_lprw_rate', 'lprw_handle_rating');
add_action('wp_ajax_nopriv_lprw_rate', 'lprw_handle_rating');
