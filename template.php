<?php

/*
 * override theme_links__system_main_menu()
 */
function newepf_links__system_main_menu($variables) {
	$links = $variables['links'];
	global $language_url;
	$output = '';

	if (count($links) > 0) {
		$output = '';

		$output .= '<ul>';

		$num_links = count($links);
		$i = 1;

		foreach ($links as $key => $link) {
			$class = array (
				$key
			);

			if (isset ($link['href']) && ($link['href'] == $_GET['q'] ||  $link['href'] == arg(0)) && !drupal_is_front_page() && (empty ($link['language']) || $link['language']->language == $language_url->language)) {
				$class[] = 'active';
			}elseif(isset($link['below'])){
				foreach ($link['below'] as $keyb => $linkb) {
					if(isset ($linkb['href']) && ($linkb['href'] == $_GET['q'] ||  $linkb['href'] == arg(0)) && !drupal_is_front_page() && (empty ($linkb['language']) || $linkb['language']->language == $language_url->language)){
						$class[] = 'active';
					}
				}
			}
			$output .= '<li>';

			if (isset ($link['href'])) {
				// Pass in $link as $options, they share the same keys.
				$output .= '<a href="' . check_plain(url($link['href'])) . '"' . drupal_attributes(array (
					'class' => $class
				)) . '>' . $link['title'] . '</a>';
			}
			elseif (!empty ($link['title'])) {
				// Some links are actually not links, but we wrap these in <span> for adding title and class attributes.
				if (empty ($link['html'])) {
					$link['title'] = check_plain($link['title']);
				}
				$output .= '<span>' . $link['title'] . '</span>';
			}
			if (isset ($link['below'])) {
				$output .= '<ul>';
				foreach ($link['below'] as $keyb => $linkb) {
					$class = array (
						$keyb
					);
					$output .= '<li>';

					if (isset ($linkb['href'])) {
						// Pass in $link as $options, they share the same keys.
						$output .= '<a href="' . check_plain(url($linkb['href'])) . '"' . drupal_attributes(array (
							'class' => $class
						)) . '>' . $linkb['title'] . '</a>';
					}
					elseif (!empty ($linkb['title'])) {
						// Some links are actually not links, but we wrap these in <span> for adding title and class attributes.
						if (empty ($linkb['html'])) {
							$linkb['title'] = check_plain($linkb['title']);
						}
						$output .= $linkb['title'];
					}
					$output .= "</li>\n";
				}
				$output .= '</ul>';
			}

			$i++;
			$output .= "</li>\n";
		}

		$output .= '</ul>';
	}

	return $output;
}
/*
 * implements main_menu with hoot_preprocess_page()
 */
function newepf_preprocess_page(& $variables) {

	$menus = menu_tree_page_data('main-menu');
	//$router_item = menu_get_item();
	$links = array ();
	foreach ($menus as $item) {
		if (!$item['link']['hidden']) {
			$l = $item['link']['localized_options'];
			$l['href'] = $item['link']['href'];
			$l['title'] = $item['link']['title'];
			if ($item['link']['has_children'] > 0 && $item['below']) {
				foreach ($item['below'] as $itemb) {
					$lb = $itemb['link']['localized_options'];
					$lb['href'] = $itemb['link']['href'];
					$lb['title'] = $itemb['link']['title'];
					$l['below']['menu-' . $itemb['link']['mlid']] = $lb;
				}
			}
			// Keyed with the unique mlid to generate classes in theme_links().
			$links['menu-' . $item['link']['mlid']] = $l;
		}
	}
	$variables['main_menu'] = $links;
	
	if(arg(0)=='editorial'){
		$variables['add_links']=url('node/add/editorial');
	}else if(arg(0)=='news'){
		$variables['add_links']=url('node/add/news');
	}else if(arg(0)=='events'||arg(0)=='fevents'||arg(0)=='pastevents'){
		$variables['add_links']=url('node/add/event');
	}else if(arg(0)=='bookreview'){
		$variables['add_links']=url('node/add/bookreview');
	}else if(arg(0)=='papers'){
		$variables['add_links']=url('add/paper');
	}else if(arg(0)=='blog'){
		if($variables['action_links']){
		$variables['add_links']=url('node/add/blog');
		$variables['action_links']='';
		}
	}
}
/*
 * override theme_username(), add user picture info
 */
