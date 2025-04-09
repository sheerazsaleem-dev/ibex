<?php

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Plugin as Elementor;
use ElementorPro\Modules\LoopBuilder\Documents\Loop as LoopDocument;
use ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Dynamic_CSS;
use ElementorPro\Modules\QueryControl\Controls\Template_Query;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Pagination;
use The7_Categorization_Request;
use The7_Elementor_Compatibility;
use The7_Query_Builder;
use The7_Related_Query_Builder;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Woocommerce\Products_Query as Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Query;

class Slider_Loop extends Slider
{

    const WIDGET_NAME = 'the7-slider-loop';

    const AUTOPLAY_DEFAULT = 'no';
    const SLIDES_PER_VIEW_DEFAULT = '3';

    protected $_has_template_content = false;

    /**
     * @var \WP_Query
     */
    private $_query = null;

    /**
     * @return string
     */
    public function get_name()
    {
        return self::WIDGET_NAME;
    }

    public function render()
    {
        $settings = $this->get_settings_for_display();
        if ( ! empty($settings['template_id'])) {
            $this->_query = $this->query_posts();
            $this->add_render_attribute('elementor_swiper_container', 'class', [
                'elementor-loop-container',
                'elementor-grid',
            ]);
        }
        parent::render();
    }

    protected function query_posts()
    {
        $settings = $this->get_settings_for_display();

        $post_type_settings = 'post_type';
        if ($this->is_product_template()) {
            $post_type_settings = 'query_' . $post_type_settings;
        }
        $post_type = $settings[ $post_type_settings ];

        if ($post_type === 'current_query') {
            return static::get_current_query($settings);
        }

        if ($this->is_product_template()) {
            $settings['posts_offset'] = $settings['product_posts_offset'];

            // Loop query.
            $query_builder = new Products_Query($settings, 'query_');

            $query_builder->add_pre_query_hooks();
            $query_args = $query_builder->parse_query_args();
            $query_args['paged'] = $this->template(Pagination::class)->get_paged();
            $query_args['posts_per_page'] = $settings['product_dis_posts_total'];
            $query = new \WP_Query($query_args);

            $query_builder->remove_pre_query_hooks();

            return $query;
        }

        $taxonomy = $settings['taxonomy'];
        $terms = $settings['terms'];

        // Loop query.
        $query_args = [
            'posts_offset'   => $settings['posts_offset'],
            'post_type'      => $post_type,
            'order'          => $settings['order'],
            'orderby'        => $settings['orderby'],
            'posts_per_page' => $settings['dis_posts_total'],
        ];

        if ($post_type === 'related') {
            $query_builder = new The7_Related_Query_Builder($query_args);
        } else {
            $query_builder = new The7_Query_Builder($query_args);
        }

        $query_builder->from_terms($taxonomy, $terms);

        $request = new The7_Categorization_Request();
        if ($request->taxonomy && $request->not_empty()) {
            $query_builder->with_categorizaition($request);
        }

        return $query_builder->query();
    }

