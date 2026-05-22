<?php
// Silence is golden. Required WordPress fallback template.
get_header();
if (have_posts()) {
    while (have_posts()) {
        the_post();
        the_content();
    }
}
get_footer();
