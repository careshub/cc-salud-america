<?php
/**
 * The file that defines the custom post type and taxonomy we'll need for this plugin.
 *
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    CC Salud America
 * @subpackage CC Salud America/includes
 */

/**
 * Define the custom post type and taxonomy we'll need for this plugin.
 *
 *
 * @since      1.0.0
 * @package    CC Salud America
 * @subpackage CC Salud America/includes
 * @author     Your Name <email@example.com>
 */
class CC_SA_Term_Intros_CPT_Tax extends CC_Salud_America {

	private $nonce_value = 'sa_term_intro_meta_box_nonce';
	private $nonce_name = 'sa_term_intro_meta_box';
	public $post_type = 'sa_term_introduction';
	public $meta_file_upload_sections = array(
		'research_review' => array(
			'label' => 'Research Review',
			'fields' => array(
				'research_review_icon' => array(
					'label' => 'Icon',
					'type'	=> 'image'
					),
				'research_review' => array(
					'label' => 'Research Review',
					'type'	=> 'pdf'
					),
				),
		 ),
		'issue_brief' => array(
			'label' => 'Issue Briefs',
			'fields' => array(
				'issue_brief_icon'	=> array(
					'label' => 'Icon',
					'type'	=> 'image'
					),
				'issue_brief_english' => array(
					'label' => 'English Version',
					'type'	=> 'pdf'
					),
				'issue_brief_spanish' => array(
					'label' => 'Spanish Version',
					'type'	=> 'pdf'
					),
			),
		),
		'infographics' => array(
			'label' => 'Infographics',
			'fields' => array(
				'infographics_icon' => array(
					'label' => 'Icon',
					'type'	=> 'image'
					),
				'infographic_english' => array(
					'label' => 'English Version',
					'type'	=> 'image'
					),
				'infographic_spanish' => array(
					'label' => 'Spanish Version',
					'type'	=> 'image'
					),
			 ),
		),
		'fallback_image' => array(
			'label' => 'Term Fallback Image',
			'fields' => array(
				'fallback_image' => array(
					'label' => 'Used when no post thumbnail exists.',
					'type'	=> 'image'
					),
			 ),
		),
	);