    protected function add_content_controls()
    {
        //'section_layout' name is important for createTemplate js function
        $this->start_controls_section('section_layout', [
            'label' => esc_html__('Loop Template', 'the7mk2'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('slider_wrap_helper', [
            'type'         => Controls_Manager::HIDDEN,
            'default'      => 'elementor-widget-the7-slider-common owl-carousel elementor-widget-loop-the7-slider',
            'prefix_class' => '',
        ]);

        $this->add_slide_content_controls();
        $this->add_slider_height_controls();

        $this->end_controls_section();
    }

    protected function add_slide_content_controls()
    {
        //we should use _skin contoll in order to use inline editing (in this case we use only '_skin' controll to emulate skin usage).
        //The skin name should be 'post'
        $this->add_control('_skin', [
            'label'   => esc_html__('Skin', 'elementor'),
            'type'    => Controls_Manager::HIDDEN,
            'default' => 'post',
        ]);

		$this->add_control( 'wc_is_active', [
			'type'    => Controls_Manager::HIDDEN,
			'default' => the7_is_woocommerce_enabled() ? 'y' : '',
		] );

        if ( the7_is_woocommerce_enabled() ) {
			$this->add_control( 'template_type', [
				'label'   => esc_html__( 'Choose Template Type', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'posts',
				'options' => [
					'posts'    => esc_html__( 'Posts', 'the7mk2' ),
					'products' => esc_html__( 'Products', 'the7mk2' ),
                ],
			] );
		} else {
			$this->add_control( 'template_type', [
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'posts',
			] );
        }

        $this->add_control('template_id', [
            'label'              => esc_html__('Choose Loop Template', 'the7mk2'),
            'type'               => Template_Query::CONTROL_ID,
            'label_block'        => true,
            'autocomplete'       => [
                'object' => QueryControlModule::QUERY_OBJECT_LIBRARY_TEMPLATE,
                'query'  => [
                    'post_status' => Document::STATUS_PUBLISH,
                    'meta_query'  => [
                        [
                            'key'     => Document::TYPE_META_KEY,
                            'value'   => LoopDocument::get_type(),
                            'compare' => 'IN',
                        ],
                    ],
                ],
            ],
            'actions'            => [
                'new'  => [
                    'visible'         => true,
                    'document_config' => [
                        'type' => LoopDocument::get_type(),
                    ],
                ],
                'edit' => [
                    'visible' => true,
                ],
            ],
            'frontend_available' => true,
        ]);
    }

    protected function add_slider_height_controls()
    {

        $this->add_control('equal_height', [
            'label'     => esc_html__('Equal height', 'the7mk2'),
            'type'      => Controls_Manager::SWITCHER,
            'label_off' => esc_html__('Off', 'the7mk2'),
            'label_on'  => esc_html__('On', 'the7mk2'),
            'default'   => 'yes',
            'condition' => [
                'dis_posts_total!' => 1,
                'template_id!'     => '',
            ],
            'selectors' => [
                '{{WRAPPER}} .e-loop-item > .elementor-section,
					 {{WRAPPER}} .e-loop-item > .elementor-section > .elementor-container,
					 {{WRAPPER}} .e-loop-item > .e-con,
					 {{WRAPPER}} .e-loop-item .elementor-section-wrap  > .e-con, 
                     {{WRAPPER}} .the7-swiper-slide' => 'height: 100%',
            ],
        ]);
    }

    protected function get_initial_config()
    {
        $config = parent::get_initial_config();

        $config['is_loop'] = true;
        $config['edit_handle_selector'] = '.elementor-widget-container';

        return $config;
    }

    /**
     * @return string|void
     */
    protected function the7_title()
    {
        return esc_html__('Loop Slider', 'the7mk2');
    }

    /**
     * @return string[]
     */
    protected function the7_keywords()
    {
        return ['slides', 'carousel', 'image', 'slider', 'loop', 'custom post type', 'carousel'];
    }

    /**
     * this section only for loop skin
     * @return void
     */
    protected function add_query_content_controls()
    {
        $this->add_posts_query_content_controls();
        $this->add_products_query_content_controls();
    }

    /**
     * @return void
     */
    protected function add_posts_query_content_controls()
    {
        /**
         * Must have section_id = query_section to work properly.
         * @see elements-widget-settings.js:onEditSettings()
         */
        $this->start_controls_section('query_section', [
			'label'      => esc_html__( 'Query', 'the7mk2' ),
			'tab'        => Controls_Manager::TAB_CONTENT,
			'conditions' => [
				'relation' => 'or',
				'terms'    => [
					[
						'name'     => 'template_type',
						'operator' => '=',
						'value'    => 'posts',
					],
					[
						'name'     => 'wc_is_active',
						'operator' => '!=',
						'value'    => 'y',
					],
				],
			],
        ]);

        $this->add_control('post_type', [
            'label'   => esc_html__('Source', 'the7mk2'),
            'type'    => Controls_Manager::SELECT2,
            'default' => 'post',
            'options' => the7_elementor_elements_widget_post_types() + ['related' => esc_html__('Related', 'the7mk2')],
            'classes' => 'select2-medium-width',
        ]);

        $this->add_control('taxonomy', [
            'label'     => esc_html__('Select Taxonomy', 'the7mk2'),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'category',
            'options'   => [],
            'classes'   => 'select2-medium-width',
            'condition' => [
                'post_type!' => ['', 'current_query'],
            ],
        ]);

        $this->add_control('terms', [
            'label'     => esc_html__('Select Terms', 'the7mk2'),
            'type'      => Controls_Manager::SELECT2,
            'default'   => '',
            'multiple'  => true,
            'options'   => [],
            'classes'   => 'select2-medium-width',
            'condition' => [
                'taxonomy!'  => '',
                'post_type!' => ['current_query', 'related'],
            ],
        ]);

        $this->add_control('order', [
            'label'     => esc_html__('Order', 'the7mk2'),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'desc',
            'options'   => [
                'asc'  => esc_html__('Ascending', 'the7mk2'),
                'desc' => esc_html__('Descending', 'the7mk2'),
            ],
            'condition' => [
                'post_type!' => 'current_query',
            ],
        ]);

        $this->add_control('orderby', [
            'label'     => esc_html__('Order By', 'the7mk2'),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'date',
            'options'   => [
                'date'          => esc_html__('Date', 'the7mk2'),
                'title'         => esc_html__('Name', 'the7mk2'),
                'ID'            => esc_html__('ID', 'the7mk2'),
                'modified'      => esc_html__('Modified', 'the7mk2'),
                'comment_count' => esc_html__('Comment count', 'the7mk2'),
                'menu_order'    => esc_html__('Menu order', 'the7mk2'),
                'rand'          => esc_html__('Rand', 'the7mk2'),
            ],
            'condition' => [
                'post_type!' => 'current_query',
            ],
        ]);


        $this->add_control('dis_posts_total', [
            'label'       => esc_html__('Total Number Of Posts', 'the7mk2'),
            'description' => esc_html__('Leave empty to display all posts.', 'the7mk2'),
            'type'        => Controls_Manager::NUMBER,
            'default'     => '12',
            'condition'   => [
                'post_type!' => 'current_query',
            ],
        ]);

        $this->add_control('posts_offset', [
            'label'       => esc_html__('Posts Offset', 'the7mk2'),
            'description' => esc_html__('Offset for posts query (i.e. 2 means, posts will be displayed starting from the third post).', 'the7mk2'),
            'type'        => Controls_Manager::NUMBER,
            'default'     => 0,
            'min'         => 0,
            'condition'   => [
                'post_type!' => 'current_query',
            ],
        ]);

        $this->end_controls_section();
    }

    /**
     * @return void
     */
    protected function add_products_query_content_controls()
    {
        $this->start_controls_section('product_query_section', [
            'label'     => esc_html__('Query', 'the7mk2'),
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'template_type' => ['products'],
                'wc_is_active'  => 'y',
            ],
        ]);

        $this->template(Query::class)->add_query_group_control();


        $this->add_control('product_dis_posts_total', [
            'label'       => esc_html__('Total Number Of Posts', 'the7mk2'),
            'description' => esc_html__('Leave empty to display all posts.', 'the7mk2'),
            'type'        => Controls_Manager::NUMBER,
            'default'     => '12',
            'condition'   => [
                'query_post_type!' => 'current_query',
            ],
        ]);

        $this->add_control('product_posts_offset', [
            'label'       => esc_html__('Posts Offset', 'the7mk2'),
            'description' => esc_html__('Offset for posts query (i.e. 2 means, posts will be displayed starting from the third post).', 'the7mk2'),
            'type'        => Controls_Manager::NUMBER,
            'default'     => 0,
            'min'         => 0,
            'condition'   => [
                'query_post_type!' => 'current_query',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render_slides()
    {
        /** @var \WP_Query $query */
        $query = $this->get_query();

        if ( ! $query->found_posts) {
            return;
        }

        $this->add_render_attribute('swiper_slide_inner_wrapper', 'class', 'the7-swiper-slide-inner');
        // It's the global `wp_query` it self. and the loop was started from the theme.
        if ($query->in_the_loop) {
            $this->render_post();
        } else {
            $is_product_template = $this->is_product_template();

            while ($query->have_posts()) {
                $query->the_post();

                if ($is_product_template) {
                    // Start loop.
                    global $product;
                    $product = wc_get_product(get_the_ID());
                }

                $this->render_post();
            }
        }

        wp_reset_postdata();
    }

    public function get_query()
    {
        return $this->_query;
    }

    /**
     * Render Post
     * Uses the chosen custom template to render Loop posts.
     */
    protected function render_post()
    {
        $settings = $this->get_settings_for_display();
        $loop_item_id = get_the_ID();

        $template_id_key = 'template_id';

        /** @var LoopDocument $document */
        $document = \Elementor\Plugin::$instance->documents->get($settings[ $template_id_key ]);

        // Bail if document is not an instance of LoopDocument.
        if ( ! $document instanceof LoopDocument) {
            return;
        }

        $this->remove_render_attribute('swiper_slide_wrapper');
        $this->add_render_attribute('swiper_slide_wrapper', 'class', [
            'post-id-' . $loop_item_id,
            'the7-swiper-slide',
        ]);

        ?>
        <div <?php echo $this->get_render_attribute_string('swiper_slide_wrapper'); ?>>
            <div <?php echo $this->get_render_attribute_string('swiper_slide_inner_wrapper'); ?>>
                <?php
                $this->print_dynamic_css($loop_item_id, $settings[ $template_id_key ]);

                $this->before_skin_render();

                The7_Elementor_Compatibility::instance()->print_document($document);
                $this->after_skin_render();
                ?>
            </div>
        </div>
        <?php
    }

    protected function print_dynamic_css($post_id, $post_id_for_data)
    {
        $document = Elementor::instance()->documents->get_doc_for_frontend($post_id_for_data);

        if ( ! $document) {
            return;
        }

        Elementor::instance()->documents->switch_to_document($document);

        $css_file = Loop_Dynamic_CSS::create($post_id, $post_id_for_data);
        $post_css = $css_file->get_content();

        if ( ! empty($post_css)) {
            $css = str_replace('.elementor-' . $post_id, '.e-loop-item-' . $post_id, $post_css);
            $css = sprintf('<style id="%s">%s</style>', 'loop-dynamic-' . $post_id_for_data, $css);

            echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        Elementor::instance()->documents->restore_document();
    }

    public function before_skin_render()
    {
        add_filter('elementor/document/wrapper_attributes', [$this, 'add_swiper_slide_class_to_loop_item'], 10, 2);
    }

    public function after_skin_render()
    {
        remove_filter('elementor/document/wrapper_attributes', [$this, 'add_swiper_slide_class_to_loop_item']);
    }

    public function add_swiper_slide_class_to_loop_item($attributes, $document)
    {
        if (LoopDocument::DOCUMENT_TYPE === $document::get_type()) {
            $attributes['class'] .= ' the7-slide-content';
        }

        return $attributes;
    }

    protected function get_slides_count()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings['template_id'])) {
            return 0;
        }

        return $this->get_query()->post_count;
    }

    /**
     * @return bool
     */
    protected function is_product_template()
    {
        return the7_is_woocommerce_enabled() && $this->get_settings_for_display('template_type') === 'products';
    }
}
