<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class GWCRE_Global_Reviews_Widget extends \Elementor\Widget_Base {

    public function get_name(): string {
        return 'gwcre-global-reviews';
    }

    public function get_title(): string {
        return esc_html__( 'Global Product Reviews', 'global-wc-reviews-elementor' );
    }

    public function get_icon(): string {
        return 'eicon-comments';
    }

    public function get_categories(): array {
        return [ 'woocommerce-elements' ];
    }

    public function get_keywords(): array {
        return [ 'reviews', 'review', 'woocommerce', 'products', 'ratings', 'testimonials', 'carousel', 'pagination', 'global' ];
    }

    public function get_style_depends(): array {
        return [ 'gwcre-frontend' ];
    }

    public function get_script_depends(): array {
        return [ 'gwcre-frontend' ];
    }

    protected function register_controls(): void {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Reviews', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'heading',
            [
                'label'   => esc_html__( 'Heading', 'global-wc-reviews-elementor' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Reviews', 'global-wc-reviews-elementor' ),
            ]
        );

        $this->add_control(
            'reviews_per_page',
            [
                'label'   => esc_html__( 'Reviews Per Page', 'global-wc-reviews-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                ],
                'description' => esc_html__( 'Use 1 to match the single-review carousel layout shown in the reference image.', 'global-wc-reviews-elementor' ),
            ]
        );

        $this->add_control(
            'sort',
            [
                'label'   => esc_html__( 'Sort Reviews', 'global-wc-reviews-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'newest',
                'options' => [
                    'newest'  => esc_html__( 'Newest First', 'global-wc-reviews-elementor' ),
                    'oldest'  => esc_html__( 'Oldest First', 'global-wc-reviews-elementor' ),
                    'highest' => esc_html__( 'Highest Rated First', 'global-wc-reviews-elementor' ),
                    'lowest'  => esc_html__( 'Lowest Rated First', 'global-wc-reviews-elementor' ),
                ],
            ]
        );

        $this->add_control(
            'rating_filter',
            [
                'label'   => esc_html__( 'Rating Filter', 'global-wc-reviews-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all' => esc_html__( 'All Ratings', 'global-wc-reviews-elementor' ),
                    '5'   => esc_html__( '5 Stars', 'global-wc-reviews-elementor' ),
                    '4'   => esc_html__( '4 Stars', 'global-wc-reviews-elementor' ),
                    '3'   => esc_html__( '3 Stars', 'global-wc-reviews-elementor' ),
                    '2'   => esc_html__( '2 Stars', 'global-wc-reviews-elementor' ),
                    '1'   => esc_html__( '1 Star', 'global-wc-reviews-elementor' ),
                    '0'   => esc_html__( '0 Stars / Unrated', 'global-wc-reviews-elementor' ),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'display_section',
            [
                'label' => esc_html__( 'Display Options', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        foreach ( [
            'show_product'  => esc_html__( 'Show Subject', 'global-wc-reviews-elementor' ),
            'show_rating'   => esc_html__( 'Show Star Rating', 'global-wc-reviews-elementor' ),
            'show_verified' => esc_html__( 'Show Verified Buyer Label', 'global-wc-reviews-elementor' ),
            'show_avatar'   => esc_html__( 'Show Reviewer Avatar', 'global-wc-reviews-elementor' ),
            'show_date'     => esc_html__( 'Show Review Date', 'global-wc-reviews-elementor' ),
            'show_pagination' => esc_html__( 'Show Pagination', 'global-wc-reviews-elementor' ),
        ] as $key => $label ) {
            $default = in_array( $key, [ 'show_rating', 'show_avatar', 'show_date', 'show_pagination' ], true ) ? 'yes' : 'no';
            $this->add_control(
                $key,
                [
                    'label'   => $label,
                    'type'    => \Elementor\Controls_Manager::SWITCHER,
                    'default' => $default,
                ]
            );
        }

        $this->end_controls_section();

        $this->start_controls_section(
            'layout_style',
            [
                'label' => esc_html__( 'Carousel Layout', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'card_width',
            [
                'label'      => esc_html__( 'Card Width', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range'      => [
                    'px' => [ 'min' => 360, 'max' => 1400, 'step' => 1 ],
                    '%'  => [ 'min' => 50, 'max' => 100, 'step' => 1 ],
                ],
                'default' => [ 'unit' => '%', 'size' => 88 ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-carousel-stage' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'stage_padding',
            [
                'label'      => esc_html__( 'Stage Side Padding', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 20, 'max' => 140, 'step' => 1 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 70 ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-carousel-shell' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'card_style',
            [
                'label' => esc_html__( 'Card', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'card_background',
                'selector' => '{{WRAPPER}} .gwcre-review-card',
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label'      => esc_html__( 'Padding', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top' => 28, 'right' => 54, 'bottom' => 18, 'left' => 54, 'unit' => 'px', 'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default'    => [ 'top' => 4, 'right' => 4, 'bottom' => 4, 'left' => 4, 'unit' => 'px', 'isLinked' => true ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_shadow_color',
            [
                'label'     => esc_html__( 'Shadow Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#0000002E',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-card' => '--gwcre-shadow-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_shadow_x',
            [
                'label'      => esc_html__( 'X-Position', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => -50, 'max' => 50, 'step' => 1 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 0 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-card' => '--gwcre-shadow-x: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_shadow_y',
            [
                'label'      => esc_html__( 'Y-Position', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => -50, 'max' => 50, 'step' => 1 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 8 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-card' => '--gwcre-shadow-y: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_shadow_spread',
            [
                'label'      => esc_html__( 'Spread', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => -50, 'max' => 50, 'step' => 1 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 0 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-card' => '--gwcre-shadow-spread: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_shadow_blur',
            [
                'label'      => esc_html__( 'Blur', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 10 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-card' => '--gwcre-shadow-blur: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'review_style',
            [
                'label' => esc_html__( 'Review Content', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'review_typography',
                'selector' => '{{WRAPPER}} .gwcre-review-content',
            ]
        );

        $this->add_responsive_control(
            'review_font_size',
            [
                'label'      => esc_html__( 'Font Size', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 10, 'max' => 60, 'step' => 1 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 15 ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-content' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'review_color',
            [
                'label'     => esc_html__( 'Review Text Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'review_max_width',
            [
                'label'      => esc_html__( 'Text Max Width', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [ 'min' => 300, 'max' => 1100 ],
                    '%'  => [ 'min' => 50, 'max' => 100 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 780 ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-content' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'accent_style',
            [
                'label' => esc_html__( 'Stars, Author & Product', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'star_color',
            [
                'label'     => esc_html__( 'Star Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#D7A62A',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-star.is-filled' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'empty_star_color',
            [
                'label'     => esc_html__( 'Empty Star Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#D6D6D6',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-star:not(.is-filled)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'star_size',
            [
                'label'      => esc_html__( 'Star Size', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 12, 'max' => 60, 'step' => 1 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 23 ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-stars' => '--gwcre-star-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'star_border_weight',
            [
                'label'      => esc_html__( 'Star Border Weight', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 5, 'step' => 0.25 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 1.5 ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-star' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'product_color',
            [
                'label'     => esc_html__( 'Product Link Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gwcre-product' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'product_font_size',
            [
                'label'      => esc_html__( 'Product Font Size', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 10, 'max' => 40, 'step' => 1 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 14 ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-product' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'author_color',
            [
                'label'     => esc_html__( 'Author Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gwcre-author' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'date_color',
            [
                'label'     => esc_html__( 'Date Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gwcre-date' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'pagination_style',
            [
                'label' => esc_html__( 'Pagination', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'arrow_size',
            [
                'label'      => esc_html__( 'Arrow / Circle Size', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 24, 'max' => 100, 'step' => 1 ],
                ],
                'default' => [ 'unit' => 'px', 'size' => 48 ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-arrow' => '--gwcre-arrow-diameter: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_padding',
            [
                'label'      => esc_html__( 'Arrow Padding', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
                'default'    => [
                    'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label'     => esc_html__( 'Arrow Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#D4D4D4',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-arrow' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_hover_color',
            [
                'label'     => esc_html__( 'Arrow Hover Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#8F8F8F',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-arrow:hover:not(:disabled)' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_color',
            [
                'label'     => esc_html__( 'Pagination Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#9B9B9B',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-page-indicator' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render(): void {
        $settings = $this->get_settings_for_display();

        if ( ! class_exists( 'WooCommerce' ) ) {
            echo '<div class="gwcre-notice">' . esc_html__( 'Global Product Reviews requires WooCommerce.', 'global-wc-reviews-elementor' ) . '</div>';
            return;
        }

        wp_enqueue_style( 'gwcre-frontend' );
        wp_enqueue_script( 'gwcre-frontend' );

        $per_page = min( 3, max( 1, absint( $settings['reviews_per_page'] ?? 1 ) ) );
        $sort     = sanitize_key( $settings['sort'] ?? 'newest' );
        $rating   = sanitize_text_field( $settings['rating_filter'] ?? 'all' );
        $result   = GWCRE_Plugin::get_reviews( 1, $per_page, $sort, $rating );
        $total_pages = max( 1, absint( $result['total_pages'] ?? 1 ) );

        $heading = trim( (string) ( $settings['heading'] ?? '' ) );
        $id = 'gwcre-' . esc_attr( $this->get_id() );
        ?>
        <section
            id="<?php echo esc_attr( $id ); ?>"
            class="gwcre-widget"
            data-page="1"
            data-total-pages="<?php echo esc_attr( $total_pages ); ?>"
            data-per-page="<?php echo esc_attr( $per_page ); ?>"
            data-sort="<?php echo esc_attr( $sort ); ?>"
            data-rating="<?php echo esc_attr( $rating ); ?>"
            data-show-product="<?php echo esc_attr( $settings['show_product'] ?? 'no' ); ?>"
            data-show-rating="<?php echo esc_attr( $settings['show_rating'] ?? 'yes' ); ?>"
            data-show-verified="<?php echo esc_attr( $settings['show_verified'] ?? 'no' ); ?>"
            data-show-avatar="<?php echo esc_attr( $settings['show_avatar'] ?? 'yes' ); ?>"
            data-show-date="<?php echo esc_attr( $settings['show_date'] ?? 'yes' ); ?>"
        >
            <?php if ( '' !== $heading ) : ?>
                <h2 class="gwcre-heading"><?php echo esc_html( $heading ); ?></h2>
            <?php endif; ?>

            <div class="gwcre-carousel-shell">
                <button type="button" class="gwcre-arrow gwcre-arrow-prev" aria-label="<?php esc_attr_e( 'Previous review', 'global-wc-reviews-elementor' ); ?>" <?php disabled( true, true ); ?>>‹</button>

                <div class="gwcre-carousel-stage" aria-live="polite" aria-atomic="true">
                    <div class="gwcre-slide-container">
                        <?php if ( ! empty( $result['items'] ) ) : ?>
                            <?php echo GWCRE_Plugin::render_items( $result['items'], $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php else : ?>
                            <div class="gwcre-empty"><?php esc_html_e( 'There are no approved product reviews to display yet.', 'global-wc-reviews-elementor' ); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="button" class="gwcre-arrow gwcre-arrow-next" aria-label="<?php esc_attr_e( 'Next review', 'global-wc-reviews-elementor' ); ?>" <?php disabled( 1 >= $total_pages, true ); ?>>›</button>
            </div>

            <?php if ( ! empty( $settings['show_pagination'] ) && 'yes' === $settings['show_pagination'] && $total_pages > 1 ) : ?>
                <div class="gwcre-pagination" role="navigation" aria-label="<?php esc_attr_e( 'Review pagination', 'global-wc-reviews-elementor' ); ?>">
                    <span class="gwcre-page-indicator">1 / <?php echo esc_html( $total_pages ); ?></span>
                </div>
            <?php endif; ?>
        </section>
        <?php
    }
}