	/**
	 * Initialize the extension class
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		// Register custom post type
		add_action( 'init', array( $this, 'register_cpt_sa_term_introduction' ), 28 );

		// Add meta box to handle video URLs
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		// Handle saving policies
		add_action( 'save_post', array( $this, 'save' ) );

	}

	/**
	 * Define the "advocacy target introduction" custom post type
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	public function register_cpt_sa_term_introduction() {

	    $labels = array(
	        'name' => __( 'Term Introductions', $this->plugin_slug ),
	        'singular_name' => __( 'Term Introduction', $this->plugin_slug ),
	        'add_new' => __( 'Add New', $this->plugin_slug ),
	        'add_new_item' => __( 'Add New Introduction', $this->plugin_slug ),
	        'edit_item' => __( 'Edit Introduction', $this->plugin_slug ),
	        'new_item' => __( 'New Introduction', $this->plugin_slug ),
	        'view_item' => __( 'View Introduction', $this->plugin_slug ),
	        'search_items' => __( 'Search Introductions', $this->plugin_slug ),
	        'not_found' => __( 'No introductions found', $this->plugin_slug ),
	        'not_found_in_trash' => __( 'No introductions found in Trash', $this->plugin_slug ),
	        'parent_item_colon' => __( 'Parent Introduction:', $this->plugin_slug ),
	        'menu_name' => __( 'Term Introductions', $this->plugin_slug ),
	    );

	    $args = array(
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Introductory content used at the top of Salud America advocacy target archive pages',
	        'supports' => array( 'title', 'editor', 'thumbnail' ),
			'taxonomies' => array( 'sa_advocacy_targets' ),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => 'salud_america',
	        'show_in_nav_menus' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => true,
	        'has_archive' => false,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => true,
	        'capability_type' => $this->post_type,
	        'map_meta_cap' => true
	    );

	    register_post_type( $this->post_type, $args );

	}

	/**
	 * Add a meta box for some specifics related to the introduction.
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	//Building the input form in the WordPress admin area
	public function add_meta_box() {
		add_meta_box( 'sa_advo_target_intros_meta_box', 'Advocacy Target Introduction Details', array( $this, 'sa_advo_target_intros_meta_box' ), $this->post_type, 'normal', 'high' );   ;
	}
	public function sa_advo_target_intros_meta_box( $post ) {
		$custom = get_post_meta( $post->ID );

		// Add a nonce field so we can check for it later.
		wp_nonce_field( $this->nonce_name, $this->nonce_value );
		?>
		<div>
			<h4>Term Header Banner Image</h4>
			<p class="info">Set the header banner image using the "Featured Image" setting.</em></p>
		</div>
		<div>
			<h4>Video URLs</h4>
			<p class="info">Note: Video URLs should take the form <em>http://www.youtube.com/watch?v=MSddu5zYOZI</em></p>
			<p>
				<label for='sa_term_intro_video_spanish_url'>Spanish Video URL</label>
				<input type='text' name='sa_term_intro_video_spanish_url' value='<?php
					if ( ! empty( $custom[ 'sa_term_intro_video_spanish_url' ][0] ) ) {
						echo $custom[ 'sa_term_intro_video_spanish_url' ][0];
					}
					?>' size="90"/>
			</p>
			<p>
				<label for='sa_term_intro_video_english_url'>English Video URL</label>
				<input type='text' name='sa_term_intro_video_english_url' value='<?php
					if ( ! empty( $custom[ 'sa_term_intro_video_english_url' ][0] ) ) {
						echo $custom[ 'sa_term_intro_video_english_url' ][0];
					}
					?>' size="90"/>
			</p>
		</div>
		<div>
			<h4>Attached PDFs</h4>
			<table id="intro-attached-pdfs" class="form-table" >
			<?php //Build a series of inputs for the various
			foreach ( $this->meta_file_upload_sections as $key => $section ) {
				?>
				<tr>
					<th scope="row" ><?php echo $section['label']; ?></th>
					<td>
					<?php foreach ( $section['fields'] as $field_id => $field ) {
						$filetype_label = ( $field['type'] == 'pdf' ) ? 'PDF' : "Image";
						?>
						<label for='sa_term_intro_<?php echo $field_id; ?>'><strong><?php echo $field['label']; ?></strong></label><br />
						<span id="sa_term_intro_<?php echo $field_id; ?>_filename"><?php
						if ( ! empty( $custom[ 'sa_term_intro_' . $field_id ][0] ) ) {
							echo basename( get_attached_file( $custom[ 'sa_term_intro_' . $field_id ][0] ) );
						} else {
							echo "<em>No file selected.</em>";
						}
						?></span>
						<input id='sa_term_intro_<?php echo $field_id; ?>' type='hidden' name='sa_term_intro_<?php echo $field_id; ?>' value='<?php
							if ( ! empty( $custom[ 'sa_term_intro_' . $field_id ][0] ) ) {
								echo $custom[ 'sa_term_intro_' . $field_id ][0];
							}
							?>'/>
						<button id="sa_term_intro_<?php echo $field_id; ?>_button" class="sa_term_intro_file_upload_button" data-section="<?php echo $field_id; ?>" data-filetype="<?php echo $field['type'] ?>">Select <?php echo $filetype_label; ?></button>
						<?php
						if ( empty( $custom[ 'sa_term_intro_' . $field_id ][0] ) ) {
							$show_style = 'style="display:none"';
						} else {
							$show_style = '';
						}
						?>
						<button id="sa_term_intro_<?php echo $field_id; ?>_button_remove" class="sa_term_intro_file_remove_button" data-section="<?php echo $field_id; ?>" <?php echo $show_style; ?>>Remove <?php echo $filetype_label; ?></button><br />
						<?php
					}
					?>
					<td>
				</tr>
				<?php
			}
			?>
			</table>
			<?php
			?>
		</div>
		<script type="text/javascript">
		/*
		 * Attaches the image uploader to the input fields
		 */
		jQuery(document).ready(function($){
			var meta_image_frame_img,
				meta_image_frame_pdf,
				section,
				filetype,
				title_text,
				button_text,
				library_type;

		    // Runs when the image button is clicked.
		    $('.sa_term_intro_file_upload_button').on( 'click', function(e){
		    	section = $(this).data( "section" );
		    	filetype = $(this).data( "filetype" );

		        // Prevents the default action from occuring.
		        e.preventDefault();

		        // If the pdf-specific frame already exists, re-open it.
		        if ( filetype == 'pdf' && meta_image_frame_pdf ) {
		            meta_image_frame_pdf.open();
		            return;
		        }
		        // If the image-specific frame already exists, re-open it.
		        if ( filetype == 'image' && meta_image_frame_img ) {
		            meta_image_frame_img.open();
		            return;
		        }

		        // Sets up the media library frame if needed
		        // We need two versions: one restricted to images, the other to PDFs
		        if ( filetype == 'pdf' ) {
		        	frame_name = meta_image_frame_pdf;
		        	title_text = 'Choose or Upload a PDF';
		        	button_text = 'Use this PDF';
		        	library_type = 'application/pdf';
		        } else if ( filetype == 'image' ) {
		        	frame_name = meta_image_frame_img;
		        	title_text = 'Choose or Upload an Image';
		        	button_text = 'Use this image';
		        	library_type = 'image';
		        }
		        frame_name = new wp.media.view.MediaFrame.Select({
		            title: title_text,
		            button: { text:  button_text },
		            library: { type: library_type },
		            multiple: false
		        });

		        // Runs when an image is selected.
		        frame_name.on('select', function(){
		            // Grabs the attachment selection and creates a JSON representation of the model.
		            var media_attachment = frame_name.state().get('selection').first().toJSON();
		            // Sends the attachment URL to our custom image input field.
		            $('#sa_term_intro_'+section+'_filename').empty().html(media_attachment.filename);
		            $('#sa_term_intro_'+section).val(media_attachment.id);
		            $('#sa_term_intro_'+section+'_button_remove').show();
		        });

		        // Opens the media library frame.
		        frame_name.open();
		    });
		    // Runs when the remove file button is clicked.
		    $('.sa_term_intro_file_remove_button').on( 'click', function(e){
		    	section = $(this).data( "section" );

		        // Prevents the default action from occuring.
		        e.preventDefault();

		        $('#sa_term_intro_'+section+'_filename').empty().html("<em>No file selected.</em>")
		        $('#sa_term_intro_'+section).val('');
		        $('#sa_term_intro_'+section+'_button_remove').hide();
		    });
		});
		</script>
	<?php
	}

	/**
	 * Save extra meta.
	 *
	 * @since    1.0.0
	 *
	 * @return   void
	 */
	public function save( $post_id ) {

 		if ( get_post_type( $post_id ) != $this->post_type ) {
			return;
		}

		if ( ! $this->user_can_save( $post_id, $this->nonce_value, $this->nonce_name  ) ) {
			return false;
		}
		// Create array of fields to save
		$meta_fields_to_save = array( 'sa_term_intro_video_english_url', 'sa_term_intro_video_spanish_url' );
		$file_upload_fields = $this->build_file_upload_inputs_array();
		$meta_fields_to_save = array_merge( $meta_fields_to_save, $file_upload_fields );
		// Save meta
		$meta_success = $this->save_meta_fields( $post_id, $meta_fields_to_save );

	}

	/**
	 * Parse the file upload inputs into an array.
	 *
	 * @since    1.0.0
	 *
	 * @return   one-dimensional array of file-upload inputs
	 */
	public function build_file_upload_inputs_array(){
		$inputs = array();
		foreach ( $this->meta_file_upload_sections as $key => $section ) {
			foreach ( $section['fields'] as $field_id => $field ) {
				$inputs[] = 'sa_term_intro_' . $field_id;
			}
		}
		return $inputs;
	}

} //End class
$sa_term_intros_cpt_tax = new CC_SA_Term_Intros_CPT_Tax();