function newepf_username($variables) {

	if (variable_get('user_pictures', 0)) {
		$account = $variables['account'];
		if (!empty ($account->picture)) {
			// @TODO: Ideally this function would only be passed file objects, but
			// since there's a lot of legacy code that JOINs the {users} table to
			// {node} or {comments} and passes the results into this function if we
			// a numeric value in the picture field we'll assume it's a file id
			// and load it for them. Once we've got user_load_multiple() and
			// comment_load_multiple() functions the user module will be able to load
			// the picture files in mass during the object's load process.
			if (is_numeric($account->picture)) {
				$account->picture = file_load($account->picture);
			}
			if (!empty ($account->picture->uri)) {
				$filepath = $account->picture->uri;
			}
		}
		elseif (variable_get('user_picture_default', '')) {
			$filepath = variable_get('user_picture_default', '');
		}
		if (isset ($filepath)) {
			$alt = t("@user's picture", array (
				'@user' => format_username($account)
			));
			// If the image does not have a valid Drupal scheme (for eg. HTTP),
			// don't load image styles.
				$variables['user_picture'] = theme('image', array (
					'path' => $filepath,
					'alt' => $alt,
					'title' => $alt,
				    'height'=>isset($variables['picture-size'])?$variables['picture-size']:8
				));
			if (!empty ($account->uid) && user_access('access user profiles')) {
				$attributes = array (
					'attributes' => array (
						'title' => t('View user profile.'),
						'class'=>array(t('user_icon'))
					),
					'html' => TRUE,
					
				);
				$variables['user_picture'] = l($variables['user_picture'], "user/$account->uid", $attributes);
			}
		}
	}
	$output='';
	if (isset ($variables['link_path'])) {
		// We have a link path, so we should generate a link using l().
		// Additional classes may be added as array elements like
		// $variables['link_options']['attributes']['class'][] = 'myclass';
		if(isset($variables['user_picture'])){
			$output= $variables['user_picture'];
		}
		$output .= l($variables['name'] . $variables['extra'], $variables['link_path'], $variables['link_options']);
	} else {
		// Modules may have added important attributes so they must be included
		// in the output. Additional classes may be added as array elements like
		// $variables['attributes_array']['class'][] = 'myclass';
		$output = '<span' . drupal_attributes($variables['attributes_array']) . '>' . $variables['name'] . $variables['extra'] . '</span>';
	}
	return $output;
}
/*
 * implements main_menu with hoot_preprocess_comment()
 */
function newepf_preprocess_comment(&$variables) {

  $variables['submitted'] = t('!username said on !datetime: ', array('!username' => $variables['author'], '!datetime' => $variables['created']));
}

/*
 * implements main_menu with hoot_preprocess_node()
 */
