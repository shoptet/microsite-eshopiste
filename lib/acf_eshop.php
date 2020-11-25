<?php

add_action( 'acf/init', function () {

  if ( ! current_user_can( 'subscriber' ) ):

    acf_add_local_field_group(array(
      'key' => 'group_5fbe3ae73c396',
      'title' => 'Pokročilé nastavení (admin only)',
      'fields' => array(
        array(
          'key' => 'field_5fbe3b2726ed2',
          'label' => 'Skrýt v náhledu e-shopu',
          'name' => 'urlbox_hide_selector',
          'type' => 'text',
          'instructions' => 'Zadejte CSS selektor elementu, který má být skrytý při vytváření náhledu e-shopu. Více selektorů oddělte čárko. Např. <code>.popup,.popup-container</code>',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
          'maxlength' => '',
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'eshop',
          ),
        ),
      ),
      'menu_order' => 100,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
    ));
    
  endif;
    
} );