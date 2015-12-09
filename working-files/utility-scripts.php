<?php
// Note that this file isn't included anywhere so these functions are not available
// unless you copy them out to where you're going to use them!.

// Pull mailing list data for SA, creates a CSV
function sa_pull_mailing_list() {
	global $wpdb;
	$user_ids = $wpdb->get_col( 'SELECT d.user_id FROM wp_bp_xprofile_data d WHERE d.field_id = 1382 AND d.value LIKE "%receive%"', 0 );
	$user_query = new BP_User_Query( array( 'user_ids' => $user_ids ) );
	// var_dump($user_query);

	// User Loop
	$i = 1;

	if ( ! empty( $user_query->results ) ) {
		// We want to exclude groups that aren't the base or SA group
		$profile_groups = bp_xprofile_get_groups();
		$profile_groups_ids = wp_list_pluck( $profile_groups, 'id' );
		$desired_groups = array( 1, 5 );
		$exclude_group_ids = array_diff( $profile_groups_ids, $desired_groups );

		$fp = fopen('sa-mailing-list.csv', 'a');

	    foreach ( $user_query->results as $user ) {
	        $profile = bp_xprofile_get_groups( array(
				'user_id'                => $user->ID,
				'fetch_fields'           => true,
				'fetch_field_data'       => true,
				'fetch_visibility_level' => true,
				'exclude_groups'         => $exclude_group_ids,
				'exclude_fields'         => array( 470 ),
				'update_meta_cache'      => true,
			) );
			// var_dump( $profile );
			// If this is the first result, we need to create the column header row.
			if ( 1 == $i ) {
				$row = array( 'user_email' );
				foreach ( $profile as $profile_group_obj ) {
					if ( strpos( $profile_group_obj->name, 'Salud' ) !== false ) {
						$is_salud_pfg = true;
					} else {
						$is_salud_pfg = false;
					}
					foreach ( $profile_group_obj->fields as $field ) {
						$towrite .= '"';
						if ( $is_salud_pfg ) {
							$row[] = 'SA: ' . $field->name;
						} else {
							$row[] = $field->name;
						}
					}
				}
				// Write the row.
				fputcsv($fp, $row);
			}

			// Write the email address
			$row = array( $user->user_email );
			// Record the user's data
			foreach ( $profile as $profile_group_obj ) {
				foreach ( $profile_group_obj->fields as $field ) {
					if ( 'public' == $field->visibility_level || 5 == $profile_group_obj->id ) {
						// Account for various field situations
						switch ( $field->id ) {
							case '1312':
								if ( ! empty( $field->data->value ) ) {
									$row[] = 'yes';
								} else {
									$row[] = '';
								}
								break;
							default:
								$value = maybe_unserialize( $field->data->value );
								if ( is_array( $value ) ) {
									$value = implode( ', ', $value );
								}

								$row[] = $value;
								break;
						}
					} elseif ( 1218 == $field->id ) {
						// Affiliation field
						$value = maybe_unserialize( $field->data->value );
						if ( is_array( $value ) ) {
							$value = implode( ', ', $value );
						}

						$row[] = $value;
					} else {
						// If this shouldn't be included, add an empty array member/placeholder.
						$row[] = '';
					}
				}
			}
			// Write the row.
			fputcsv($fp, $row);

			$i++;
	    }
		fclose($fp);
	}
}