function newepf_preprocess_node(&$variables) {
  $node = $variables['node'];
  list(, , $bundle) = entity_extract_ids('node', $node);
  $output='';
  
 // $variables['ins']=$variables['content']['links'];
    foreach (field_info_instances('node', $bundle) as $instance) {
    	if($instance['field_name']=='authorname'){
    		if(!empty($node->{$instance['field_name']})){
    			$variables['paper_authors']=$node->{$instance['field_name']}['und'][0]['value'];
    		}
    	}
    	if($instance['field_name']=='field_reviewer'){
    		if(!empty($node->{$instance['field_name']})){
    		$variables['reviewer']=$node->{$instance['field_name']}['und'][0]['value'];
    		}
    	}
    	if($instance['field_name']=='datebegin'){
    		if(!empty($node->{$instance['field_name']})){
    		$variables['datebegin']=substr($node->{$instance['field_name']}['und'][0]['value'],0,10);
    		}
    	}
    	if($instance['field_name']=='dateend'){
    		if(!empty($node->{$instance['field_name']})){
    		$variables['dateend']=substr($node->{$instance['field_name']}['und'][0]['value'],0,10);
    		}
    	}
    	if($instance['field_name']=='city'){
    		if(!empty($node->{$instance['field_name']})){
    		$variables['city']=$node->{$instance['field_name']}['und'][0]['value'];
    		}
    	}
    	if($instance['field_name']=='website'){
    		if(!empty($node->{$instance['field_name']})){
    		$variables['website']=$node->{$instance['field_name']}['und'][0]['value'];
    		}
    	}
    			
    	if($instance['widget']['module']=='arxiv'){
    		if(!empty($node->{$instance['field_name']})){
    			$variables['paper_authors']=$node->{$instance['field_name']}['und'][0]['authors'];
    		}   		
    		
    		if (!empty($node->{$instance['field_name']})&&$node->{$instance['field_name']}['und'][0]['pdfUrl'] != '') {
					$output .= '<a class="pdf_icon" href="'.url('/paper/download/'.$node->nid.'/pdf').'" >pdf </a>';
				}
				if (!empty($node->{$instance['field_name']})&&$node->{$instance['field_name']}['und'][0]['psUrl'] != '') {
					$output .= '<a class="note_icon" href="'.url('/paper/download/'.$node->nid.'/ps').'" >ps </a>';
				}
				if (!empty($node->{$instance['field_name']})&&$node->{$instance['field_name']}['und'][0]['otherUrl'] != '') {
					$output .= '<a class="note_icon" href="'.url('/paper/download/'.$node->nid.'/other').'" >other </a>';
				}
    	}
    	if($instance['field_name']=='field_author'){
    		if(!empty($node->{$instance['field_name']})){
    			$variables['paper_authors']=$node->{$instance['field_name']}['und'][0]['value'];
    		}
    	}
    	if($node->type=='editorial'){
    		$variables['editorial']=true;
    	}
    	if($instance['field_name']=='field_upload'){
    		if (!empty($node->{$instance['field_name']})&&$node->{$instance['field_name']}['und'][0]['filename'] != '') {
    			$url = file_create_url($node->{$instance['field_name']}['und'][0]['uri']);
					$output .= '<a class="note_icon" href="'.url('/paper/download/'.$node->nid).'" >download </a>';
				}
    	}
    	if($instance['field_name']=='field_prefix'){
    		if(!empty($node->{$instance['field_name']})){
    		$variables['news_prefix']=$node->{$instance['field_name']}['und'][0]['value'];
    		}
    	}
  }
  if(substr($node->type,0,5)=='paper'){
  $statistics = statistics_get($node->nid);
  $obj = db_select('arxiv_downNo', 'm')->fields('m', array ('downloadNo'))
  	       ->condition('m.nid', $node->nid)->execute()->fetchAll();
   if($obj){
   	$variables['download']=  $output.' ('.(empty($statistics['totalcount'])?0:$statistics['totalcount']).' views, '.$obj[0]->downloadNo.' download, '.$node->comment_count.' comments)';
   }else if(isset($node->comment_count)){
   	 $variables['download']=  $output.' ('.(empty($statistics['totalcount'])?0:$statistics['totalcount']).' views, 0 download, '.$node->comment_count.' comments)';
   }else{
   	 $variables['download']=  $output.' ('.(empty($statistics['totalcount'])?0:$statistics['totalcount']).' views, 0 download, 0 comments)';
   }
  }
  if($variables['page']){
  	 $variables['submitted'] = t('posted on !datetime', array( '!datetime' => $variables['date']));
  }else{
  	 $variables['submitted'] = t('posted by !username, !datetime', array('!username' => $variables['name'], '!datetime' => $variables['date']));
  }
 

}

function newepf_preprocess_plus1_widget(&$variables) {
	if (!$variables['logged_in'] && !$variables['can_vote']) {
    $variables['widget_message'] =  l(t('vote'), 'user', array('html' => TRUE));
  }
}

/**
 * Alters link url in calendar events block in order to filter events at /events
 *
 * @see template_preprocess_calendar_datebox()
 */
function newepf_preprocess_calendar_datebox(&$vars) {
  $date = $vars['date'];
  $view = $vars['view'];
  $day_path = calendar_granularity_path($view, 'day');
  $month_path = calendar_granularity_path($view, 'month');
  
 // $vars['url'] = str_replace(array($month_path, $year_path), $day_path, date_pager_url($view, NULL, $date, $force_view_url));
  
  $vars['url'] = 'events/' . $date;
  $vars['link'] = !empty($day_path) ? l($vars['day'], $vars['url'],array('html' => TRUE)) : $vars['day'];
}

