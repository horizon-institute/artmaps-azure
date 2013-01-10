<?php
if(!class_exists('ArtMapsImportAdmin')) {
class ArtMapsImportAdmin {

    public function register() {
        add_submenu_page(
                'options-general.php',
                'ArtMaps Import',
                'ArtMaps Import',
                'manage_options',
                'artmaps-import-admin-page',
                array($this, 'display'));
    }

    public function display() {
        if(!current_user_can('manage_options'))
            wp_die(__('You do not have sufficient permissions to access this page.'));
        require_once('ArtMapsNetwork.php');
        $nw = new ArtMapsNetwork();
        $blog = $nw->getCurrentBlog();
        $imported = $this->checkSubmission($blog);
        require_once('ArtMapsTemplating.php');
        $tpl = new ArtMapsTemplating();
        echo $tpl->renderImportAdminPage($imported);
    }

    private function checkSubmission(ArtMapsBlog $blog) {
        $r = false;

        if(isset($_FILES['artmaps_import_file'])) {
            require_once('ArtMapsCrypto.php');
            $file = $_FILES['artmaps_import_file']['tmp_name'];
            $crypto = new ArtMapsCrypto();
            $sig = $crypto->signFile(
                    $file,
                    $blog->getKey());
            require_once('ArtMapsCoreServer.php');
            $cs = new ArtMapsCoreServer($blog);
            $cs->doImport($file, $sig);
            $r = true;
        }

        return $r;
    }
}}
?>