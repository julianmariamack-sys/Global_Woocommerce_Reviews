<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class GWCRE_Global_Review_Form_Widget extends \Elementor\Widget_Base {

    public function get_name(): string {
        return 'gwcre-global-review-form';
    }

    public function get_title(): string {
        return esc_html__( 'Global Write a Review', 'global-wc-reviews-elementor' );
    }

    public function get_icon(): string {
        return 'eicon-form-horizontal';
    }

    public function get_categories(): array {
        return [ 'woocommerce-elements' ];
    }

    public function get_keywords(): array {
        return [ 'review', 'reviews', 'write review', 'submit review', 'woocommerce', 'product', 'rating', 'form' ];
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
                'label' => esc_html__( 'Review Form', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'heading',
            [
                'label'   => esc_html__( 'Heading', 'global-wc-reviews-elementor' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Write a Review', 'global-wc-reviews-elementor' ),
            ]
        );

        $this->add_control(
            'intro_text',
            [
                'label'   => esc_html__( 'Intro Text', 'global-wc-reviews-elementor' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'Tell us what you think about the product.', 'global-wc-reviews-elementor' ),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label'   => esc_html__( 'Submit Button Text', 'global-wc-reviews-elementor' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Submit Review', 'global-wc-reviews-elementor' ),
            ]
        );

        $this->add_control(
            'require_email',
            [
                'label'   => esc_html__( 'Require Email', 'global-wc-reviews-elementor' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_product_link',
            [
                'label'   => esc_html__( 'Show Product Link After Submit', 'global-wc-reviews-elementor' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'form_style',
            [
                'label' => esc_html__( 'Form', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'form_width',
            [
                'label'      => esc_html__( 'Form Width', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range'      => [
                    'px' => [ 'min' => 300, 'max' => 1200 ],
                    '%'  => [ 'min' => 50, 'max' => 100 ],
                ],
                'default' => [ 'unit' => '%', 'size' => 100 ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-form-inner' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'form_background',
                'selector' => '{{WRAPPER}} .gwcre-review-form-inner',
            ]
        );

        $this->add_responsive_control(
            'form_padding',
            [
                'label'      => esc_html__( 'Padding', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top' => 32, 'right' => 34, 'bottom' => 32, 'left' => 34, 'unit' => 'px', 'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-form-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default'    => [ 'top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6, 'unit' => 'px', 'isLinked' => true ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-form-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'form_shadow',
            [
                'label'     => esc_html__( 'Box Shadow', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => '0 8px 20px rgba(0,0,0,0.10)',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-form-inner' => 'box-shadow: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'field_style',
            [
                'label' => esc_html__( 'Fields', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label'     => esc_html__( 'Label Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-form label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'label_typography',
                'selector' => '{{WRAPPER}} .gwcre-review-form label',
            ]
        );

        $this->add_control(
            'field_background',
            [
                'label'     => esc_html__( 'Field Background', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-form input, {{WRAPPER}} .gwcre-review-form select, {{WRAPPER}} .gwcre-review-form textarea' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_border',
            [
                'label'     => esc_html__( 'Field Border Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#D8D8D8',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-form input, {{WRAPPER}} .gwcre-review-form select, {{WRAPPER}} .gwcre-review-form textarea' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_radius',
            [
                'label'      => esc_html__( 'Field Radius', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
                'default'    => [ 'unit' => 'px', 'size' => 4 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-form input, {{WRAPPER}} .gwcre-review-form select, {{WRAPPER}} .gwcre-review-form textarea' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'star_style',
            [
                'label' => esc_html__( 'Rating Stars', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'star_color',
            [
                'label'     => esc_html__( 'Selected Star Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#FFD400',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-rating-button.is-selected, {{WRAPPER}} .gwcre-rating-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'empty_star_color',
            [
                'label'     => esc_html__( 'Unselected Star Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#00000000',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-rating-button:not(.is-selected)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'star_size',
            [
                'label'      => esc_html__( 'Star Size', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [ 'px' => [ 'min' => 18, 'max' => 50 ] ],
                'default'    => [ 'unit' => 'px', 'size' => 30 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-rating-button' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'star_spacing',
            [
                'label'      => esc_html__( 'Star Spacing', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
                'default'    => [ 'unit' => 'px', 'size' => 10 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-rating-input' => 'gap: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'button_style',
            [
                'label' => esc_html__( 'Submit Button', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label'     => esc_html__( 'Background', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#222222',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-submit' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label'     => esc_html__( 'Text Color', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-submit' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_background',
            [
                'label'     => esc_html__( 'Hover Background', 'global-wc-reviews-elementor' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#444444',
                'selectors' => [
                    '{{WRAPPER}} .gwcre-review-submit:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_width_desktop',
            [
                'label'      => esc_html__( 'Button Width — 1280px and larger', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range'      => [
                    'px' => [ 'min' => 50, 'max' => 1600 ],
                    '%'  => [ 'min' => 5, 'max' => 100 ],
                    'vw' => [ 'min' => 5, 'max' => 100 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 1200 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-submit' => '--gwcre-button-width-desktop: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_margin_left_desktop',
            [
                'label'      => esc_html__( 'Button Left Margin — 1280px and larger', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 1600 ],
                    '%'  => [ 'min' => 0, 'max' => 100 ],
                    'vw' => [ 'min' => 0, 'max' => 100 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 0 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-submit' => '--gwcre-button-margin-left-desktop: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_width_tablet',
            [
                'label'      => esc_html__( 'Button Width — 768px to 1279px', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range'      => [
                    'px' => [ 'min' => 50, 'max' => 1600 ],
                    '%'  => [ 'min' => 5, 'max' => 100 ],
                    'vw' => [ 'min' => 5, 'max' => 100 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 1200 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-submit' => '--gwcre-button-width-tablet: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_margin_left_tablet',
            [
                'label'      => esc_html__( 'Button Left Margin — 768px to 1279px', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 1600 ],
                    '%'  => [ 'min' => 0, 'max' => 100 ],
                    'vw' => [ 'min' => 0, 'max' => 100 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 0 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-submit' => '--gwcre-button-margin-left-tablet: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_width_mobile',
            [
                'label'      => esc_html__( 'Button Width — 0px to 767px', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range'      => [
                    'px' => [ 'min' => 50, 'max' => 1600 ],
                    '%'  => [ 'min' => 5, 'max' => 100 ],
                    'vw' => [ 'min' => 5, 'max' => 100 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 1200 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-submit' => '--gwcre-button-width-mobile: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_margin_left_mobile',
            [
                'label'      => esc_html__( 'Button Left Margin — 0px to 767px', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 1600 ],
                    '%'  => [ 'min' => 0, 'max' => 100 ],
                    'vw' => [ 'min' => 0, 'max' => 100 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 0 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-submit' => '--gwcre-button-margin-left-mobile: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_radius',
            [
                'label'      => esc_html__( 'Button Radius', 'global-wc-reviews-elementor' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
                'default'    => [ 'unit' => 'px', 'size' => 4 ],
                'selectors'  => [
                    '{{WRAPPER}} .gwcre-review-submit' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'confirmation_style',
            [
                'label' => esc_html__( 'Confirmation', 'global-wc-reviews-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'confirmation_text',
            [
                'label'   => esc_html__( 'Confirmation Text', 'global-wc-reviews-elementor' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'I confirm this review is based on my own experience.', 'global-wc-reviews-elementor' ),
            ]
        );

        $this->end_controls_section();
    }

    protected function render(): void {
        $settings = $this->get_settings_for_display();

        if ( ! class_exists( 'WooCommerce' ) ) {
            echo '<div class="gwcre-notice">' . esc_html__( 'Global Write a Review requires WooCommerce.', 'global-wc-reviews-elementor' ) . '</div>';
            return;
        }

        wp_enqueue_style( 'gwcre-frontend' );
        wp_enqueue_script( 'gwcre-frontend' );

        $heading    = trim( (string) ( $settings['heading'] ?? '' ) );
        $intro_text = trim( (string) ( $settings['intro_text'] ?? '' ) );
        $button     = trim( (string) ( $settings['button_text'] ?? '' ) );
        $id         = 'gwcre-form-' . esc_attr( $this->get_id() );
        ?>
        <section
            id="<?php echo esc_attr( $id ); ?>"
            class="gwcre-review-form-widget"
            data-require-email="<?php echo esc_attr( $settings['require_email'] ?? 'yes' ); ?>"
        >
            <div class="gwcre-review-form-inner">
                <?php if ( '' !== $heading ) : ?>
                    <h2 class="gwcre-form-heading"><?php echo esc_html( $heading ); ?></h2>
                <?php endif; ?>

                <?php if ( '' !== $intro_text ) : ?>
                    <p class="gwcre-form-intro"><?php echo esc_html( $intro_text ); ?></p>
                <?php endif; ?>

                <form class="gwcre-review-form" novalidate>
                    <div class="gwcre-form-message" role="status" aria-live="polite"></div>

                    <?php
                    global $product;
                    $current_product_id = 0;
                    if ( is_object( $product ) && is_a( $product, 'WC_Product' ) ) {
                        $current_product_id = $product->get_id();
                    } elseif ( function_exists( 'is_product' ) && is_product() ) {
                        $current_product_id = absint( get_the_ID() );
                    }
                    ?>
                    <input type="hidden" name="product_id" value="<?php echo esc_attr( $current_product_id ); ?>">

                    <div class="gwcre-form-row">
                        <div class="gwcre-form-field gwcre-form-field-full">
                            <label for="<?php echo esc_attr( $id ); ?>-subject"><?php esc_html_e( 'Subject', 'global-wc-reviews-elementor' ); ?> <span aria-hidden="true">*</span></label>
                            <input id="<?php echo esc_attr( $id ); ?>-subject" type="text" name="subject" maxlength="200" required>
                        </div>
                    </div>

                    <div class="gwcre-form-row">
                        <div class="gwcre-form-field">
                            <label><?php esc_html_e( 'Your Rating', 'global-wc-reviews-elementor' ); ?> <span aria-hidden="true">*</span></label>
                            <div class="gwcre-rating-input" role="radiogroup" aria-label="<?php esc_attr_e( 'Product rating', 'global-wc-reviews-elementor' ); ?>">
                                <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                                    <button type="button" class="gwcre-rating-button" data-rating="<?php echo esc_attr( $i ); ?>" role="radio" aria-checked="false" aria-label="<?php echo esc_attr( sprintf( _n( '%d star', '%d stars', $i, 'global-wc-reviews-elementor' ), $i ) ); ?>">★</button>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" value="" required>
                        </div>
                    </div>

                    <div class="gwcre-form-row gwcre-form-row-two">
                        <div class="gwcre-form-field">
                            <label for="<?php echo esc_attr( $id ); ?>-name"><?php esc_html_e( 'Name', 'global-wc-reviews-elementor' ); ?> <span aria-hidden="true">*</span></label>
                            <input id="<?php echo esc_attr( $id ); ?>-name" type="text" name="author" autocomplete="name" required placeholder="<?php esc_attr_e( 'First and Last Name', 'global-wc-reviews-elementor' ); ?>" value="">
                        </div>
                        <div class="gwcre-form-field">
                            <label for="<?php echo esc_attr( $id ); ?>-email"><?php esc_html_e( 'Email', 'global-wc-reviews-elementor' ); ?><?php echo 'yes' === ( $settings['require_email'] ?? 'yes' ) ? ' *' : ''; ?></label>
                            <input id="<?php echo esc_attr( $id ); ?>-email" type="email" name="email" autocomplete="email" placeholder="<?php esc_attr_e( 'Your Email', 'global-wc-reviews-elementor' ); ?>" <?php echo 'yes' === ( $settings['require_email'] ?? 'yes' ) ? 'required' : ''; ?> value="">
                        </div>
                    </div>

                    <div class="gwcre-form-row">
                        <div class="gwcre-form-field gwcre-form-field-full">
                            <label for="<?php echo esc_attr( $id ); ?>-review"><?php esc_html_e( 'Your Review', 'global-wc-reviews-elementor' ); ?> <span aria-hidden="true">*</span></label>
                            <textarea id="<?php echo esc_attr( $id ); ?>-review" name="review" rows="6" required></textarea>
                        </div>
                    </div>

                    <div class="gwcre-review-confirm">
                        <input id="<?php echo esc_attr( $id ); ?>-confirm" type="checkbox" name="confirm_experience" value="1" required>
                        <label for="<?php echo esc_attr( $id ); ?>-confirm"><?php echo esc_html( $settings['confirmation_text'] ?? __( 'I confirm this review is based on my own experience.', 'global-wc-reviews-elementor' ) ); ?></label>
                    </div>

                    <input type="text" name="website" class="gwcre-honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <button type="submit" class="gwcre-review-submit"><span class="gwcre-submit-label"><?php echo esc_html( $button ?: __( 'Submit Review', 'global-wc-reviews-elementor' ) ); ?></span></button>
                </form>
            </div>
        </section>
        <?php
    }
}
