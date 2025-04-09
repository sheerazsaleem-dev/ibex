<?php
/**
 * Custom taxonomy walker.
 *
 * @see \The7\Mods\Compatibility\Elementor\Widgets\Woocommerce\Product_Categories
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Walkers;

use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Custom_Taxonomy_List class.
 */
class Custom_Taxonomy_List extends \Walker {

	/**
	 * @var The7_Elementor_Widget_Base
	 */
	protected $widget;

	/**
	 * What the class handles.
	 *
	 * @var string
	 */
	public $tree_type;

	/**
	 * DB fields to use.
	 *
	 * @var array
	 */
	public $db_fields = [
		'parent' => 'parent',
		'id'     => 'term_id',
		'slug'   => 'slug',
	];

	/**
	 * @param  The7_Elementor_Widget_Base $widget Widget to operate.
     * @param  string                     $taxonomy Taxonomy to walk.
	 */
	public function __construct( The7_Elementor_Widget_Base $widget, $taxonomy = 'category' ) {
		$this->widget = $widget;
        $this->tree_type = $taxonomy;
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth Depth of category. Used for tab indentation.
	 * @param array  $args Will only append content if style argument value is 'list'.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth Depth of category. Used for tab indentation.
	 * @param array  $args Will only append content if style argument value is 'list'.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @param string  $output            Passed by reference. Used to append additional content.
	 * @param object  $cat               Category.
	 * @param int     $depth             Depth of category in reference to parents.
	 * @param array   $args              Arguments.
	 * @param integer $current_object_id Current object ID.
	 */
	public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$settings = $this->widget->get_settings_for_display();

		$sub_cat_act_icon = '';
		if ( isset($settings['selected_active_icon']) && $settings['selected_active_icon'] ) {
			$sub_cat_act_icon = $this->widget->get_elementor_icon_html(
				$settings['selected_active_icon']
			);
		}
		$sub_sub_cat_act_icon = $sub_cat_act_icon;
		if ( isset($settings['selected_sub_active_icon']) && $settings['selected_sub_active_icon'] && ! empty( $settings['selected_sub_active_icon']['value'] ) ) {
			$sub_sub_cat_act_icon = $this->widget->get_elementor_icon_html(
				$settings['selected_sub_active_icon']
			);
		}

		$sub_cat_icon = '';
		if ( isset($settings['selected_icon']) && $settings['selected_icon'] ) {
			$sub_cat_icon = $this->widget->get_elementor_icon_html(
				$settings['selected_icon']
			);
		}
		$sub_sub_cat_icon = $sub_cat_icon;
		if (  isset($settings['selected_sub_icon']) && $settings['selected_sub_icon'] && ! empty( $settings['selected_sub_icon']['value'] ) ) {
			$sub_sub_cat_icon = $this->widget->get_elementor_icon_html(
				$settings['selected_sub_icon']
			);
		}
		$sub_cat_count = '';
		if ( $args['show_count'] ) {
			$sub_cat_count = ' <span class="count">' . $cat->count . '</span>';
		}

		$cat_id  = (int) $cat->term_id;
		$output .= '<li class="cat-item cat-item-' . $cat_id;

		if ( $args['current_category'] === $cat_id ) {
			$output .= ' current-cat';
		}

		if ( $args['has_children'] && $args['hierarchical'] && ( empty( $args['max_depth'] ) || $args['max_depth'] > $depth + 1 ) ) {
			$output .= ' has-children';
		}

		if ( $args['current_category_ancestors'] && $args['current_category'] && in_array( $cat_id, $args['current_category_ancestors'], true ) ) {
			$output .= ' current-cat-parent';
		}

		$item_open_tag = '<a  class="cat-item-wrap" href="' . get_term_link( $cat_id, $this->tree_type ) . '">';
		$item_close_tag = '</a>';
		
		if ( isset($settings['taxonomy_links']) && $settings['taxonomy_links'] === '' ) {
			$item_open_tag = '<span class="cat-item-wrap">';
			$item_close_tag = '</span>';
		}

		if ( $depth < 1 ) {
			$output .= '"> ' . $item_open_tag . ' <span class="item-content">' . $this->get_term_name( $cat ) . '</span>' . $sub_cat_count . '<span class="next-level-button"> ' . $sub_cat_icon . $sub_cat_act_icon . '</span>' . $item_close_tag . '';
		} else {
			$output .= '">' . $item_open_tag . '<span class="item-content">' . $this->get_term_name( $cat ) . '</span>' . $sub_cat_count . '<span class="next-level-button"> ' . $sub_sub_cat_icon . $sub_sub_cat_act_icon . '</span></a>' . $item_close_tag . '';
		}
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $cat    Category.
	 * @param int    $depth  Depth of category. Not used.
	 * @param array  $args   Only uses 'list' for whether should append to output.
	 */
	public function end_el( &$output, $cat, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max.
	 * depth and no ignore elements under that depth. It is possible to set the.
	 * max depth to include all depths, see walk() method.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              Arguments.
	 * @param string $output            Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element || ( 0 === $element->count && ! empty( $args[0]['hide_empty'] ) ) ) {
			return;
		}
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
    
    /**
     * @param \WP_Term $term Term object.
     */
    protected function get_term_name( $term ) {
        return $term->name;
    }
}