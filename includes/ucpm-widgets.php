<?php
// Register and load the widget
if (!function_exists('ucpm_load_widget')) {

	function ucpm_load_widget() {
		register_widget('UCPM_Recent_Properties');
		register_widget('UCPM_Recent_Properties_For_Sale');
		register_widget('UCPM_Recent_Properties_For_Lease');
		register_widget('UCPM_Search_Properties');
	}

}
add_action('widgets_init', 'ucpm_load_widget');

if (!class_exists('UCPM_Recent_Properties')) {

	class UCPM_Recent_Properties extends WP_Widget {

		public function __construct() {
			$widget_ops = array(
				'classname' => 'ucpm_recent_properties',
				'description' => esc_html__("Your site's most recent properties.", "ucpm"),
				'customize_selective_refresh' => true,
			);
			parent::__construct('ucpm-recent-properties', esc_html__('UCPM Recent properties', 'ucpm'), $widget_ops);
			$this->alt_option_name = 'ucpm_recent_properties';
		}

		public function widget($args, $instance) {
			if (!isset($args['widget_id'])) {
				$args['widget_id'] = $this->id;
			}

			$title = (!empty($instance['title']) ) ? $instance['title'] : esc_html__('Recent properties', 'ucpm');

			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			$number = (!empty($instance['number']) ) ? absint($instance['number']) : 5;
			if (!$number)
				$number = 5;

			echo $args['before_widget'];
			if ($title) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			echo do_shortcode('[ucpm_properties number="' . esc_attr( $number ) . '" compact="true"]');
			echo $args['after_widget'];
		}

		public function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = sanitize_text_field($new_instance['title']);
			$instance['number'] = (int) $new_instance['number'];
			return $instance;
		}

		public function form($instance) {
			$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
			$number = isset($instance['number']) ? absint($instance['number']) : 5;
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:', 'ucpm'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e('Number of properties to show:', 'ucpm'); ?></label>
				<input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $number ); ?>" size="3" />
			</p>

			<?php
		}

	}

}

if (!class_exists('UCPM_Recent_Properties_For_Sale')) {

    class UCPM_Recent_Properties_For_Sale extends WP_Widget {

        public function __construct() {
            $widget_ops = array(
                'classname' => 'ucpm_recent_properties_for_sale',
                'description' => esc_html__("Your site's properties for sale.", "ucpm"),
                'customize_selective_refresh' => true,
            );
            parent::__construct('ucpm-recent-properties-for-sale', esc_html__('UCPM Recent properties for sale', 'ucpm'), $widget_ops);
            $this->alt_option_name = 'ucpm_recent_properties_for_sale';
        }

        public function widget($args, $instance) {
            if (!isset($args['widget_id'])) {
                $args['widget_id'] = $this->id;
            }

            $title = (!empty($instance['title']) ) ? $instance['title'] : esc_html__('Recent properties for sale', 'ucpm');

            $title = apply_filters('widget_title', $title, $instance, $this->id_base);

            $number = (!empty($instance['number']) ) ? absint($instance['number']) : 5;
            if (!$number)
                $number = 5;

            echo $args['before_widget'];
            if ($title) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
            echo do_shortcode('[ucpm_properties number="' . esc_attr( $number ) . '" purpose="Sell" compact="true"]');
            echo $args['after_widget'];
        }

        public function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = sanitize_text_field($new_instance['title']);
            $instance['number'] = (int) $new_instance['number'];
            return $instance;
        }

        public function form($instance) {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
            $number = isset($instance['number']) ? absint($instance['number']) : 5;
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:', 'ucpm'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e('Number of properties to show:', 'ucpm'); ?></label>
                <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $number ); ?>" size="3" />
            </p>

            <?php
        }

    }

}

if (!class_exists('UCPM_Recent_Properties_For_Lease')) {

    class UCPM_Recent_Properties_For_Lease extends WP_Widget {

        public function __construct() {
            $widget_ops = array(
                'classname' => 'ucpm_recent_properties_for_lease',
                'description' => esc_html__("Your site's properties for lease.", "ucpm"),
                'customize_selective_refresh' => true,
            );
            parent::__construct('ucpm-recent-properties-for-lease', esc_html__('UCPM Recent properties for lease', 'ucpm'), $widget_ops);
            $this->alt_option_name = 'ucpm_recent_properties_for_lease';
        }

        public function widget($args, $instance) {
            if (!isset($args['widget_id'])) {
                $args['widget_id'] = $this->id;
            }

            $title = (!empty($instance['title']) ) ? $instance['title'] : esc_html__('Recent properties for lease', 'ucpm');

            $title = apply_filters('widget_title', $title, $instance, $this->id_base);

            $number = (!empty($instance['number']) ) ? absint($instance['number']) : 5;
            if (!$number)
                $number = 5;

            echo $args['before_widget'];
            if ($title) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
            echo do_shortcode('[ucpm_properties number="' . esc_attr( $number ) . '" purpose="Lease" compact="true"]');
            echo $args['after_widget'];
        }

        public function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = sanitize_text_field($new_instance['title']);
            $instance['number'] = (int) $new_instance['number'];
            return $instance;
        }

        public function form($instance) {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
            $number = isset($instance['number']) ? absint($instance['number']) : 5;
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:', 'ucpm'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e('Number of properties to show:', 'ucpm'); ?></label>
                <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $number ); ?>" size="3" />
            </p>

            <?php
        }

    }

}