/**
 * Alters link url for month in calendar events block in order to filter events at /events
 * 
 * @see theme_date_nav_title
 */
function newepf_date_nav_title($params) {
  $granularity = $params['granularity'];
  $view = $params['view'];
  $date_info = $view->date_info;
  $link = !empty($params['link']) ? $params['link'] : FALSE;
  $format = !empty($params['format']) ? $params['format'] : NULL;
  switch ($granularity) {
    case 'month':
      $format = !empty($format) ? $format : (empty($date_info->mini) ? 'F Y' : 'F');
      $title = date_format_date($date_info->min_date, 'custom', $format);
      $date_arg = $date_info->year . '-' . date_pad($date_info->month);
      break;
  }
  if (!empty($date_info->mini) || $link) {
    // Month navigation titles are used as links in the mini view.
    $attributes = array('title' => t('View full page month'));
    $url = 'events/'.$date_arg;//date_pager_url($view, $granularity, $date_arg, TRUE);
    return l($title, $url, array('attributes' => $attributes,'html' => TRUE));
  }
  else {
    return $title;
  }
}

function newepf_more_link($variables) {
  return '<div class="more-link">' . l(t('Show more'), $variables['url'], array('attributes' => array('title' => $variables['title']))) . '</div>';
}

/**
 * Implements hook_form_alter().
 */
