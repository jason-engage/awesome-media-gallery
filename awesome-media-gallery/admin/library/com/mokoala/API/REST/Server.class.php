<?php


class MK_API_REST_Server
{
    
    protected $parameters;
    protected $method;
    protected $status;
    
    protected $response = array( 'status' => 'ok', 'status_code' => '200', 'status_message' => '', 'body' => array( ) );
    
    public function processRequest( $method, $parameters, MK_RecordUser $user = null )
    {
        if ( !empty( $parameters[ 'module' ] ) ) {
            if ( $method == 'post' ) {
                
            } else {
                try {
                    $module = MK_RecordModuleManager::getFromType( $parameters[ 'module' ] );
                    unset( $parameters[ 'module' ] );
                    
                    $expand = isset( $parameters[ 'expand' ] ) ? (boolean) $parameters[ 'expand' ] : true;
                    unset( $parameters[ 'expand' ] );
                    
                    $request = isset( $parameters[ 'request' ] ) ? (array) $parameters[ 'request' ] : null;
                    unset( $parameters[ 'request' ] );
					
					//needed
					$config = MK_Config::getInstance();
					if (file_exists('lang/'.$config->site->languages->language)) {
						include 'lang/'.$config->site->languages->language;
                    }
                    
                    // Insert record
                    if (
						( ( !empty( $user ) && $user->isAuthorized() ) && ( $module->getType() == 'image_comment' || $module->getType() == 'image_favourite' || $module->getType() == 'user_follower' || $module->getType() == 'image_comment_like') ) &&
						!empty( $parameters[ 'action' ] ) && !empty( $parameters[ 'fields' ] ) && $parameters[ 'action' ] == 'add'
					) {
						$new_record = MK_RecordManager::getNewRecord( $module->getId() );

                        foreach ( $parameters[ 'fields' ] as $field => $field_value ) {
							if ( get_magic_quotes_gpc() ) {
							   $field_value = strip_tags(stripslashes($field_value));
							}
                            $new_record->setMetaValue( $field, $field_value );
                        }
                        
                        $new_record->save();
						
						$new_rec_id = $new_record->getId();
						
	 					//needed
						include 'includes/functions.php';

                        $module_slug = $module->getSlug();
                                                               
                        switch ( $module_slug ) {
                            
                            case 'comments':
                                
                                $image_id     = $new_record->getImage();
                                $image_module = MK_RecordModuleManager::getFromType( 'image' );
                                $image        = MK_RecordManager::getFromId( $image_module->getId(), $image_id );
                                
                                $notification_text = '<a href="member.php?user=' . $user->getId() . '">' . $user->getDisplayName() . '</a> ' . $langscape["added"] . ' ' . $langscape["a new comment on"] . ' <a href="'. getImageTypeName($image->getTypeGallery()) . '/' . $image->getImageSlug() . '#comment-' . $new_rec_id . '">' . $image->getTitle() . '</a>';
                                
                                $user->addNotification( $notification_text, true, null, 'comment' );
                                
                                break;
                            
                            case 'favourites':
                                
                                $image_id     = $new_record->getImage();
                                $image_module = MK_RecordModuleManager::getFromType( 'image' );
                                $image        = MK_RecordManager::getFromId( $image_module->getId(), $image_id );
                                
                                $notification_text = '<a href="' . $user->getUsername() . '">' . $user->getDisplayName() . '</a> ' . $langscape["added"] . ' <a href="'. getImageTypeName($image->getTypeGallery()) . '/' . $image->getImageSlug() . '">' . $image->getTitle() . '</a> ' . $langscape["as a favorite"];
                               
                                $user->addNotification( $notification_text, true, null, 'like' );
                                
                                break;

                            case 'followers':
                                
                                
                                $member_id      = $new_record->getFollowing();
                                $member_module  = MK_RecordModuleManager::getFromType( 'user' );
                                $member         = MK_RecordManager::getFromId( $member_module->getId(), $member_id );
                                
                                $notification_text = '<a href="' . $user->getUsername() . '">' . $user->getDisplayName() . '</a> ' . $langscape["is now following"] . ' <a href="' . $member->getUsername() . '">' . $member->getDisplayName() . '</a>';
                               
                                $user->addNotification( $notification_text, true, null, 'follow' );
                                
                                break;
                                                            
                            case 'comments-likes':

                                $comment_id = $new_record->getId();
                                $image_id = $new_record->getImageid();
                                                                                
                                $image_module   = MK_RecordModuleManager::getFromType( 'image' );
                                $image          = MK_RecordManager::getFromId( $image_module->getId(), $image_id );
                                
                                $notification_text = '<a href="' . $user->getUsername() . '">' . $user->getDisplayName() . '</a> ' . $langscape["likes a"] . ' <a href="'. getImageTypeName($image->getTypeGallery()) . '/' . $image->getImageSlug() . '#comment-' . $comment_id . '">' . $langscape["comment"] . '</a> ' . $langscape["from"] . ' ' . $image->getTitle();
								
								
                                $user->addNotification( $notification_text, true, null, 'like' );
                                break;
                                                                
                        }
                        
                        $this->response[ 'body' ] = $new_record->toArray( $expand, $request );
                    } elseif ( !empty( $parameters[ 'id' ] ) && !empty( $user ) && $user->isAuthorized() && !empty( $parameters[ 'action' ] ) && !empty( $parameters[ 'fields' ] ) && $parameters[ 'action' ] == 'update' ) {
                       
					    $new_record = MK_RecordManager::getFromId( $module->getId(), $parameters[ 'id' ] );                    
 
                        $uid = $new_record->getId();
                        
                        if ( $new_record->canEdit( $user ) ) {
	                        	                        
                            foreach ( $parameters[ 'fields' ] as $field => $field_value ) {
                       
                                $new_record->setMetaValue( $field, strip_tags(stripslashes($field_value)) );
                                
                            }
                            
                            $new_record->save();
                            
	                        if ( isset($parameters[ 'fields' ]['approved']) ) {

	                            $user->sendApprovedEmail( $uid );
	                        
	                        }
                            
                        } else {
                            exit;
                        }
                        
                        $this->response[ 'body' ] = $new_record->toArray( $expand, $request );
                    }
                    // Either an ID or a list of IDs have been supplied
                        elseif ( !empty( $parameters[ 'id' ] ) && ( $id = $parameters[ 'id' ] ) ) {
                        
                        
                        // Perform an action on specified records
                        if ( !empty( $user ) && $user->isAuthorized() && !empty( $parameters[ 'action' ] ) && $parameters[ 'action' ] == 'delete' ) {
                            //echo 'AUTHORIZED';
                                                        
                            if ( is_array( $id ) ) // More than one record to delete
                                {
                                $record_list = array( );
                                foreach ( $id as $_id ) {
                                    $record = MK_RecordManager::getFromId( $module->getId(), $_id );
                                    if ( $record->canDelete( $user ) ) {
                                        $record->delete();
                                    }
                                }
                                
                            } else // Single record to delete
                                {
                                
                                $record = MK_RecordManager::getFromId( $module->getId(), $id );
                                
                                
                                if ( $record->canDelete( $user ) )
                                    {

                                    $record->delete();

                                } else {
                                    //echo 'User cannot delete!!';
                                }
                            }
                            
                            //exit();
                            
                        } elseif ( !empty( $parameters[ 'id' ] ) && !empty( $user ) && $user->isAuthorized() && !empty( $parameters[ 'action' ] ) && $parameters[ 'action' ] == 'report-image' ) {
                            //Report Image
                            $record = MK_RecordManager::getFromId( $module->getId(), $id );
                            $config = MK_Config::getInstance();
                            
                            $email = new MK_BrandedEmail();
                            $email
                            	->setSubject( $langscape["Media Item Reported"] )
                            	->setMessage( '<p>' . $langscape["The media item"] . ' <a href="'.MK_Utility::serverUrl( $image_page.'?image='.$image->getId() ).'">'.$image->getTitle().'</a> ' . $langscape["has been reported"] . '.</p>' )
                            	->send( $config->site->email );
                            
                            //return 'Success';
                                 
                            $this->response[ 'body' ] = $record->toArray( $expand, $request );
                            
                        }
                        
                        
                        
                        
                        // Return specified records
                        else {
                            if ( is_array( $id ) ) {
                                $record_list = array( );
                                foreach ( $id as $_id ) {
                                    $record        = MK_RecordManager::getFromId( $module->getId(), $_id );
                                    $record_list[] = $record->toArray( $expand, $request );
                                }
                                
                                $this->response[ 'body' ] = $record_list;
                            } else {
                                $record = MK_RecordManager::getFromId( $module->getId(), $id );
                                
                                $this->response[ 'body' ] = $record->toArray( $expand, $request );
                            }
                        }
                    } else {
                        
                        
                        $records   = array( );
                        $paginator = new MK_Paginator();
                        $paginator->setPage( !empty( $parameters[ 'page' ] ) ? $parameters[ 'page' ] : 1 )->setPerPage( !empty( $parameters[ 'per_page' ] ) ? $parameters[ 'per_page' ] : 10 );
                        
                        unset( $parameters[ 'page' ], $parameters[ 'per_page' ] );
                        
                        $search_parameters = array( );
                        
                        if ( !empty( $parameters[ 'keywords' ] ) ) {
                            $slug_field = $module->objectFieldSlug();
                            
                            $prepared_keywords = trim( $parameters[ 'keywords' ] );
                            $prepared_keywords = '%' . str_replace( ' ', '%', $prepared_keywords ) . '%';
                            $prepared_keywords = MK_Database::getInstance()->quote( $prepared_keywords );
                            
                            $search_parameters[] = array(
                                 'literal' => '`' . $slug_field->getName() . '` LIKE ' . $prepared_keywords 
                            );
                            
                            unset( $parameters[ 'keywords' ] );
                        }
                        
                        $options = array( );
                        if ( !empty( $parameters[ 'options' ] ) ) {
                            $options = $parameters[ 'options' ];
                            unset( $parameters[ 'options' ] );
                        }
                        
                        foreach ( $parameters as $parameter_key => $parameter_value ) {
                            $search_parameters[] = array(
                                 'field' => $parameter_key,
                                'value' => $parameter_value 
                            );
                        }
                        
                        if ( $search_parameters ) {
                            $module_records = $module->searchRecords( $search_parameters, $paginator, $options );
                        } else {
                            $module_records = $module->getRecords( $paginator, $options );
                        }
                        
                        foreach ( $module_records as $record ) {
                            $record_array = $record->toArray( $expand, $request );
                            
                            $text_indent = '';
                            
                            for ( $i = 0; $i < $record->getNestedLevel(); $i++ ) {
                                $text_indent .= '&nbsp;&nbsp;&nbsp;';
                            }
                            
                            $record_array[ 'text_indent' ] = $text_indent;
                            
                            $records[] = $record_array;
                        }
                        
                        $this->response[ 'body' ] = array(
                             'records' => $records,
                            'page' => $paginator->getPage(),
                            'per_page' => $paginator->getPerPage(),
                            'total_pages' => $paginator->getTotalPages(),
                            'total_records' => $paginator->getTotalRecords() 
                        );
                        
                    }
                    
                }
                catch ( Exception $e ) {
                    $this->response[ 'status_code' ] = '400';
                }
            }
        } else {
            $this->response[ 'status' ]      = 'error';
            $this->response[ 'status_code' ] = '400';
        }
        
        $this->response[ 'status_message' ] = MK_Request::getStatusCode( $this->response[ 'status_code' ] );
        
        return $this->response;
        
    }
    
}

?>