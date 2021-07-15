<div class="rightPageContainer">
    <form name="Register" action="index.php?Page_Type=Register" method="post" class="inputForm"
          onsubmit="return confirm('Click OK to confirm your agreement to the terms and conditions of GOCDB account registration.');">
        <h1>Register</h1>
        <br />
        Register Unique Identity: <b> <?php echo($params['dn']); ?> </b>
        <br/>
        <br/>


    <div class="alert alert-warning" role="alert">
            <h3>Terms and Conditions of Account Registration</h3>
            <ul>
               <li>By registering a GOCDB account you are agreeing to abide by the
                   <a href="<?php echo $params["aup"]?>" target="_blank" title="<?php echo $params["aup_title"]?>">
                   <?php echo $params["aup_title"]?>
                   <img src="/portal/img/new_window.png" alt="new window logo" class="new_window"></a>
               </li>
               <li>Personal data, which you provide below and that is collected when you use GOCDB, will be processed in accordance with the
                   <a href="<?php echo $params["privacy_notice"]?>" target="_blank" title="<?php echo $params["privacy_notice_title"]?>">
                   <?php echo $params["privacy_notice_title"]?>
                   <img src="/portal/img/new_window.png" alt="new window logo"  class="new_window"></a>
               </li><br>
               <li>Please read both the above documents before registering your account.
              </li>
            </ul>
        </div>



        <div class="listContainer">
            <b>Please provide the following data for your account.</b>
            <br>
            <?php
            foreach ($params['authAttributes'] as $key => $val) {
                $attributeValStr = '';
                foreach ($val as $v) {
                    $attributeValStr .= $v . ',';
                }
                //if(strlen($attributeValStr) > 2){$attributeValStr = substr($attributeValStr, 2);}
                xecho('[' . $key . ']  [' . $attributeValStr . ']');
                echo '<br>';
            }
            ?>
        </div>
        <br>

        <span class="input_name">
            Title
        </span>
        <?php
            $titles = array('', 'Dr', 'Miss', 'Mr', 'Mrs',  'Ms', 'Mx', 'Prof');
        ?>
        <select name="TITLE" class="add_edit_form">
          <?php
            foreach($titles as $title) {
              echo '<option value="'.$title.'">'.$title.'</option>';
            }
          ?>
        </select>

        <span class="input_name">
            Forename *
            <span class="input_syntax" >(unaccentuated letters, spaces, dashes and quotes)</span>
        </span>
        <input class="input_input_text" type="text" name="FORENAME" />

        <span class="input_name">
            Surname *
            <span class="input_syntax" >(unaccentuated letters, spaces, dashes and quotes)</span>
        </span>
        <input class="input_input_text" type="text" name="SURNAME" />

        <span class="input_name">
            E-Mail *
            <span class="input_syntax" >(valid e-mail format)</span>
        </span>
        <input class="input_input_text" type="text" name="EMAIL" />

        <span class="input_name">
            Telephone Number
            <span class="input_syntax" >(numbers, optional +, dots spaces or dashes)</span>
        </span>
        <input class="input_input_text" type="text" name="TELEPHONE" />

        <br />
        <input class="input_button" type="submit" value="Submit" />
    </form>
</div>
