<?php
/*
 *                          IE - Text Input 
 *                         IED - Text Input Dynamic
 *                          IS - Radio box
 *                          SL - Select element 
 *    Input element Select SLD - Date format 
 *                         SLZ - Time Zone 
 *                         SLZN - Time Zone List
 *                         SLT - TFTP Lang
 *                         SLM - Music on hold 
 *                         SLK - System KeySet
 *  * Input element Select SLS - System Language 
 *    Input element Select SDM - Model List 
 *                         SDE - Extension List 
 */

$npref = $form_prefix.'_';
$napref = $form_prefix.'-ar_';
if (empty($form_prefix)){
    $npref = "sccp_";
    $napref ="sccp-ar_";
}
$day_format = array("D.M.Y", "D.M.YA", "Y.M.D", "YA.M.D", "M-D-Y", "M-D-YA", "D-M-Y", "D-M-YA", "Y-M-D", "YA-M-D", "M/D/Y", "M/D/YA",
        "D/M/Y", "D/M/YA", "Y/M/D", "YA/M/D", "M/D/Y", "M/D/YA");
$mysql_table = array("sccpdevice","sccpdeviceconfig");
$time_zone_name = timezone_identifiers_list();

$time_zone = array("-12", "-11", "-10", "-09", "-08", "-07", "-06", "-05", "-04", "-03", "-02", "-01", "00",
                   "01","02","03","04","05","06","07","08","09","10","11","12");
if (\FreePBX::Modules()->checkStatus("soundlang")) {
    $syslangs = \FreePBX::Soundlang()->getLanguages();
    if (!is_array($syslangs)) {
        $syslangs = array();
    }
}
if (function_exists('music_list')){
    $moh_list = music_list();
//    $cur = (isset($mohsilence) && $mohsilence != "" ? $mohsilence : 'default');
}
if (!is_array($moh_list)){
    $moh_list = array('default');
}
$sofkey_list = \FreePBX::Sccp_manager()-> sccp_list_keysets();
$model_list = \FreePBX::Sccp_manager()->get_db_SccpTableData("HWDevice");
$extension_list = \FreePBX::Sccp_manager()->get_db_SccpTableData("HWextension");

$extension_list[]=array(model=>'none', vendor=>'CISCO', dns=>'0');