// Functions in the global scope

/**
 * Return the html for a fallback image, given the terms of a post.
 *
 * @param string $size ONe of the WP-defined sizes.
 *
 */
function sa_get_advo_target_fallback_image( $term, $size = 'feature-front-sub', $class = 'alignleft' ) {
	if ( empty( $term ) ) {
		// We'll grab one at random.
		$args = array(
		    'post_type' => 'sa_term_introduction',
		    'fields' => 'ids',
		    'order_by' => 'rand',
		    );
	} else {
		// Find the advo target intro for the requested term.
		$args = array(
		    'post_type' => 'sa_term_introduction',
		    'posts_per_page' => 1,
		    'fields' => 'ids',
		    'tax_query' => array(
		        array(
		            'taxonomy' => $term->taxonomy,
		            'field'    => 'term_id',
		            'terms'    => $term->term_id,
		        ),
		    ),
		);
	}

	$term_intros = new WP_Query( $args );
	if ( ! empty( $term_intros ) ) {
		$intro_id = current( $term_intros->posts );
	}

	$fallback_image_post_id = get_post_meta( $intro_id, 'sa_term_intro_fallback_image', true );

	// Get the html:
	if ( ! empty( $fallback_image_post_id ) ) {
		$args = array( 'class' => "attachment-{$size} {$class} wp-post-image" );

        // If there's no video, we use the fullsize fallback image for the term.
        $retval = wp_get_attachment_image( $fallback_image_post_id, $size, false, $args );
    }

    return $retval;
}