function newepf_form_alter(&$form, &$form_state, $form_id) {

  // Filter the form_id value to identify all the custom blocks
  $form_id_processed = $form_id;
  $delta = '';
  for ($a = 1 ; $a <= variable_get('custom_search_blocks_number', 1) ; $a++) {
    if ($form_id == 'custom_search_blocks_form_' . $a) {
      $form_id_processed = 'custom_search_blocks_form';
      $delta = 'blocks_' . $a . '_';
    }
  }

  switch ($form_id_processed) {
    case 'search_theme_form':
    case 'search_block_form':
    case 'custom_search_blocks_form':

      if (user_access('use custom search')) {
        // Title.
        $form[$form_id]['#title'] = variable_get('custom_search_' . $delta . 'label', CUSTOM_SEARCH_LABEL_DEFAULT);
        $form[$form_id]['#title_display'] = (!variable_get('custom_search_' . $delta . 'label_visibility', FALSE)) ? 'invisible' : 'before' ;

        // Search box.
        $form[$form_id]['#default_value'] = variable_get('custom_search_' . $delta . 'text', '');
        $form[$form_id]['#weight'] = variable_get('custom_search_' . $delta . 'search_box_weight', 0);
        $form[$form_id]['#attributes'] = array('class' => array('custom-search-default-value', 'custom-search-box'));
        $form[$form_id]['#size'] = variable_get('custom_search_' . $delta . 'size', CUSTOM_SEARCH_SIZE_DEFAULT);
        $form[$form_id]['#maxlength'] = variable_get('custom_search_' . $delta . 'max_length', CUSTOM_SEARCH_MAX_LENGTH_DEFAULT);

        // Default text.
        $form['default_text'] = array(
          '#type'           => 'hidden',
          '#default_value'  => variable_get('custom_search_' . $delta . 'text', ''),
          '#attributes'     => array('class' => array('default-text')),
        );

        // CSS
        drupal_add_css(drupal_get_path('module', 'custom_search') . '/custom_search.css');

        // Criteria
        $criteria = array('or' => 6, 'phrase' => 7, 'negative' => 8);
        foreach ($criteria as $c => $w) {
          if (variable_get('custom_search_' . $delta . 'criteria_' . $c . '_display', FALSE)) {
            $form['custom_search_criteria_' . $c] = array(
              '#type'       => 'textfield',
              '#title'      => variable_get('custom_search_' . $delta . 'criteria_' . $c . '_label', constant('CUSTOM_SEARCH_CRITERIA_' . strtoupper($c) . '_LABEL_DEFAULT')),
              '#size'       => 15,
              '#maxlength'  => 255,
              '#weight'     => variable_get('custom_search_' . $delta . 'criteria_' . $c . '_weight', $w),
            );
          }
        }

        // Content type & other searches.
        // Content types.
        $toptions = array();
        $types = array_keys(array_filter(variable_get('custom_search_' . $delta . 'node_types', array())));
        if (count($types)) {
          $names = node_type_get_names();
          if (count($types) > 1 || variable_get('custom_search_' . $delta . 'any_force', FALSE)) $toptions['c-all'] = variable_get('custom_search_' . $delta . 'type_selector_all', CUSTOM_SEARCH_ALL_TEXT_DEFAULT);
          foreach ($types as $type) {
            if($type!='paper_from_arxiv'){
            	$toptions['c-' . $type] = $names[$type];
            }
          }
        }
        $options = array();
        // Other searches.
        $others = array_keys(array_filter(variable_get('custom_search_' . $delta . 'other', array())));
        // If content types and other searches are combined, make an optgroup.
        if (count($others) && count($toptions) && variable_get('custom_search_' . $delta . 'type_selector', 'select') == 'select') {
          $content = module_invoke('node', 'search_info');
          $options[$content['title']] = $toptions;
        }
        else {
          $options = $toptions;
        }
        foreach (module_implements('search_info') as $module) {
          if ($module != 'node' && $name = module_invoke($module, 'search_info')) {
            if (in_array($module, $others)) $options['o-' . $module] = $name['title'];
          }
        }
        if (count($options)) {
          $selector_type = variable_get('custom_search_' . $delta . 'type_selector', 'select');
          if ($selector_type == 'selectmultiple') {
            $selector_type = 'select';
            $multiple = TRUE;
          }
          else $multiple = FALSE;
          $form['custom_search_types'] = array(
            '#type'           => $selector_type,
            '#multiple'       => $multiple,
            '#title'          => variable_get('custom_search_' . $delta . 'type_selector_label', CUSTOM_SEARCH_TYPE_SELECTOR_LABEL_DEFAULT),
            '#options'        => $options,
            '#default_value'  => ((variable_get('custom_search_' . $delta . 'type_selector', 'select') == 'checkboxes') ? array('c-all') : 'c-all'),
            '#attributes'     => array('class' => array('custom-search-selector', 'custom-search-types')),
            '#weight'         => variable_get('custom_search_' . $delta . 'content_types_weight', 1),
            '#validated'      => TRUE,
          );

          // If there's only one type, hide the selector
          if (count($others) + count($types) == 1 && !variable_get('custom_search_' . $delta . 'any_force', FALSE)) {
            $form['custom_search_types']['#type'] = 'hidden';
            $form['custom_search_types']['#default_value'] = key(array_slice($options, count($options)-1));
          }

          if (!variable_get('custom_search_' . $delta . 'type_selector_label_visibility', TRUE)) $form['custom_search_types']['#title_display'] = 'invisible';
        }

        // Custom paths
        if (variable_get('custom_search_' . $delta . 'paths', '') != '') {
          $options = array();
          $lines = explode("\n", variable_get('custom_search_' . $delta . 'paths', ''));
          foreach ($lines as $line) {
            $temp = explode('|', $line);
            $options[$temp[0]] = (count($temp) >= 2) ? t($temp[1]) : '';
          }
          if (count($options) == 1) {
            $form['custom_search_paths'] = array(
              '#type'           => 'hidden',
              '#default_value'  => key($options),
            );
          }
          else {
            $form['custom_search_paths'] = array(
              '#type'           => variable_get('custom_search_' . $delta . 'paths_selector', 'select'),
              '#title'          => variable_get('custom_search_' . $delta . 'paths_selector_label', CUSTOM_SEARCH_PATHS_SELECTOR_LABEL_DEFAULT),
              '#options'        => $options,
              '#default_value'  => key($options),
              '#weight'         => variable_get('custom_search_' . $delta . 'custom_paths_weight', 9),
            );
            if (!variable_get('custom_search_' . $delta . 'paths_selector_label_visibility', TRUE)) $form['custom_search_paths']['#title_display'] = 'invisible';
          }
        }

        // Submit button.
        $form['actions']['submit']['#value'] = variable_get('custom_search_' . $delta . 'submit_text', CUSTOM_SEARCH_SUBMIT_TEXT_DEFAULT);

        if (variable_get('custom_search_' . $delta . 'image_path', '') != '') {
          $form['actions']['submit']['#type'] = 'image_button';
          $form['actions']['submit']['#src'] = variable_get('custom_search_' . $delta . 'image_path', '');
          $form['actions']['submit']['#name'] = 'op';
          $form['actions']['submit']['#attributes'] = array('alt' => array(variable_get('custom_search_' . $delta . 'submit_text', CUSTOM_SEARCH_SUBMIT_TEXT_DEFAULT)), 'class' => array('custom-search-button'));
        }
        elseif ($form['actions']['submit']['#value'] == '') $form['actions']['submit']['#attributes'] = array('style' => 'display:none;');

        $form['actions']['#weight'] = variable_get('custom_search_' . $delta . 'submit_button_weight', 3);

        // Popup
        $form['popup'] = array(
          '#type'       => 'fieldset',
          '#weight'     => 1 + variable_get('custom_search_' . $delta . 'search_box_weight', 0),
          '#attributes' => array('class' => array('custom_search-popup')),
        );
        if (!empty($form['custom_search_types']) && variable_get('custom_search_' . $delta . 'content_types_region', 'block') == 'popup') {
          $form['popup']['custom_search_types'] = $form['custom_search_types'];
          unset($form['custom_search_types']);
        }
        if (!empty($form['custom_search_paths']) && variable_get('custom_search_' . $delta . 'custom_paths_region', 'block') == 'popup') {
          $form['popup']['custom_search_paths'] = $form['custom_search_paths'];
          unset($form['custom_search_paths']);
        }
        foreach ($criteria as $c => $w) {
          if (variable_get('custom_search_' . $delta . 'criteria_' . $c . '_display', FALSE) && variable_get('custom_search_' . $delta . 'criteria_' . $c . '_region', 'block') == 'popup') {
            $form['popup']['custom_search_criteria_' . $c] = $form['custom_search_criteria_' . $c];
            unset($form['custom_search_criteria_' . $c]);
          }
        }

        // Form attributes
        $form['#attributes']['class'] = array('search-form');
        $form['#submit'][] = 'epf_custom_search_submit';
      }

    break;

  }

}

