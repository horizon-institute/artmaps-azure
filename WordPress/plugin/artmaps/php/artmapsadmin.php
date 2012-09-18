<?php
if(!class_exists("ArtMapsAdmin")) {
class ArtMapsAdmin {

    private $core = null;

    public function __construct(ArtMapsCore $core) {
        $this->core = $core;
    }

    public function display() {
        $SubmitButtonName = "UpdateArtMapsAdminConfiguration";
        $CoreServerUrlFieldName = "ArtMapsAdminCoreServerUrl";
        $MasterKeyFieldName = "ArtMapsAdminMasterKey";
        $options = $this->core->getNetworkConfiguration();
        if(!isset($_POST[$SubmitButtonName]))
            goto DISPLAY;
        if(isset($_POST[$CoreServerUrlFieldName]))
            $options->CoreServerUrl = $_POST[$CoreServerUrlFieldName];
        if(isset($_POST[$MasterKeyFieldName]))
            $options->MasterKey = $_POST[$MasterKeyFieldName];
        $this->core->updateNetworkConfiguration($options);
        ?>
<div class="updated">
    <p>
        <strong><?= _e("Settings Updated.", "ArtMaps") ?></strong>
    </p>
</div>
        <?php
        DISPLAY:
        ?>
<div class="wrap">
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<h2>ArtMaps Settings</h2>
<h3>Core Server URL</h3>
<p>
    <label for="<?= $CoreServerUrlFieldName ?>">
        <input
                type="text"
                id="<?= $CoreServerUrlFieldName ?>"
                name="<?= $CoreServerUrlFieldName ?>"
                value="<?= _e(apply_filters("format_to_edit", $options->CoreServerUrl), "ArtMaps") ?>" />
    </label>
</p>
<h3>Master Key</h3>
<?php if($options->MasterKey != "") { ?>
<h4>For security, the saved key is not displayed</h4>
<?php } else { ?>
<h4>No key is currently assigned</h4>
<?php } ?>
<textarea name="<?= $MasterKeyFieldName ?>" style="width: 80%; height: 100px;"></textarea>
<div class="submit">
<input
        type="submit"
        name="<?= $SubmitButtonName ?>"
        value="<?= _e("Update", "ArtMaps") ?>" />
</div>
</form>
</div>
         <?php
     }
}}
?>