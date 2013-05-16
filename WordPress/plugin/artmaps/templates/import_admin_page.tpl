{if $imported}
<div class="updated">
    <p>
        <strong>The file was successfully imported</strong>
    </p>
</div>
{/if}
<div class="wrap">
    <form method="post" action="" enctype="multipart/form-data">
        <h2>ArtMaps Import</h2>
        <input type="file" name="artmaps_import_file" />
        <div class="submit">
        <input
                class="button-primary"
                type="submit"
                name="artmaps_import_update"
                value="Import" />
        </div>
    </form>
</div>