if (!class_exists('UCPM_Search_properties')) {

	class UCPM_Search_properties extends WP_Widget {

		public function __construct() {
			$widget_ops = array(
				'classname' => 'ucpm_search_properties',
				'description' => esc_html__('Use this widget to display search properties form', 'ucpm'),
				'customize_selective_refresh' => true,
			);
			parent::__construct('ucpm-search-properties', esc_html__('UCPM Search properties', 'ucpm'), $widget_ops);
			$this->alt_option_name = 'ucpm_search_properties';
		}

		public function widget($args, $instance) {
			if (!isset($args['widget_id'])) {
				$args['widget_id'] = $this->id;
			}

			$title = (!empty($instance['title']) ) ? $instance['title'] : esc_html__('Search properties', 'ucpm');
			$placeholder_text = (!empty($instance['placeholder-text']) ) ? $instance['placeholder-text'] : esc_html__('Search by Keyword, City or State', 'ucpm');
			$submit_button_text = (!empty($instance['submit-button-text']) ) ? $instance['submit-button-text'] : esc_html__('Search', 'ucpm');
			$exclude_fields = (!empty($instance['exclude-fields']) ) ? $instance['exclude-fields'] : '';
			if ($exclude_fields)
				$exclude_fields = implode(', ', $exclude_fields);

			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			echo $args['before_widget'];
			if ($title) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			echo do_shortcode('[ucpm_search placeholder="' . $placeholder_text . '" submit_btn="' . $submit_button_text . '" exclude="' . $exclude_fields . '"]');
			echo $args['after_widget'];
		}

		public function update($new_instance, $old_instance) {
			$instance = $old_instance;

			$instance['title'] = sanitize_text_field($new_instance['title']);
			$instance['placeholder-text'] = sanitize_text_field($new_instance['placeholder-text']);
			$instance['submit-button-text'] = sanitize_text_field($new_instance['submit-button-text']);
			$instance['exclude-fields'] = $new_instance['exclude-fields'];
			return $instance;
		}

		public function form($instance) {
			$instance = wp_parse_args((array) $instance, array('title' => '', 'placeholder-text' => '', 'submit-button-text' => '', 'exclude-fields' => ''));
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:', 'ucpm'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('placeholder-text'); ?>"><?php esc_html_e('Placeholder Text:', 'ucpm'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('placeholder-text'); ?>" name="<?php echo $this->get_field_name('placeholder-text'); ?>" type="text" value="<?php echo esc_attr($instance['placeholder-text']); ?>" />
				<small><?php esc_html_e('Text to display as the placeholder text in the text input.', 'ucpm') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('submit-button-text'); ?>"><?php esc_html_e('Submit Button Text:', 'ucpm'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('submit-button-text'); ?>" name="<?php echo $this->get_field_name('submit-button-text'); ?>" type="text" value="<?php echo esc_attr($instance['submit-button-text']); ?>" />
				<small><?php esc_html_e('Text to display on the search button.', 'ucpm') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('exclude-fields'); ?>"><?php esc_html_e('Exclude Fields:', 'ucpm'); ?></label>
					<?php $search_fields = array('type'); ?>
				<select class="widefat" id="<?php echo $this->get_field_id('exclude-fields'); ?>" name="<?php echo $this->get_field_name('exclude-fields'); ?>[]" multiple="true">
					<option value=""><?php esc_html_e('Exclude Fields', 'ucpm'); ?></option>
					<?php
					foreach ($search_fields as $search_field) {
						$selected = is_array($instance['exclude-fields']) && in_array($search_field, $instance['exclude-fields']) ? ' selected="selected" ' : '';
						echo '<option value="' . esc_attr( $search_field ) . '" ' . $selected . '>' . ucwords(str_replace('_', ' ', $search_field)) . '</option>';
					}
					?>
				</select>
				<small><?php esc_html_e('Select list of fields that you don\'t want to include on the search box.', 'ucpm') ?></small>
			</p>
			<?php
		}

	}

}
