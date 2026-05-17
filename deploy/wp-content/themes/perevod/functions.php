<?php
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
remove_action('wp_head', 'wp_generator');

add_theme_support('post-thumbnails');


if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.3');
}

function wpdocs_dequeue_dashicon()
{
    if (current_user_can('update_core')) {
        return;
    }
    wp_deregister_style('dashicons');
}
add_action('wp_enqueue_scripts', 'wpdocs_dequeue_dashicon');

// Fully Disable Gutenberg editor.
add_filter('use_block_editor_for_post_type', '__return_false', 10);
// Don't load Gutenberg-related stylesheets.
add_action('wp_enqueue_scripts', 'remove_block_css', 100);
function remove_block_css()
{
    wp_dequeue_style('wp-block-library'); // Wordpress core
    wp_dequeue_style('wp-block-library-theme'); // Wordpress core
    wp_dequeue_style('wc-block-style'); // WooCommerce
    wp_dequeue_style('storefront-gutenberg-blocks'); // Storefront theme
}

function _theme_enqueue_styles()
{
    //important styles
    // wp_enqueue_style('style', get_stylesheet_directory_uri() . '/style.css', array(), _S_VERSION);

    wp_enqueue_style('style', get_stylesheet_directory_uri() . '/style.css', array(), _S_VERSION);

    //scripts

    wp_enqueue_script('magnific-popup', get_stylesheet_directory_uri() . '/js/jquery.magnific-popup.min.js', array('jquery'), _S_VERSION, true);

    wp_enqueue_script('carousel', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), _S_VERSION, true);

    wp_enqueue_script('frontend', get_stylesheet_directory_uri() . '/js/frontend.min.js', array('jquery'), _S_VERSION, true);

    wp_enqueue_script('autoptimize_single', get_stylesheet_directory_uri() . '/js/autoptimize_single_b6c301a3ab86429da902b6675b957f8c.js', array('jquery'), _S_VERSION, true);

    wp_enqueue_script('slick', get_stylesheet_directory_uri() . '/js/slick.js', array('jquery'), _S_VERSION, true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    wp_enqueue_script('theme', get_stylesheet_directory_uri() . '/js/theme.min.js', array('jquery'), _S_VERSION, true);
}
add_action('wp_enqueue_scripts', '_theme_enqueue_styles', 22);


//my

function catch_that_image()
{
    global $post, $posts;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches[1][0];

    // no image found display default image instead
    if (empty($first_img)) {
        $first_img = "/wp-content/themes/..../images/logo.png";
    }
    return $first_img;
}

function shorten_text($iChars = 22, $szTail = "...", $bPrint = true)
{
    global $post;
    $szText = strip_tags(trim($post->post_content));
    $szText = substr($szText, 0, $iChars);
    $szText = substr($szText, 0, strrpos($szText, ' ')) . $szTail;
    apply_filters('the_excerpt', $szText);
    if ($bPrint == true) echo $szText;
    else return $szText;
}

function new_excerpt_more($more)
{
    global $post;
    return '... <a href="' . get_permalink($post->ID) . '">' . ' подробнее' . '</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');


//end my

if (function_exists('register_sidebar'))
    register_sidebar(array(
        'name' => 'MenuTop',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h3 class="title">',
        'after_title' => '</h3>',
    ));
if (function_exists('register_sidebar'))
    register_sidebar(array(
        'name' => 'Foot1',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h3 class="title">',
        'after_title' => '</h3>',
    ));
if (function_exists('register_sidebar'))
    register_sidebar(array(
        'name' => 'Perevod',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h3 class="title">',
        'after_title' => '</h3>',
    ));
if (function_exists('register_sidebar'))
    register_sidebar(array(
        'name' => 'Languages',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h3 class="title">',
        'after_title' => '</h3>',
    ));
if (function_exists('register_sidebar'))
    register_sidebar(array(
        'name' => 'Menu4',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h3 class="title">',
        'after_title' => '</h3>',
    ));
if (function_exists('register_sidebar'))
    register_sidebar(array(
        'name' => 'Blog',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<div class="title">',
        'after_title' => '</div>',
    ));
