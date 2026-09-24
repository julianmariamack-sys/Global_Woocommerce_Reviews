<?php
/**
 * Plugin Name: Global WooCommerce Reviews for Elementor
 * Description: Adds an Elementor widget that displays approved WooCommerce reviews from all products, merging variation reviews under their parent product.
 * Version: 1.6.5
 * Author: JM Custom Web Dev & IT
 * Text Domain: global-wc-reviews-elementor
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce, elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'GWCRE_VERSION', '1.6.5' );
define( 'GWCRE_FILE', __FILE__ );
define( 'GWCRE_DIR', plugin_dir_path( __FILE__ ) );
define( 'GWCRE_URL', plugin_dir_url( __FILE__ ) );

final class GWCRE_Plugin {

    public function __construct() {
        add_action( 'plugins_loaded', [ $this, 'init' ] );
    }

    public function init() {
        // WooCommerce is required for the review data and product APIs.
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        add_action( 'init', [ $this, 'register_assets' ] );
        add_action( 'elementor/init', [ $this, 'init_elementor' ] );
        add_action( 'wp_ajax_gwcre_get_page', [ $this, 'ajax_get_page' ] );
        add_action( 'wp_ajax_nopriv_gwcre_get_page', [ $this, 'ajax_get_page' ] );
        add_action( 'wp_ajax_gwcre_submit_review', [ $this, 'ajax_submit_review' ] );
        add_action( 'wp_ajax_nopriv_gwcre_submit_review', [ $this, 'ajax_submit_review' ] );

        // Add the review subject to the WooCommerce Products > Reviews list
        // and make it editable from the individual comment edit screen.
        // WooCommerce uses its own ReviewsListTable for this screen, so use
        // the dedicated WooCommerce review-table hooks rather than the legacy
        // WordPress comments-list hooks.
        add_filter( 'woocommerce_product_reviews_table_columns', [ $this, 'add_subject_review_table_column' ], 20 );
        add_filter( 'woocommerce_product_reviews_table_column_gwcre_subject_content', [ $this, 'render_subject_review_table_column' ], 10, 2 );
        add_action( 'add_meta_boxes_comment', [ $this, 'add_subject_comment_meta_box' ] );
        add_action( 'edit_comment', [ $this, 'save_subject_comment_meta' ] );
    }

    public function init_elementor() {
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }

        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
    }

    /**
     * Add a Subject column to WooCommerce's Products > Reviews table.
     *
     * WooCommerce 6.7+ uses its dedicated ReviewsListTable and the
     * woocommerce_product_reviews_table_columns filter for this screen.
     */
    public function add_subject_review_table_column( $columns ) {
        $new_columns = [];

        foreach ( $columns as $key => $label ) {
            $new_columns[ $key ] = $label;

            // Place Subject between Rating and Review, matching the frontend
            // hierarchy where the rating appears immediately above the subject.
            if ( 'rating' === $key ) {
                $new_columns['gwcre_subject'] = esc_html__( 'Subject', 'global-wc-reviews-elementor' );
            }
        }

        if ( ! isset( $new_columns['gwcre_subject'] ) ) {
            $new_columns['gwcre_subject'] = esc_html__( 'Subject', 'global-wc-reviews-elementor' );
        }

        return $new_columns;
    }

    /**
     * Render the Subject column in WooCommerce's ReviewsListTable.
     *
     * @param string     $output Existing column output.
     * @param WP_Comment $item   Review being rendered.
     * @return string
     */
    public function render_subject_review_table_column( $output, $item ) {
        if ( ! $item instanceof WP_Comment ) {
            return $output;
        }

        $subject = trim( (string) get_comment_meta( $item->comment_ID, 'gwcre_subject', true ) );

        if ( '' === $subject ) {
            return '<span class="description">' . esc_html__( 'No subject', 'global-wc-reviews-elementor' ) . '</span>';
        }

        return esc_html( $subject );
    }

    /**
     * Add an editable Subject field to the admin comment edit screen.
     */
    public function add_subject_comment_meta_box() {
        add_meta_box(
            'gwcre-review-subject',
            esc_html__( 'Review Subject', 'global-wc-reviews-elementor' ),
            [ $this, 'render_subject_comment_meta_box' ],
            'comment',
            'normal',
            'high'
        );
    }

    public function render_subject_comment_meta_box( $comment ) {
        $subject = trim( (string) get_comment_meta( $comment->comment_ID, 'gwcre_subject', true ) );
        wp_nonce_field( 'gwcre_save_comment_subject_' . $comment->comment_ID, 'gwcre_subject_nonce' );
        ?>
        <p>
            <label for="gwcre-comment-subject"><strong><?php esc_html_e( 'Subject', 'global-wc-reviews-elementor' ); ?></strong></label>
        </p>
        <p>
            <input
                type="text"
                id="gwcre-comment-subject"
                name="gwcre_subject"
                value="<?php echo esc_attr( $subject ); ?>"
                maxlength="200"
                class="widefat"
            />
        </p>
        <p class="description"><?php esc_html_e( 'This subject is displayed in the Global Reviews Elementor widget.', 'global-wc-reviews-elementor' ); ?></p>
        <?php
    }

    /**
     * Save the Subject field when an admin edits a review/comment.
     */
    public function save_subject_comment_meta( $comment_id ) {
        if ( ! isset( $_POST['gwcre_subject_nonce'] ) ) {
            return;
        }

        $nonce = sanitize_text_field( wp_unslash( $_POST['gwcre_subject_nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'gwcre_save_comment_subject_' . $comment_id ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
            return;
        }

        $comment = get_comment( $comment_id );
        if ( ! $comment || 'review' !== $comment->comment_type ) {
            return;
        }

        $post_type = get_post_type( $comment->comment_post_ID );
        if ( ! in_array( $post_type, [ 'product', 'product_variation' ], true ) ) {
            return;
        }

        $subject = isset( $_POST['gwcre_subject'] )
            ? sanitize_text_field( wp_unslash( $_POST['gwcre_subject'] ) )
            : '';

        if ( '' === $subject ) {
            delete_comment_meta( $comment_id, 'gwcre_subject' );
        } else {
            update_comment_meta( $comment_id, 'gwcre_subject', $subject );
        }
    }

    public function register_widgets( $widgets_manager ) {
        require_once GWCRE_DIR . 'includes/widgets/class-global-reviews-widget.php';
        require_once GWCRE_DIR . 'includes/widgets/class-global-review-form-widget.php';
        $widgets_manager->register( new GWCRE_Global_Reviews_Widget() );
        $widgets_manager->register( new GWCRE_Global_Review_Form_Widget() );
    }

    public function register_assets() {
        wp_register_style(
            'gwcre-frontend',
            GWCRE_URL . 'assets/css/global-reviews.css',
            [],
            GWCRE_VERSION
        );

        wp_register_script(
            'gwcre-frontend',
            GWCRE_URL . 'assets/js/global-reviews.js',
            [],
            GWCRE_VERSION,
            true
        );

        wp_localize_script(
            'gwcre-frontend',
            'GWCRE',
            [
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'       => wp_create_nonce( 'gwcre_load_more' ),
                'submitNonce' => wp_create_nonce( 'gwcre_submit_review' ),
                'loading'    => esc_html__( 'Loading reviews…', 'global-wc-reviews-elementor' ),
                'submitting' => esc_html__( 'Submitting…', 'global-wc-reviews-elementor' ),
            ]
        );
    }

    public static function get_reviews( $page = 1, $per_page = 1, $sort = 'newest', $rating = 'all' ) {
        $page     = max( 1, absint( $page ) );
        $per_page = min( 50, max( 1, absint( $per_page ) ) );
        $offset   = ( $page - 1 ) * $per_page;

        $order   = 'DESC';
        $orderby = 'comment_date_gmt';

        if ( 'oldest' === $sort ) {
            $order = 'ASC';
        } elseif ( 'highest' === $sort || 'lowest' === $sort ) {
            $orderby = 'meta_value_num';
            $order   = 'highest' === $sort ? 'DESC' : 'ASC';
        }

        $args = [
            'status'    => 'approve',
            'type'      => 'review',
            'number'    => $per_page,
            'offset'    => $offset,
            'orderby'   => $orderby,
            'order'     => $order,
            'post_type' => [ 'product', 'product_variation' ],
        ];

        if ( 'highest' === $sort || 'lowest' === $sort ) {
            $args['meta_key'] = 'rating';
        }

        if ( 'all' !== $rating ) {
            if ( '0' === (string) $rating ) {
                $args['meta_query'] = [
                    'relation' => 'OR',
                    [
                        'key'     => 'rating',
                        'compare' => 'NOT EXISTS',
                    ],
                    [
                        'key'     => 'rating',
                        'value'   => 0,
                        'compare' => '=',
                    ],
                ];
            } else {
                $args['meta_query'] = [
                    [
                        'key'     => 'rating',
                        'value'   => absint( $rating ),
                        'compare' => '=',
                    ],
                ];
            }
        }

        $count_args = $args;
        unset( $count_args['number'], $count_args['offset'] );
        $count_args['count'] = true;
        $total = (int) get_comments( $count_args );

        $comments = get_comments( $args );
        $items    = [];

        foreach ( $comments as $comment ) {
            $rating_value = absint( get_comment_meta( $comment->comment_ID, 'rating', true ) );
            $rating_value = min( 5, $rating_value );

            $product_id = absint( $comment->comment_post_ID );
            $product    = wc_get_product( $product_id );

            if ( ! $product ) {
                continue;
            }

            // A review on a variation is displayed as a review of its parent product.
            if ( $product->is_type( 'variation' ) ) {
                $parent_id = $product->get_parent_id();
                if ( $parent_id ) {
                    $product_id = $parent_id;
                    $product    = wc_get_product( $parent_id );
                }
            }

            if ( ! $product ) {
                continue;
            }

            $items[] = [
                'comment_id'     => $comment->comment_ID,
                // Use the saved review author name. For older reviews where
                // comment_author is empty, fall back to the associated WordPress
                // user's display name so the reviewer name is still visible.
                'reviewer'       => ( '' !== trim( (string) $comment->comment_author )
                    ? $comment->comment_author
                    : ( $comment->user_id ? get_the_author_meta( 'display_name', $comment->user_id ) : '' ) ),
                'user_id'         => absint( $comment->user_id ),
                'content'        => $comment->comment_content,
                'subject'        => trim( (string) get_comment_meta( $comment->comment_ID, 'gwcre_subject', true ) ),
                'rating'         => $rating_value,
                'date'           => $comment->comment_date,
                'date_iso'       => mysql2date( DATE_ATOM, $comment->comment_date_gmt, true ),
                'verified'       => function_exists( 'wc_review_is_from_verified_owner' ) ? wc_review_is_from_verified_owner( $comment->comment_ID ) : false,
                'product_id'     => $product_id,
                'product_name'   => $product->get_name(),
                'product_url'    => get_permalink( $product_id ),
            ];
        }

        $total_pages = max( 1, (int) ceil( $total / $per_page ) );

        return [
            'items'       => $items,
            'has_more'    => $page < $total_pages,
            'total'       => $total,
            'total_pages' => $total_pages,
        ];
    }

    public static function render_items( array $items, array $settings ) {
        ob_start();
        foreach ( $items as $item ) {
            $rating = max( 0, min( 5, absint( $item['rating'] ) ) );
            $review_text = wpautop( wp_kses_post( $item['content'] ) );
            $reviewer_name = trim( (string) ( $item['reviewer'] ?? '' ) );

            // Some reviews created by earlier versions of this plugin may have
            // accidentally stored the reviewer's email address as comment_author.
            // Never display an email address in the public reviews widget. If the
            // comment is associated with a WordPress account, use its display name.
            if ( is_email( $reviewer_name ) ) {
                $user_id = absint( $item['user_id'] ?? 0 );
                $user_name = $user_id ? trim( (string) get_the_author_meta( 'display_name', $user_id ) ) : '';

                if ( '' !== $user_name && ! is_email( $user_name ) ) {
                    $reviewer_name = $user_name;
                } else {
                    // If the email address was stored as the author name, use
                    // the portion before the @ as the public display name.
                    $parts = explode( '@', $reviewer_name, 2 );
                    $reviewer_name = trim( (string) ( $parts[0] ?? '' ) );
                }
            }

            if ( '' === $reviewer_name ) {
                $reviewer_name = esc_html__( 'Anonymous', 'global-wc-reviews-elementor' );
            }
            ?>
            <article class="gwcre-review-card" data-review-id="<?php echo esc_attr( $item['comment_id'] ); ?>">
                <div class="gwcre-review-topline">
                    <?php if ( 'yes' === ( $settings['show_rating'] ?? 'yes' ) ) : ?>
                        <div class="gwcre-stars" aria-label="<?php echo esc_attr( sprintf( _n( '%d out of 5 stars', '%d out of 5 stars', $rating, 'global-wc-reviews-elementor' ), $rating ) ); ?>">
                            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                                <span class="gwcre-star <?php echo $i <= $rating ? 'is-filled' : ''; ?>" aria-hidden="true">★</span>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( 'yes' === ( $settings['show_verified'] ?? 'no' ) && ! empty( $item['verified'] ) ) : ?>
                        <span class="gwcre-verified"><?php esc_html_e( 'Verified buyer', 'global-wc-reviews-elementor' ); ?></span>
                    <?php endif; ?>
                </div>

                <?php if ( 'yes' === ( $settings['show_product'] ?? 'no' ) ) : ?>
                    <div class="gwcre-product">
                        <span class="gwcre-product-name"><?php echo esc_html( '' !== trim( (string) ( $item['subject'] ?? '' ) ) ? $item['subject'] : $item['product_name'] ); ?></span>
                    </div>
                <?php endif; ?>

                <div class="gwcre-review-content">
                    <?php echo $review_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>

                <div class="gwcre-review-meta">
                    <span class="gwcre-author"><?php echo esc_html( $reviewer_name ); ?></span>
                    <?php if ( 'yes' === ( $settings['show_date'] ?? 'yes' ) ) : ?>
                        <time class="gwcre-date" datetime="<?php echo esc_attr( $item['date_iso'] ); ?>"><?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $item['date'] ) ) ); ?></time>
                    <?php endif; ?>
                </div>
            </article>
            <?php
        }
        return ob_get_clean();
    }

    public function ajax_get_page() {
        check_ajax_referer( 'gwcre_load_more', 'nonce' );

        $page = isset( $_POST['page'] ) ? max( 1, absint( $_POST['page'] ) ) : 1;
        $settings = [
            'show_product'  => isset( $_POST['show_product'] ) && 'yes' === sanitize_text_field( wp_unslash( $_POST['show_product'] ) ) ? 'yes' : 'no',
            'show_rating'   => isset( $_POST['show_rating'] ) && 'yes' === sanitize_text_field( wp_unslash( $_POST['show_rating'] ) ) ? 'yes' : 'no',
            'show_verified' => isset( $_POST['show_verified'] ) && 'yes' === sanitize_text_field( wp_unslash( $_POST['show_verified'] ) ) ? 'yes' : 'no',
            'show_avatar'   => isset( $_POST['show_avatar'] ) && 'yes' === sanitize_text_field( wp_unslash( $_POST['show_avatar'] ) ) ? 'yes' : 'no',
            'show_date'     => isset( $_POST['show_date'] ) && 'yes' === sanitize_text_field( wp_unslash( $_POST['show_date'] ) ) ? 'yes' : 'no',
        ];

        $per_page = isset( $_POST['per_page'] ) ? min( 3, max( 1, absint( $_POST['per_page'] ) ) ) : 1;
        $sort     = isset( $_POST['sort'] ) ? sanitize_key( wp_unslash( $_POST['sort'] ) ) : 'newest';
        $rating   = isset( $_POST['rating'] ) ? sanitize_text_field( wp_unslash( $_POST['rating'] ) ) : 'all';

        $result = self::get_reviews( $page, $per_page, $sort, $rating );

        wp_send_json_success( [
            'html'        => self::render_items( $result['items'], $settings ),
            'has_more'    => $result['has_more'],
            'total_pages' => $result['total_pages'],
        ] );
    }

    public function ajax_submit_review() {
        check_ajax_referer( 'gwcre_submit_review', 'nonce' );

        if ( ! class_exists( 'WooCommerce' ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'WooCommerce is required.', 'global-wc-reviews-elementor' ) ], 400 );
        }

        if ( ! empty( $_POST['website'] ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Unable to submit the review.', 'global-wc-reviews-elementor' ) ], 400 );
        }

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        $rating     = isset( $_POST['rating'] ) ? absint( $_POST['rating'] ) : 0;
        $subject    = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
        $author     = isset( $_POST['author'] ) ? sanitize_text_field( wp_unslash( $_POST['author'] ) ) : '';
        $email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
        $content    = isset( $_POST['review'] ) ? wp_kses_post( wp_unslash( $_POST['review'] ) ) : '';

        $product = $product_id ? wc_get_product( $product_id ) : false;
        if ( ! $product || ! $product->is_visible() || ! in_array( $product->get_type(), [ 'simple', 'variable', 'grouped', 'external' ], true ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Please select a valid product.', 'global-wc-reviews-elementor' ) ], 400 );
        }

        if ( ! comments_open( $product_id ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Reviews are currently closed for this product.', 'global-wc-reviews-elementor' ) ], 400 );
        }

        if ( $rating < 1 || $rating > 5 ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Please select a rating from 1 to 5 stars.', 'global-wc-reviews-elementor' ) ], 400 );
        }

        if ( '' === $subject ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Please enter a subject.', 'global-wc-reviews-elementor' ) ], 400 );
        }

        if ( '' === $author ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Please enter your name.', 'global-wc-reviews-elementor' ) ], 400 );
        }

        $require_email = isset( $_POST['require_email'] ) && 'yes' === sanitize_text_field( wp_unslash( $_POST['require_email'] ) );
        if ( $require_email && ! is_email( $email ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Please enter a valid email address.', 'global-wc-reviews-elementor' ) ], 400 );
        }

        if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Please write a review before submitting.', 'global-wc-reviews-elementor' ) ], 400 );
        }

        if ( '1' !== ( isset( $_POST['confirm_experience'] ) ? sanitize_text_field( wp_unslash( $_POST['confirm_experience'] ) ) : '0' ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Please confirm that the review is based on your own experience.', 'global-wc-reviews-elementor' ) ], 400 );
        }

        $user_id = is_user_logged_in() ? absint( get_current_user_id() ) : 0;
        // Preserve the name and email explicitly entered in this form.
        // Logged-in users may intentionally submit a different display name or email.

        $commentdata = [
            'comment_post_ID'      => $product_id,
            'comment_author'       => $author,
            'comment_author_email' => $email,
            'comment_content'      => $content,
            'comment_type'         => 'review',
            'comment_parent'       => 0,
            'user_id'              => $user_id,
            'user_agent'           => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
            'comment_author_IP'    => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
        ];

        $allowed = wp_allow_comment( $commentdata, true );
        if ( is_wp_error( $allowed ) ) {
            wp_send_json_error( [ 'message' => $allowed->get_error_message() ], 400 );
        }

        if ( 'spam' === $allowed || 'trash' === $allowed ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Your review could not be accepted.', 'global-wc-reviews-elementor' ) ], 400 );
        }

        // All submitted reviews must remain pending so an administrator can review them
        // before they become visible in WooCommerce.
        $commentdata['comment_approved'] = 0;
        $comment_id = wp_insert_comment( wp_slash( $commentdata ) );

        if ( ! $comment_id ) {
            wp_send_json_error( [ 'message' => esc_html__( 'The review could not be saved. Please try again.', 'global-wc-reviews-elementor' ) ], 500 );
        }

        update_comment_meta( $comment_id, 'rating', $rating );
        update_comment_meta( $comment_id, 'gwcre_subject', $subject );

        $message = esc_html__( 'Thank you! Your review has been submitted and is awaiting approval.', 'global-wc-reviews-elementor' );

        wp_send_json_success( [
            'message'     => $message,
            'approved'    => $approved,
            'productName' => $product->get_name(),
            'productUrl'  => get_permalink( $product_id ),
        ] );
    }

}

new GWCRE_Plugin();