/**
 * Alter the search to respect the search modes selected.
 */
function epf_custom_search_submit($form, &$form_state) {
  $delta = (isset($form_state['values']['delta'])) ? 'blocks_' . $form_state['values']['delta'] . '_' : '' ;
  variable_set('custom_search_delta', $delta); // save for later use (exclusion & refresh)
  $type = 'node';
  $keys = $form_state['values'][$form_state['values']['form_id']];

  $types = (isset($form_state['values']['custom_search_types'])) ? $form_state['values']['custom_search_types'] : array();
  if($types=='c-paper'){
  	$types=array('c-paper','c-paper_from_arxiv');
  }
  if (!is_array($types)) $types = array($types);
  $types = array_map('_custom_search_filter_keys', array_filter($types));

  if (module_exists('taxonomy')) {
    $terms = array();
    $vocabularies = taxonomy_get_vocabularies();
    foreach ($vocabularies as $voc) {
      if (isset($form_state['values']['custom_search_vocabulary_' . $voc->vid])) {
        $vterms = $form_state['values']['custom_search_vocabulary_' . $voc->vid];
        if (!is_array($vterms)) $vterms = array($vterms);
        $terms = array_merge($terms, $vterms);
      }
    }
    $terms = array_map('_custom_search_filter_keys', array_values(array_filter($terms)));
    // if one or more -Any- is selected, delete them
    while (($index = array_search('all', $terms)) !== FALSE) array_splice($terms, $index, 1);
  }

  $search_types = module_implements('search_info');
  if (in_array(current($types), $search_types)) $type = current($types);
  else {
    if (isset($form_state['values']['custom_search_criteria_or']) && trim($form_state['values']['custom_search_criteria_or']) != '') $keys .= ' ' . str_replace(' ', ' OR ', trim($form_state['values']['custom_search_criteria_or']));
    if (isset($form_state['values']['custom_search_criteria_negative']) && trim($form_state['values']['custom_search_criteria_negative']) != '') $keys .= ' -' . str_replace(' ', ' -', trim($form_state['values']['custom_search_criteria_negative']));
    if (isset($form_state['values']['custom_search_criteria_phrase']) && trim($form_state['values']['custom_search_criteria_phrase']) != '') $keys .= ' "' . trim($form_state['values']['custom_search_criteria_phrase']) . '"';
    $original_keywords = $keys;
    if (count($types)) {
      // If a content type is selected, and it's not -Any-, search for that type.
      if (!in_array('all', $types)) $keys = search_expression_insert($keys, 'type', implode(',', $types));
      // If -Any- is selected and -Any- is set to restrict the search, grab the content types.
      elseif (variable_get('custom_search_' . $delta . 'any_restricts', FALSE)) {
        $restricted_types = array_keys(array_filter(variable_get('custom_search_' . $delta . 'node_types', array())));
        $keys = search_expression_insert($keys, 'type', implode(',', $restricted_types));
      }
    }
    if (module_exists('taxonomy') && count($terms)) {
      $keys = search_expression_insert($keys, 'term', implode(',', $terms));
    }
    if (module_exists('custom_search_i18n')) {
      if (variable_get('custom_search_i18n_' . $delta . 'search_language', 'all') == 'current') {
        $keys = search_expression_insert($keys, 'language', i18n_language()->language);
      }
    }
  }
  $search_path = array(
    'path'  => 'search/' . $type . '/' . $keys,
    'query' => array(),
  );

  // Integrates other search modules
  if (module_exists('apachesolr_search')) {
    $search_path = _custom_search_apachesolr_search(array(
      'keywords'  => $original_keywords,
      'types'     => $types,
      'terms'     => (!empty($terms)) ? $terms : array(),
    ));
  }
  elseif (module_exists('google_appliance')) {
    $search_path = _custom_search_google_appliance_search(array(
      'keys'  => $keys,
    ));
  }
  elseif (module_exists('luceneapi_node') && variable_get('luceneapi:default_search', 0)) {
    $search_path = _custom_search_lucenapi_search(array(
      'keywords'  => $original_keywords,
      'types'     => $types,
      'terms'     => (!empty($terms)) ? $terms : array(),
    ));
  }
  elseif (module_exists('search_api_page')) {
    $search_api_page = search_api_page_load(variable_get('custom_search_' . $delta . 'search_api_page', 0));
    if ($search_api_page) {
      $search_path = _custom_search_search_api_search(array(
        'keywords'  => $original_keywords,
        'types'     => $types,
        'terms'     => (!empty($terms)) ? $terms : array(),
        'page'      => $search_api_page,
      ));
    }
  }

  // Build a custom path if needed
  if (isset($form_state['values']['custom_search_paths']) && $form_state['values']['custom_search_paths'] != '') {
    $custom_path = str_replace('[key]', $form_state['values'][$form_state['values']['form_id']], $form_state['values']['custom_search_paths']);
    if (strpos($form_state['values']['custom_search_paths'], '[terms]') !== FALSE) $custom_path = str_replace('[terms]', (count($terms)) ? implode($form_state['values']['custom_search_paths_terms_separator'], $terms) : '', $custom_path);
    // Check for a query string
    $custom_path_query_position = strpos($custom_path, '?');
    $custom_path_query = array();
    if ($custom_path_query_position !== FALSE) {
      $custom_path_query_tmp = substr($custom_path, 1 + $custom_path_query_position);
      $custom_path_query_tmp = str_replace('&amp;', '&', $custom_path_query_tmp);
      $custom_path_query_tmp = explode('&', $custom_path_query_tmp);
      foreach ($custom_path_query_tmp as $param) {
        $param_exploded = explode('=', $param);
        $custom_path_query[$param_exploded[0]] = $param_exploded[1];
      }
      $custom_path = substr($custom_path, 0, $custom_path_query_position);
    }
    // Check for external path. If not, add base path
    if (drupal_substr($custom_path, 0, 4) != 'http') $custom_path = url($custom_path, array('absolute' => TRUE));
    // Send the final url
    $form_state['redirect'] = url($custom_path, array('query' => $custom_path_query, 'absolute' => TRUE));
  }
  else $form_state['redirect'] = url($search_path['path'], array('query' => $search_path['query'], 'absolute' => TRUE));

}
