<?php
$custom_fields = get_post_meta( get_the_ID() );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(  ); ?>>
    <div class="entry-content">
        <header class="entry-header clear">
            <h3 class="screamer sapink"><?php the_title(); ?></h3>
        </header>
        <?php
        $size = 'feature-front';
        the_post_thumbnail( $size, array('class' => "attachment-$size alignright" ) );
        the_content();
        if ( ! empty( $custom_fields['sa_take_action_url'][0] ) ) {
            ?>
            <a href="<?php echo $custom_fields['sa_take_action_url'][0]; ?>" class="button">Take Action Now!</a>
            <?php
        }
        ?>
    </div><!-- .entry-content -->
    <?php edit_post_link('Edit This Post', '<footer class="entry-meta"><span class="edit-link">', '</span></footer>', get_the_ID() ); ?>
</article><!-- #post -->