$items = $itm -> children();
//print_r($sofkey_list);
//print_r($syslangs);
//print_r($moh_list);
if ($h_show==1) {
 ?>

 <div class="section-title" data-for="<?php echo $npref.$itm['name'];?>">
    <h3><i class="fa fa-minus"></i><?php echo _($items ->label) ?></h3>
 </div>
 <div class="section" data-id="<?php echo $npref.$itm['name'];?>">

<?php
}
foreach ($items as $child) {
    
    if ($child['type'] == 'IE') {
        $res_input = '';
        $res_name = '';
        $res_id = $npref.$child->input[0]->name;
        if (empty($child->nameseparator)) {
            $child->nameseparator = ' / ';
        }
        $i = 0;
 
        echo '<!-- Begin '.$child->label.' -->';
                
        ?>
        <div class="element-container">
            <div class="row"> <div class="form-group"> 
                    <div class="col-md-3">
                        <label class="control-label" for="<?php echo $res_id; ?>"><?php echo _($child->label);?></label>
                        <i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $res_id; ?>"></i>
                    </div>
                    <div class="col-md-9">
                        <?php
                        
                        foreach ($child->xpath('input') as $value) {
                                $res_n =  (string)$value->name;
                                $res_name = $npref . $res_n;
                                if (empty($res_id)) {
                                  $res_id = $res_name;
                                }

                                if (!empty($fvalues[$res_n])) {
                                    if (!empty($fvalues[$res_n]['data'])) {
                                        $value->value = $fvalues[$res_n]['data'];
                                    }
                                }

                                if (empty($value->value)) {
                                    $value->value = $value->default;
                                }
                                if (empty($value->type)) {
                                    $value->type = 'text';
                                }
                                if (empty($value->class)) {
                                    $value->class = 'form-control';
                                }
                                if ($i > 0) echo $child->nameseparator;
//
                            echo '<input type="' . $value->type . '" class="' . $value->class . '" id="' . $res_id . '" name="' . $res_name . '" value="' . $value->value.'"';
                            if (isset($value->options)){
                                foreach ($value->options ->attributes() as $optkey =>$optval){
                                    echo  ' '.$optkey.'="'.$optval.'"';
                                }
                            }
                            if (!empty($value->min)) echo  ' min="'.$value->min.'"';
                            if (!empty($value->max)) echo  ' max="'.$value->max.'"';
                            echo  '>';
                            $i ++;

                        }
                        ?>
                    </div>
                </div></div>
            <div class="row"><div class="col-md-12">
                    <span id="<?php echo $res_id;?>-help" class="help-block fpbx-help-block"><?php echo _($child->help);?></span>
                </div></div>
        </div>

        <?php
        echo '<!-- END '.$child->label.' -->';
        
    }
    if ($child['type'] == 'IED') {
        $res_input = '';
        $res_name = '';
        $res_n =  (string)$child->name;

//        $res_value
        $lnhtm = '';
        $res_id = $napref.$child->name;
        $i = 0;

        if (!empty($fvalues[$res_n])) {
            if (!empty($fvalues[$res_n]['data'])) {
                $res_value = explode(';', $fvalues[$res_n]['data']);
            }
        }
        if (empty($res_value)) {
            $res_value = array((string) $child->default);
//            $res_value = explode('/', (string) $child->default);
        }
        
        echo '<!-- Begin '.$child->label.' -->';
        ?>
     	<!--Local Networks-->
	<div class="element-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label" for="<?php echo $res_id; ?>"><?php echo _($child->label);?></label>
                                <i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $res_id; ?>"></i>
                            </div>
                            <div class="col-md-9"><div class = "col-sm-7">
                            <?php
                            foreach ($res_value as $dat_v) {
                            ?>
                                <div class = "<?php echo $res_id;?> form-group form-inline" data-nextid=<?php echo $i+1;?> > 
                            <?php
                            $res_vf = explode('/', $dat_v);
                            $i2 = 0;
                            foreach ($child->xpath('input') as $value) {
                                $res_n = $res_id.'['.$i.']['.$value['field'].']';
                                $fields_id = (string)$value['field'];
                                $opt_at[$fields_id]['nameseparator']=(string)$value['nameseparator'];
                                if (!empty($value->class)) {
                                    $opt_at[$fields_id]['class']='form-control ' .(string)$value->class;
                                }
                                $opt_at[$fields_id]['nameseparator']=(string)$value['nameseparator'];
                                
                                echo '<input type="text" name="'. $res_n.'" class="'.$opt_at[$fields_id]['class'].'" value="'.$res_vf[$i2].'"';
                                if (isset($value->options)){
                                    foreach ($value->options ->attributes() as $optkey =>$optval){
                                        $opt_at[$fields_id]['options'][$optkey]=(string)$optval;
                                        echo  ' '.$optkey.'="'.$optval.'"';
                                    }
                                }
                                echo '> '.(string)$value['nameseparator'].' ';
                                $i2 ++;

                            }
                            echo '</div>';
                            $i++;
                            }
                            ?>
                                    
                                </div>
                                <div class = "col-sm-5">
                                <?php 
//                                print_r($opt_at);
//                                print_r(json_encode($opt_at));
//                                print_r(bin2hex(json_encode($opt_at)));
                                echo '<input type="button" id="'.$res_id.'-btn" data-id="'.$res_id.'" data-for="'.$res_id.'"data-json="'.bin2hex(json_encode($opt_at)).'" class="input-js-add" value="'._($child->button).'" />';
                                ?>
        			</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row"><div class="col-md-12">
                <span id="<?php echo $res_id;?>-help" class="help-block fpbx-help-block"><?php echo _($child->help);?></span>
            </div></div>
	</div>
        <?php        
        echo '<!-- END '.$child->label.' -->';
        
    }    
    
    if ($child['type'] == 'IS') {
            $res_n =  (string)$child->name;
            $res_id = $npref.$child->name;
            echo '<!-- Begin '.$child->label.' -->';
        ?>
        <div class="element-container">
            <div class="row"><div class="form-group"> 
                    <div class="col-md-3 radioset">
                        <label class="control-label" for="<?php echo $res_id; ?>"><?php echo _($child->label);?></label>
                        <i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $res_id; ?>"></i>
                    </div>
                    <div class="col-md-9 radioset">
                        <?php
                          $i = 0;
//                          $res_v = 'no';
                          if (empty($child->default)) {
                              $res_v = 'no';
                          } else {
                              $res_v = (string)$child->default;
                          }
                          if (!empty($child->value)) { 
                               $res_v = (string)$child->value;
                          }
                          if (!empty($fvalues[$res_n])) {
                            if (!empty($fvalues[$res_n]['data'])) {
                                $res_v = (string)$fvalues[$res_n]['data'];
                            }
                          }

                          foreach ($child->xpath('button') as $value) {
                            echo '<input type="radio" name="' . $res_id . '" id="' . $res_id. '_' . $i .'" value="' . $value[@value] . '"' . (strtolower((string)$value[@value]) == strtolower($res_v) ? " checked" : "") . '>';
                            echo '<label for="' . $res_id. '_' . $i . '">' . $value . '</label>';
                            $i++;
                          }
                        ?>                        
                        </div>
                </div></div>
            <div class="row"><div class="col-md-12">
                    <span id="<?php echo $res_id;?>-help" class="help-block fpbx-help-block"><?php echo _($child->help);?></span>
            </div></div>
        </div>

        <?php
        echo '<!-- END '.$child->label.' -->';

    }

/*
 *    Input element Select SLD - Date format 
 *                         SLZ - Time Zone 
 *                         SLT - TFTP Lang
 *                         SLM - Music on hold 
 *                         SLK - System KeySet
 */

    if ($child['type'] == 'SLD'  || $child['type'] == 'SLM'|| $child['type'] == 'SLK'|| $child['type'] == 'SLZ' || $child['type'] == 'SLZN') {
//        $value = $child -> select;
        $res_n =  (string)$child ->name;       
        $res_id = $npref.$res_n;
        if (empty($child->class)) {
           $child->class = 'form-control';
        }
        if ($child['type'] == 'SLD') {
            $select_opt= $day_format;
        }
//        if ($child['type'] == 'SLT') {
//            $select_opt= $tftp_lang;
//        }
        if ($child['type'] == 'SLM') {
            $select_opt= $moh_list;
        }
        if ($child['type'] == 'SLK') {
            $select_opt= $sofkey_list;
        }
        if ($child['type'] == 'SLZ') {
            $select_opt= $time_zone;
        }
        if ($child['type'] == 'SLZN') {
            $select_opt= $time_zone_name;
        }

        echo '<!-- Begin '.$child->label.' -->';

        ?>
        <div class="element-container">
           <div class="row"> <div class="form-group"> 
 
                   <div class="col-md-3">
                        <label class="control-label" for="<?php echo $res_id; ?>"><?php echo _($child->label);?></label>
                        <i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $res_id; ?>"></i>
                    </div>
                    <div class="col-md-9"><div class = "lnet form-group form-inline" data-nextid=1> <?php
                            echo  '<select name="'.$res_id.'" class="'. $child->class . '" id="' . $res_id . '">';
                            if (!empty($fvalues[$res_n])) {
                                if (!empty($fvalues[$res_n]['data'])) {
                                    $child->value = $fvalues[$res_n]['data'];
                                }
                            }
                            foreach ($select_opt as $key) {
                                echo '<option value="' . $key . '"';
                                if ($key == $child->value) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $key . '</option>';
                            }
                            ?> </select>
                    </div></div>
            </div></div>
            <div class="row"><div class="col-md-12">
                <span id="<?php echo $res_id;?>-help" class="help-block fpbx-help-block"><?php echo _($child->help);?></span>
            </div></div>
        </div>
        <?php
        echo '<!-- END '.$child->label.' -->';
        
    }
/*
 *    Input element Select SLS - System Language 
 */
    
    if ($child['type'] == 'SLS' || $child['type'] == 'SLT') {
//        $value = $child -> select;
        $res_n =  (string)$child ->name;       
        $res_id = $npref.$res_n;

        if ($child['type'] == 'SLS') {
            $select_opt= $syslangs;
        }
        if ($child['type'] == 'SLT') {
            $select_opt= $tftp_lang;
        }
        
        if (empty($child->class)) {
           $child->class = 'form-control';
        }
        
        echo '<!-- Begin '.$child->label.' -->';
        ?>
        <div class="element-container">
           <div class="row"> <div class="form-group"> 
 
                   <div class="col-md-3">
                        <label class="control-label" for="<?php echo $res_id; ?>"><?php echo _($child->label);?></label>
                        <i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $res_id; ?>"></i>
                    </div>
                    <div class="col-md-9"> <!-- <div class = "lnet form-group form-inline" data-nextid=1> --> <?php
                            echo  '<select name="'.$res_id.'" class="'. $child->class . '" id="' . $res_id . '">';
                            if (!empty($fvalues[$res_n])) {
                                if (!empty($fvalues[$res_n]['data'])) {
                                    $child->value = $fvalues[$res_n]['data'];
                                }
                            }
                            foreach ($select_opt as $key => $val) {
                                echo '<option value="' . $key . '"';
                                if ($key == $child->value) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $val. '</option>';
                            }
                            ?> </select>
                    <!-- </div> --> </div>
            </div></div>
            <div class="row"><div class="col-md-12">
                <span id="<?php echo $res_id;?>-help" class="help-block fpbx-help-block"><?php echo _($child->help);?></span>
            </div></div>
        </div>
        <!--END System Language-->
        <?php
        echo '<!-- END '.$child->label.' -->';
        
    }
/*
 *    Input element Select 
 */

    if ($child['type'] == 'SL') {
        $res_n =  (string)$child->name;
        $res_id = $npref.$child->name;
        
        if (empty($child ->class)) {
           $child->class = 'form-control';
        }
        echo '<!-- Begin '.$child->label.' -->';

        ?>
        <!--System Language-->
        <div class="element-container">
           <div class="row"> <div class="form-group"> 
 
                   <div class="col-md-3">
                        <label class="control-label" for="<?php echo $res_id; ?>"><?php echo _($child->label);?></label>
                        <i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $res_id; ?>"></i>
                    </div>
                    <div class="col-md-9"> <div class = "lnet form-group form-inline" data-nextid=1> <?php
                        echo  '<select name="'.$res_id.'" class="'. $child->class . '" id="' . $res_id . '">';
                        if (!empty($fvalues[$res_n])) {
                            if (!empty($fvalues[$res_n]['data'])) {
                                $child->value = $fvalues[$res_n]['data'];
                            }
                        }
                         foreach ($child->xpath('select') as $value) {
                            if (!empty($value[@value])) {
                                $key = $value[@value];
                            } else { 
                                $key =  (string)$value; 
                            }
                            echo '<option value="' . $key . '"';
                            if (strtolower((string)$key) == strtolower((string)$child->value)) {
                                echo ' selected="selected"';
                            }
                            echo '>' . (string)$value. '</option>';
                        }
                        ?> </select>
                            
                    </div> </div>
            </div></div>
            <div class="row"><div class="col-md-12">
                <span id="<?php echo $res_id;?>-help" class="help-block fpbx-help-block"><?php echo _($child->help);?></span>
            </div></div>
        </div>
        <!--END System Language-->
        <?php
        echo '<!-- END '.$child->label.' -->';
    }

 /*
 *    Input element Select SDM - Model List 
 *                         SDE - Extension List 
 */

    if ($child['type'] == 'SDM' || $child['type'] == 'SDE' ) {
//        $value = $child -> select;
        $res_n =  (string)$child ->name;       
        $res_id = $npref.$res_n;
        if (empty($child->class)) {
           $child->class = 'form-control';
        }
        if ($child['type'] == 'SDM') {
            $select_opt= $model_list;            
        }
        if ($child['type'] == 'SDE') {
            $select_opt= $extension_list;            
        }

        echo '<!-- Begin '.$child->label.' -->';

        ?>
        <div class="element-container">
           <div class="row"> <div class="form-group"> 
 
                   <div class="col-md-3">
                        <label class="control-label" for="<?php echo $res_id; ?>"><?php echo _($child->label);?></label>
                        <i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $res_id; ?>"></i>
                    </div>
                    <div class="col-md-9"><div class = "lnet form-group form-inline" data-nextid=1> <?php
                            echo  '<select name="'.$res_id.'" class="'. $child->class . '" id="' . $res_id . '">';
                            if (!empty($fvalues[$res_n])) {
                                if (!empty($fvalues[$res_n]['data'])) {
                                    $child->value = $fvalues[$res_n]['data'];
                                }
                            }
                            $fld = (string)$child->select['name'];
                            $flv = (string)$child->select;
                            $flk = (string)$child->select['dataid'];
                            $flkv= (string)$child->select['dataval'];
                            $key = (string)$child->default;
                            
                            foreach ($select_opt as $data) {
                                echo '<option value="' . $data[$fld] . '"';
                                if ($key == $data[$fld]) {
                                    echo ' selected="selected"';
                                }
                                if (!empty($flk)){
                                    echo ' data-id="'.$data[$flk].'"';
                                }
                                if (!empty($flkv)){
                                    echo ' data-val="'.$data[$flkv].'"';
                                }
                                echo '>' . $data[$flv] . '</option>';
                            }

                            ?> </select>
                    </div></div>
            </div></div>
            <div class="row"><div class="col-md-12">
                <span id="<?php echo $res_id;?>-help" class="help-block fpbx-help-block"><?php echo _($child->help);?></span>
            </div></div>
        </div>
        <?php
        echo '<!-- END '.$child->label.' -->';
        
    }
    
}
?>
<?php
    if ($h_show==1) {
       echo '</div>';
    }
 ?>
