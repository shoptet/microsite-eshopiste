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
        array(
          'key' => 'field_605c5c1818d73',
          'label' => 'Zobrazit placeholder',
          'name' => 'show_placeholder',
          'type' => 'true_false',
          'instructions' => 'Zobrazit placeholder místo screenshotu e-shopu. Vhodné např. pokud se screenshot nevygeneruje nebo na doméně není možné udělat reprezentativní screenshot. Placeholder se může zobrazit automaticky, pokud nelze vytvořit screenshot.',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'message' => '',
          'default_value' => 0,
          'ui' => 0,
          'ui_on_text' => '',
          'ui_off_text' => '',
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