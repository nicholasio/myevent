<?php

include_once(LIB_DIR    . '/Twig/Autoloader.php');
Twig_Autoloader::register();

$___MoxoTwigloader___ = new Twig_Loader_Filesystem(VIEWS_DIR);
$___MoxoTwig___       = new Twig_Environment($___MoxoTwigloader___, array(
    //'cache' => CACHE_DIR,
    'debug' => true
));
$___MoxoTwig___->getExtension('core')->setTimezone('America/Fortaleza');

$___MoxoTwig___->addExtension(new Twig_Extension_Debug());

$formInput = new Twig_SimpleFunction('form_input', function ($label, $id, $type, $default_value = null, $params = null, $helpText = '') {
    ob_start();
    if ( is_null($default_value) )
        $default_value = '';
    $sParams = [];
    $class = false;
    if ( !is_null($params) && is_array($params) ){
        foreach($params as $key => $value){
            if($key == 'class')
                $class = true;
            $sParams[] = $key . '="'. $value . '"';
        }
    }
    if ( ! $class )
        $sParams[] = 'class=input-xlarge focused';

    ?>
      <div class="control-group">
        <label class="control-label" for="<?= $id ?>"><?= $label ?>: </label>
        <div class="controls">
          <input   <?= implode($sParams, ' ') ?> id="<?= $id ?>" type="<?= $type ?>" name="<?= $id ?>" value="<?= $default_value ?>" >
           <span class="help-inline"><?= $helpText ?></span>
        </div>

      </div>
    <?php
    $content = ob_get_contents();
    ob_end_clean();
    echo $content;
});

$___MoxoTwig___->addFunction($formInput);

$formCombo = new Twig_SimpleFunction('form_combo', function($label, $id, $options, $default_value = null, $helpText = ''){
    ?>
    <div class="control-group">
        <label class="control-label" for="selectError3"><?= $label ?></label>
        <div class="controls">
          <select id="<?= $id ?>" name="<?= $id ?>">
            <?php foreach($options as $key => $value) : ?>
                <option <?php
                    if ($default_value == $key)
                        echo 'selected';
                ?>
                    value="<?= $key ?>"><?= $value ?></option>
            <?php endforeach; ?>
          </select>
          <span class="help-inline"><?= $helpText ?></span>
        </div>
      </div>
    <?php

});

$___MoxoTwig___->addFunction($formCombo);

$flashMessage = new Twig_SimpleFunction('flash_messages', function($title){
    $flashHelper = \Moxo\Helpers\FlashMessages::getInstance();
    //var_dump($_SESSION);

    if ( $flashHelper->hasErrors() ) {
        $msg = $flashHelper->display('error', false);
        $type = 'error';
        //$msg  = "<h4>{$title}</h4>" . '<br />' . $msg;

    } else if ($flashHelper->hasMessages() ) {
        $msg = $flashHelper->display('success', false);
        $type = 'success';
    } else
        return;


    ?>
    <div class="box-content alerts">
        <div class="alert alert-<?= $type ?>">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <?= $msg; ?>
        </div>
    </div>
    <?php

});
$___MoxoTwig___->addFunction($flashMessage);

$emailHash = new Twig_SimpleFunction('email_hash', function($email){
    $email = trim( $email );
    $email = strtolower( $email );
    echo md5( $email );
});
$___MoxoTwig___->addFunction($emailHash);

$sha1 = new Twig_SimpleFunction('sha1', function($str){
    return sha1( $str );
});

$___MoxoTwig___->addFunction($sha1);

$getMeta = new Twig_SimpleFunction('get_meta', function( $meta_key ){
    return get_meta( $meta_key );
});

$___MoxoTwig___->addFunction($getMeta);