if (function_exists('register_sidebar'))
    register_sidebar(array(
        'name' => 'Blog1',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<div class="title">',
        'after_title' => '</div>',
    ));
if (function_exists('register_sidebar'))
    register_sidebar(array(
        'name' => 'Price',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h3 class="title">',
        'after_title' => '</h3>',
    ));

function arh_the_breadcrumb()
{
    $serch_mass = array();
    $pags = get_pages();
    foreach ($pags as $pg) {
        $serch_mass[] = array("page" => $pg->post_name, "title" => $pg->post_title);
    }
    $customBreadcrumb = '
    <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
    <a itemprop="item" href="https://moscowtrans.ru">
    <span itemprop="name">Главная</span><meta itemprop="position" content="1"></a>
    </li>&nbsp;»&nbsp;
    <a itemprop="item" href="https://moscowtrans.ru/perevod-pasporta/"><li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
    <meta itemprop="name" content="Перевод паспорта"><meta itemprop="position" content="2">Перевод паспорта
    </a></li>&nbsp;»&nbsp;
    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem"><meta itemprop="name" content="Перевод иностранного паспорта"><meta itemprop="position" content="3">Перевод иностранного паспорта</li>
    </div>
    ';

    if ($_SERVER['REQUEST_URI'] == "/perevod-pasporta/perevod-inostrannogo-pasporta/") {
        echo $customBreadcrumb;
        return;
    }

    if (!is_front_page()) {
        echo '<li itemprop="itemListElement" itemscope
        itemtype="http://schema.org/ListItem"><a itemprop="item" href="';
        echo get_option('home');
        echo '"><span itemprop="name">Главная</span><meta itemprop="position" content="1" />';
        echo "</a></li>&nbsp;»&nbsp;";
        $position = 2;
        if (is_category() || is_single()) {
            $categories = get_the_category();
            if (!empty($categories)) {
                $this_category =  esc_html($categories[0]->name);
                $this_cat_id = esc_html($categories[0]->cat_ID);
                $this_link = get_category_link($this_cat_id);
            }
            echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            //echo '<a href="'.$this_link.'" itemprop="item"><meta itemprop="name" content="'.$this_category.'">';
            echo '<a href="' . $this_link . '" itemprop="item">' . $this_category;
            //echo '<meta itemprop="position" content="'.$position.'">';

            $position++;
            the_category(' ');
            echo '</a>';
            echo '</li>';
            if (is_single()) {
                $this_title =  esc_html(get_the_title());
                echo "&nbsp;»&nbsp;";
                the_title('<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><meta itemprop="name" content="' . $this_title . '"><meta itemprop="position" content="' . $position . '">', '</li>');
            }
        } elseif (is_page()) {
            $this_title =  esc_html(get_the_title());
            echo the_title('<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><meta itemprop="name" content="' . $this_title . '"><meta itemprop="position" content="' . $position . '">', '</li>');
        }
    } else {
        echo 'Home';
    }
};
function get_rez_frmserchmass($target, $mass)
{
    $viv = '';
    if ($target != '') {
        foreach ($mass as $e) {
            if ($e['page'] == $target) {
                $viv = $e;
            }
        }
    }
    if ($viv == '') {
        return false;
    } else {
        return $viv;
    }
}
add_filter( 'comment_form_default_fields', 'truemisha_change_labels', 25 );
 
