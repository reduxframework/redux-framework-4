<?php

if ( !defined ( 'ABSPATH' ) ) {
        exit;
}

if (!class_exists('Redux_Required')){
    
    class Redux_Required extends Redux_Class {
        
        /**
         * Checks dependencies between objects based on the $field['required'] array
         * If the array is set it needs to have exactly 3 entries.
         * The first entry describes which field should be monitored by the current field. eg: "content"
         * The second entry describes the comparison parameter. eg: "equals, not, is_larger, is_smaller ,contains"
         * The third entry describes the value that we are comparing against.
         * Example: if the required array is set to array('content','equals','Hello World'); then the current
         * field will only be displayed if the field with id "content" has exactly the value "Hello World"
         *
         * @param array $field
         *
         * @return array $params
         */
        public function check_dependencies( $field ) {
            $core = $this->core();
            
            if ( isset( $field['ajax_save'] ) && $field['ajax_save'] == false ) {
                $core->reload_fields[] = $field['id'];
            }

            if ( ! empty ( $field['required'] ) ) {
                if ( ! isset ( $core->required_child[ $field['id'] ] ) ) {
                    $core->required_child[ $field['id'] ] = array();
                }

                if ( ! isset ( $core->required[ $field['id'] ] ) ) {
                    $core->required[ $field['id'] ] = array();
                }

                if ( is_array( $field['required'][0] ) ) {

                    foreach ( $field['required'] as $value ) {
                        if ( is_array( $value ) && count( $value ) == 3 ) {
                            $data               = array();
                            $data['parent']     = $value[0];
                            $data['operation']  = $value[1];
                            $data['checkValue'] = $value[2];

                            $core->required[ $data['parent'] ][ $field['id'] ][] = $data;

                            if ( ! in_array( $data['parent'], $core->required_child[ $field['id'] ] ) ) {
                                $core->required_child[ $field['id'] ][] = $data;
                            }

                            $this->check_required_dependencies($core, $field, $data );
                        }
                    }
                } else {
                    $data               = array();
                    $data['parent']     = $field['required'][0];
                    $data['operation']  = $field['required'][1];
                    $data['checkValue'] = $field['required'][2];

                    $core->required[ $data['parent'] ][ $field['id'] ][] = $data;

                    if ( ! in_array( $data['parent'], $core->required_child[ $field['id'] ] ) ) {
                        $core->required_child[ $field['id'] ][] = $data;
                    }

                    $this->check_required_dependencies($core, $field, $data );
                }
            }
        }
        
        private function check_required_dependencies($core, $field, $data ) {
            //required field must not be hidden. otherwise hide this one by default

            if ( ! in_array( $data['parent'], $core->fieldsHidden ) && ( ! isset ( $core->folds[ $field['id'] ] ) || $core->folds[ $field['id'] ] != "hide" ) ) {
                if ( isset ( $core->options[ $data['parent'] ] ) ) {
                    $return = $this->compare_value_dependencies( $core->options[ $data['parent'] ], $data['checkValue'], $data['operation'] );
                }
            }

            if ( ( isset ( $return ) && $return ) && ( ! isset ( $core->folds[ $field['id'] ] ) || $core->folds[ $field['id'] ] != "hide" ) ) {
                $core->folds[ $field['id'] ] = "show";
            } else {
                $core->folds[ $field['id'] ] = "hide";
                
                if ( ! in_array( $field['id'], $core->fieldsHidden ) ) {
                    $core->fieldsHidden[] = $field['id'];
                }
            }
        }
        
        // Compare data for required field
        public function compare_value_dependencies( $parentValue, $checkValue, $operation ) {
            $return = false;
            
            switch ( $operation ) {
                case '=':
                case 'equals':
                    $data['operation'] = "=";

                    if ( is_array( $parentValue ) ) {
                        foreach ( $parentValue as $idx => $val ) {
                            if ( is_array( $checkValue ) ) {
                                foreach ( $checkValue as $i => $v ) {
                                    if ( Redux_Helpers::makeBoolStr($val) === Redux_Helpers::makeBoolStr($v) ) {
                                        $return = true;
                                    }
                                }
                            } else {
                                if ( Redux_Helpers::makeBoolStr($val) === Redux_Helpers::makeBoolStr($checkValue) ) {
                                    $return = true;
                                }
                            }
                        }
                    } else {
                        if ( is_array( $checkValue ) ) {
                            foreach ( $checkValue as $i => $v ) {
                                if ( Redux_Helpers::makeBoolStr($parentValue) === Redux_Helpers::makeBoolStr($v) ) {
                                    $return = true;
                                }
                            }
                        } else {
                            if ( Redux_Helpers::makeBoolStr($parentValue) === Redux_Helpers::makeBoolStr($checkValue) ) {
                                $return = true;
                            }
                        }
                    }
                    break;

                case '!=':
                case 'not':
                    $data['operation'] = "!==";
                    if ( is_array( $parentValue ) ) {
                        foreach ( $parentValue as $idx => $val ) {
                            if ( is_array( $checkValue ) ) {
                                foreach ( $checkValue as $i => $v ) {
                                    if ( Redux_Helpers::makeBoolStr($val) !== Redux_Helpers::makeBoolStr($v) ) {
                                        $return = true;
                                    }
                                }
                            } else {
                                if ( Redux_Helpers::makeBoolStr($val) !== Redux_Helpers::makeBoolStr($checkValue) ) {
                                    $return = true;
                                }
                            }
                        }
                    } else {
                        if ( is_array( $checkValue ) ) {
                            foreach ( $checkValue as $i => $v ) {
                                if ( Redux_Helpers::makeBoolStr($parentValue) !== Redux_Helpers::makeBoolStr($v) ) {
                                    $return = true;
                                }
                            }
                        } else {
                            if ( Redux_Helpers::makeBoolStr($parentValue) !== Redux_Helpers::makeBoolStr($checkValue) ) {
                                $return = true;
                            }
                        }
                    }

                    break;
                case '>':
                case 'greater':
                case 'is_larger':
                    $data['operation'] = ">";
                    if ( $parentValue > $checkValue ) {
                        $return = true;
                    }
                    break;
                case '>=':
                case 'greater_equal':
                case 'is_larger_equal':
                    $data['operation'] = ">=";
                    if ( $parentValue >= $checkValue ) {
                        $return = true;
                    }
                    break;
                case '<':
                case 'less':
                case 'is_smaller':
                    $data['operation'] = "<";
                    if ( $parentValue < $checkValue ) {
                        $return = true;
                    }
                    break;
                case '<=':
                case 'less_equal':
                case 'is_smaller_equal':
                    $data['operation'] = "<=";
                    if ( $parentValue <= $checkValue ) {
                        $return = true;
                    }
                    break;
                case 'contains':
                    if ( is_array( $parentValue ) ) {
                        $parentValue = implode( ',', $parentValue );
                    }

                    if ( is_array( $checkValue ) ) {
                        foreach ( $checkValue as $idx => $opt ) {
                            if ( strpos( $parentValue, (string) $opt ) !== false ) {
                                $return = true;
                            }
                        }
                    } else {
                        if ( strpos( $parentValue, (string) $checkValue ) !== false ) {
                            $return = true;
                        }
                    }

                    break;
                case 'doesnt_contain':
                case 'not_contain':
                    if ( is_array( $parentValue ) ) {
                        $parentValue = implode( ',', $parentValue );
                    }

                    if ( is_array( $checkValue ) ) {
                        foreach ( $checkValue as $idx => $opt ) {
                            if ( strpos( $parentValue, (string) $opt ) === false ) {
                                $return = true;
                            }
                        }
                    } else {
                        if ( strpos( $parentValue, (string) $checkValue ) === false ) {
                            $return = true;
                        }
                    }

                    break;
                case 'is_empty_or':
                    if ( empty ( $parentValue ) || $parentValue == $checkValue ) {
                        $return = true;
                    }
                    break;
                case 'not_empty_and':
                    if ( ! empty ( $parentValue ) && $parentValue != $checkValue ) {
                        $return = true;
                    }
                    break;
                case 'is_empty':
                case 'empty':
                case '!isset':
                    if ( empty ( $parentValue ) || $parentValue == "" || $parentValue == null ) {
                        $return = true;
                    }
                    break;
                case 'not_empty':
                case '!empty':
                case 'isset':
                    if ( ! empty ( $parentValue ) && $parentValue != "" && $parentValue != null ) {
                        $return = true;
                    }
                    break;
            }

            return $return;
        }
    }
}