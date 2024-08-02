<?php

class LPRW_Latest_Posts_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'lprw_latest_posts_widget',
            __('Latest Posts with Ratings', 'lprw'),
            array('description' => __('A widget to display latest posts with ratings.', 'lprw'))
        );
    }

    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $num_posts = $instance['num_posts'];
        $show_ratings = $instance['show_ratings'];
        $rating_position = $instance['rating_position'];

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $latest_posts = new WP_Query(array(
            'posts_per_page' => $num_posts,
            'post_status' => 'publish'
        ));

        if ($latest_posts->have_posts()) {
            $current_month = '';
            while ($latest_posts->have_posts()) {
                $latest_posts->the_post();
                $post_month = get_the_date('F Y');
                if ($post_month != $current_month) {
                    if ($current_month != '') {
                        echo '</ul>';
                    }
                    $current_month = $post_month;
                    echo '<h3>' . $current_month . '</h3>';
                    echo '<ul class="lprw-latest-posts">';
                }

                $rating = get_post_meta(get_the_ID(), 'lprw_post_rating', true);
                $rating = $rating ? $rating : 0;
                $rating_count = get_post_meta(get_the_ID(), 'lprw_post_rating_count', true);
                $average_rating = $rating_count ? $rating / $rating_count : 0;
                
                ?>
        
                <li>
                    <div class="lprw-post-thumbnail">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
                        <?php else : ?>
                            <a href="<?php the_permalink(); ?>"><img src="<?php echo plugin_dir_url(__FILE__) . '../assets/images/default-thumbnail.png'; ?>" alt="Default Thumbnail"></a>
                        <?php endif; ?>
                    </div>
                    <div class="lprw-post-info" data-post_id="<?php echo get_the_ID(); ?>">
                        <a href="<?php the_permalink(); ?>" class="lprw-post-title"><?php the_title(); ?></a>
                        <span class="lprw-post-date"><?php echo get_the_date(); ?></span>
                        <?php if ($show_ratings === 'on') : ?>
                            <div class="lprw-rating-panel <?php echo $rating_position; ?>">
                                <span class="lprw-post-rating">
                                <?php echo $this->display_star_rating($average_rating); ?>
                                <span class="lprw-rating-average"><?php echo number_format($average_rating, 1); ?></span>
                            </span>
                            <button class="lprw-rate-button" data-post_id="<?php echo get_the_ID(); ?>">Rate this post</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </li>
                <?php
            }
            echo '</ul>';
        }

        wp_reset_postdata();
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Latest Posts', 'lprw');
        $num_posts = !empty($instance['num_posts']) ? $instance['num_posts'] : 5;
        $show_ratings = !empty($instance['show_ratings']) ? $instance['show_ratings'] : 'on';
        $rating_position = !empty($instance['rating_position']) ? $instance['rating_position'] : 'bottom-left';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'lprw'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('num_posts')); ?>"><?php _e('Number of posts:', 'lprw'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('num_posts')); ?>" name="<?php echo esc_attr($this->get_field_name('num_posts')); ?>" type="number" value="<?php echo esc_attr($num_posts); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_ratings, 'on'); ?> id="<?php echo esc_attr($this->get_field_id('show_ratings')); ?>" name="<?php echo esc_attr($this->get_field_name('show_ratings')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_ratings')); ?>"><?php _e('Show ratings', 'lprw'); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('rating_position')); ?>"><?php _e('Rating panel position:', 'lprw'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('rating_position')); ?>" name="<?php echo esc_attr($this->get_field_name('rating_position')); ?>" class="widefat">
                <option value="bottom-left" <?php selected($rating_position, 'bottom-left'); ?>><?php _e('Bottom-Left', 'lprw'); ?></option>
                <option value="bottom-center" <?php selected($rating_position, 'bottom-center'); ?>><?php _e('Bottom-Center', 'lprw'); ?></option>
                <option value="bottom-right" <?php selected($rating_position, 'bottom-right'); ?>><?php _e('Bottom-Right', 'lprw'); ?></option>
                <option value="bottom-between" <?php selected($rating_position, 'bottom-between'); ?>><?php _e('Bottom-Between', 'lprw'); ?></option>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['num_posts'] = (!empty($new_instance['num_posts'])) ? intval($new_instance['num_posts']) : 5;
        $instance['show_ratings'] = (!empty($new_instance['show_ratings'])) ? strip_tags($new_instance['show_ratings']) : 'off';
        $instance['rating_position'] = (!empty($new_instance['rating_position'])) ? strip_tags($new_instance['rating_position']) : 'bottom-left';
        return $instance;
    }

    private function display_star_rating($rating) {
        $output = '<div class="lprw-star-rating">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $output .= '<span class="star filled">★</span>';
            } else {
                $output .= '<span class="star">☆</span>';
            }
        }
        $output .= '</div>';
        return $output;
    }
}