function truemisha_change_labels( $fields ){
//echo '<pre>';
//print_r($fields);
//echo '</pre>';
$fields['cookies'] = str_replace('Сохранить моё имя, email и адрес сайта в этом браузере для последующих моих комментариев.','Сохранить мое имя',$fields['cookies']);
return $fields;
}
function the_breadcrumb()
{
    $serch_mass = array();
    $pags = get_pages();
    foreach ($pags as $pg) {
        $serch_mass[] = array("tip" => "page", "id" => "", "page" => $pg->post_name, "title" => $pg->post_title);
    }
    unset($pags);
    $posts = get_posts(array(
        'post_type' => 'post', // тип постов - записи
        'numberposts' => 0, // получить 5 постов, можно также использовать posts_per_page
        'orderby' => 'date', // сортировать по дате
        'order' => 'DESC', // по убыванию (сначала - свежие посты)
        'suppress_filters' => true // 'posts_*' и 'comment_feed_*' фильтры игнорируются
    ));
    foreach ($posts as $post) {
        $serch_mass[] = array("tip" => "post", "id" => $post->ID, "page" => $post->post_name, "title" => $post->post_title);
    }
    unset($posts);
    //echo '<pre>';
    //print_r($serch_mass);
    //echo '</pre>';
    $customBreadcrumb = '
    <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
    <a itemprop="item" href="https://moscowtrans.ru">
    <span itemprop="name">Главная</span><meta itemprop="position" content="1"></a>
    </li>
    ';
    $pg_sructur = explode('/', $_SERVER['REQUEST_URI']);
    $dop_breadcrubs = '';
    foreach ($pg_sructur as $e) {
        if ($e != '') {
            $target = get_rez_frmserchmass($e, $serch_mass);
            if ($target != false) {
                if ($target['tip'] == "post") {
                    $category = get_the_category($post->ID);
                    /*$dop_breadcrubs .= '&nbsp;»&nbsp;
    <a itemprop="item" href="https://moscowtrans.ru/'.$category[0]->slug.'/"><li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
    <meta itemprop="name" content="'.$category[0]->name.'"><meta itemprop="position" content="2">'.$category[0]->name.'
    </a></li>';*/
                    $cat_viv = str_replace('<li   >', '', $category[0]->name);
					$cat_viv = str_replace('Города','Статьи и новости',$cat_viv);
					$cat_url = $category[0]->slug;
					//echo '<hr>'.$category[0]->slug.'<hr>';
					$cat_url = str_replace('goroda','stati-i-novosti',$cat_url);
                    $dop_breadcrubs .= '&nbsp;»&nbsp;
    <a itemprop="item" href="https://moscowtrans.ru/' . $cat_url . '/"><li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">' . $cat_viv . '</a></li>';
                }
                /*$dop_breadcrubs .= '&nbsp;»&nbsp;
    <a itemprop="item" href="https://moscowtrans.ru/'.$target['page'].'/"><li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
    <meta itemprop="name" content="'.$target['title'].'"><meta itemprop="position" content="2">'.$target['title'].'
    </a></li>';*/
                $cat_viv = str_replace('<li   >', '', $target['title']);
                $dop_breadcrubs .= '&nbsp;»&nbsp;
    <a itemprop="item" href="https://moscowtrans.ru/' . $target['page'] . '/"><li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">' . $target['title'] . '</a></li>';
            }
        }
    }
    echo $customBreadcrumb . $dop_breadcrubs . '    </div>';
};

$use_prg = new prg_pattern();

class prg_pattern
{
    public function __construct()
    {
        add_action('template_redirect', array($this, 'prg_get_and_redirect'));
        add_shortcode('prgpattern', array($this, 'prg_pattern_form'));
    }


    public function prg_pattern_form($atts)
    {
        $atts = shortcode_atts(
            array(
                'slug' => 'noFoo',
                'title' => 'noBob',
                'extern' => 'false'
            ),
            $atts,
            'prgpattern'
        );

        if ($atts['extern'] == 'true') {
            $redirect_slug = esc_url($atts['slug']);
        } else {
            $redirect_slug = esc_url(home_url() . '/' . strtolower($atts['slug']));
        }

        ob_start();
?>
        <form method="POST">
            <button class="noLink" type="submit" name="prgpattern" value="<?php echo $redirect_slug; ?>"><?php echo $atts['title']; ?></button>
        </form>
<?php
        return ob_get_clean();
    }


    public function prg_get_and_redirect()
    {
        if (isset($_POST['prgpattern'])) {
            $slug = esc_url($_POST['prgpattern']);
            wp_redirect($slug);
            exit();
        }
    }
}

/**
 * Let WordPress manage the document title.
 * By adding theme support, we declare that this theme does not use a hard-coded <title> tag in the document head, and expect WordPress to provide it for us.
 */
//add_theme_support( 'title-tag' );

?>