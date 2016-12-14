<?php

class Shapely_Video extends WP_Widget {
	function __construct() {
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'customize_preview_init', array( $this, 'enqueue' ) );


		$widget_ops = array(
			'classname'   => 'shapely_video_widget',
			'description' => esc_html__( "Shapely Video Section", 'shapely' )
		);
		parent::__construct( 'shapely_video_widget', esc_html__( '[Shapely] Video Section', 'shapely' ), $widget_ops );
	}

	public function enqueue() {
		wp_enqueue_style( 'epsilon-styles', plugins_url( 'epsilon-framework/assets/css/style.css', dirname( __FILE__ ) ) );
		wp_enqueue_script( 'epsilon-object', plugins_url( 'epsilon-framework/assets/js/epsilon.js', dirname( __FILE__ ) ), array( 'jquery' ) );
	}

	function widget( $args, $instance ) {

		$terminate = false;
		switch ( $instance['video_type'] ) {
			case 'youtube':
				if ( empty( $instance['youtube_id'] ) ) {
					$terminate = true;
				}
				wp_enqueue_script( 'ytbackground', plugins_url( 'assets/js/jquery.youtubebackground.js', dirname( dirname( __FILE__ ) ) ), array( 'jquery' ) );
				break;
			case 'upload':
				if ( empty( $instance['video_id'] ) ) {
					$terminate = true;
				}
				wp_enqueue_script( 'vide', plugins_url( 'assets/js/jquery.vide.min.js', dirname( dirname( __FILE__ ) ) ), array( 'jquery' ) );
				break;
		}

		if ( $terminate ) {
			return false;
		}

		if ( empty( $instance['video_height'] ) && $instance['video_type'] === 'upload' ) {
			$instance['video_height'] = 500;
		}

		if ( empty( $instance['image_src'] ) ) {
			$instance['image_src'] = plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . '/assets/img/placeholder.jpg';
		}


		?>

		<?php if ( $instance['video_type'] == 'upload' ): ?>
			<div class="video-widget col-xs-12"
				<?php echo ! empty( $instance['video_height'] ) ? 'style="height:' . esc_attr( $instance['video_height'] ) . 'px"' : ''; ?>
				 data-vide-bg="mp4:<?php echo ! empty( $instance['video_id'] ) ? esc_attr( $instance['video_id'] ) : ''; ?>, poster:<?php echo esc_url( $instance['image_src'] ); ?>"
				 data-vide-options="loop: false, muted: true, position: 0% 0% <?php echo ! empty( $instance['autoplay'] ) ? ',autoplay: true' : ',autoplay:false'; ?>">
			<span class="video-controls">
				<button class="play-button"><icon class="fa fa-play"></icon></button>
				<button class="pause-button"><icon class="fa fa-pause"></icon></button>
			</span>
			</div>
		<?php elseif ( $instance['video_type'] == 'youtube' ): ?>
			<div <?php echo ! empty( $instance['youtube_id'] ) ? 'data-video-id="' . esc_attr( $instance['youtube_id'] ) . '"' : '' ?>
				data-autoplay="<?php echo ! empty( $instance['autoplay'] ) ? '1' : '0'; ?>"
				class="video-widget youtube"
				<?php echo ! empty( $instance['video_height'] ) ? 'style="height:' . esc_attr( $instance['video_height'] ) . 'px"' : ''; ?>>
				<span class="video-controls">
					<button class="play-button"><icon class="fa fa-play"></icon></button>
					<button class="pause-button"><icon class="fa fa-pause"></icon></button>
				</span>
			</div>
		<?php endif;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return string;
	 */
	public function form( $instance ) {
		wp_enqueue_media();
		wp_enqueue_style( 'newsmag_media_upload_css', get_template_directory_uri() . '/inc/assets/css/upload-media.css' );
		wp_enqueue_script( 'newsmag_media_upload_js', get_template_directory_uri() . '/inc/assets/js/upload-media.js', array( 'jquery' ) );


		$defaults = array(
			'autoplay'     => '',
			'video_id'     => '',
			'video_type'   => '',
			'youtube_id'   => '',
			'video_height' => '',
			'image_src'    => '',
		);

		// Merge the user-selected arguments with the defaults.
		$instance = wp_parse_args( (array) $instance, $defaults );
		// Extract the array to allow easy use of variables.
		extract( $instance );
		// Loads the widget form.
		?>

		<p><label
				for="<?php echo esc_attr( $this->get_field_id( 'video_type' ) ); ?>"><?php esc_html_e( 'Video Type ', 'shapely' ) ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'video_type' ) ); ?>"
			        id="<?php echo esc_attr( $this->get_field_id( 'video_type' ) ); ?>" class="widefat">
				<option
					value="youtube" <?php selected( $instance['video_type'], 'youtube' ); ?>><?php _e( 'YouTube', 'shapely' ); ?></option>
				<option
					value="upload" <?php selected( $instance['video_type'], 'upload' ); ?>><?php _e( 'Upload', 'shapely' ); ?></option>
			</select>
		</p>

		<p><label
				for="<?php echo esc_attr( $this->get_field_id( 'youtube_id' ) ); ?>"><?php esc_html_e( 'YouTube ID ', 'shapely' ) ?></label>

			<input type="text" value="<?php echo esc_attr( $instance['youtube_id'] ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'youtube_id' ) ); ?>"
			       id="<?php echo esc_attr( $this->get_field_id( 'youtube_id' ) ); ?>"
			       class="widefat"/>
		</p>

		<p class="shapely-media-control"
		   data-delegate-container="<?php echo esc_attr( $this->get_field_id( 'video_id' ) ) ?>">
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'video_id' ) ); ?>"><?php _e( 'Video URL', 'shapely' );
				?>:</label>

			<span class="video-path"
			      style="display:block; width:100%"><?php echo esc_html( $instance['video_id'] ) ?></span>

			<input type="hidden"
			       name="<?php echo esc_attr( $this->get_field_name( 'video_id' ) ); ?>"
			       id="<?php echo esc_attr( $this->get_field_id( 'video_id' ) ); ?>"
			       value="<?php echo esc_url( $instance['video_id'] ); ?>"
			       class="image-id blazersix-media-control-target">

			<button type="button" class="button upload-button"><?php _e( 'Choose Video', 'shapely' ); ?></button>
			<button type="button" class="button remove-button"><?php _e( 'Remove Video', 'shapely' ); ?></button>
		</p>

		<p class="shapely-media-control"
		   data-delegate-container="<?php echo esc_attr( $this->get_field_id( 'image_src' ) ) ?>">
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'image_src' ) ); ?>"><?php _e( 'Image', 'shapely' );
				?>:</label>

			<img style="display:block; width:100%" src="<?php echo esc_url( $instance['image_src'] ); ?>"/>
			<input type="hidden"
			       name="<?php echo esc_attr( $this->get_field_name( 'image_src' ) ); ?>"
			       id="<?php echo esc_attr( $this->get_field_id( 'image_src' ) ); ?>"
			       value="<?php echo esc_url( $instance['image_src'] ); ?>"
			       class="image-id blazersix-media-control-target">

			<button type="button" class="button upload-button"><?php _e( 'Choose Image', 'shapely' ); ?></button>
			<button type="button" class="button remove-button"><?php _e( 'Remove Image', 'shapely' ); ?></button>
		</p>


		<div class="checkbox_switch">
				<span class="customize-control-title onoffswitch_label">
                    <?php _e( 'Autoplay', 'shapely' ); ?>
				</span>
			<div class="onoffswitch">
				<input type="checkbox" id="<?php echo esc_attr( $this->get_field_name( 'autoplay' ) ); ?>"
				       name="<?php echo esc_attr( $this->get_field_name( 'autoplay' ) ); ?>"
				       class="onoffswitch-checkbox"
				       value="on"
					<?php checked( $instance['autoplay'], 'on' ); ?>>
				<label class="onoffswitch-label"
				       for="<?php echo esc_attr( $this->get_field_name( 'autoplay' ) ); ?>"></label>
			</div>
		</div>

		<p><label
				for="<?php echo esc_attr( $this->get_field_id( 'video_height' ) ); ?>"><?php esc_html_e( 'Video Height (in pixels)', 'shapely' ) ?></label>

			<input type="text" value="<?php echo esc_attr( $instance['video_height'] ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'video_height' ) ); ?>"
			       id="<?php echo esc_attr( $this->get_field_id( 'video_height' ) ); ?>"
			       class="widefat"/>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = array();
		$instance['youtube_id']   = ( ! empty( $new_instance['youtube_id'] ) ) ? strip_tags( $new_instance['youtube_id'] ) : '';
		$instance['video_id']     = ( ! empty( $new_instance['video_id'] ) ) ? esc_url_raw( $new_instance['video_id'] ) : '';
		$instance['image_src']    = ( ! empty( $new_instance['image_src'] ) ) ? esc_url_raw( $new_instance['image_src'] ) : '';
		$instance['video_height'] = ( ! empty( $new_instance['video_height'] ) ) ? absint( $new_instance['video_height'] ) : '';
		$instance['autoplay']     = ( ! empty( $new_instance['autoplay'] ) ) ? strip_tags( $new_instance['autoplay'] ) : '';
		$instance['video_type']   = ( ! empty( $new_instance['video_type'] ) ) ? strip_tags( $new_instance['video_type'] ) : '';
	}
}