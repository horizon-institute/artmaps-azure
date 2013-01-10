{if $updated}
<div class="updated">
    <p>
        <strong>Settings Updated</strong>
    </p>
</div>
{/if}
<div class="wrap">
    <form method="post" action="">
        <h2>ArtMaps Settings</h2>
        <h3>Object Search Source<h3>
        <select name="artmaps_blog_config_search_source">
            <option value="artmaps"{if $searchSource == 'artmaps'} selected="selected"{/if}>ArtMaps</option>
            <option value="tateartwork"{if $searchSource == 'tateartwork'} selected="selected"{/if}>Tate Collection</option>
        </select>
        <h3>Comment Template</h3>
        <textarea 
                name="artmaps_blog_option_comment_template" 
                style="width: 80%; height: 100px;">{$commentTemplate}</textarea>
        <h3>Metadata Title Template</h3>
        <textarea 
                name="artmaps_blog_option_metadata_title_template" 
                style="width: 80%; height: 100px;">{$metadataTitleTemplate}</textarea>
        <h3>Metadata Title Javascript</h3>
        <textarea 
                name="artmaps_blog_option_metadata_title_js" 
                style="width: 80%; height: 100px;">{$metadataTitleJS}</textarea>
        <h3>Metadata Format Javascript</h3>
        <textarea 
                name="artmaps_blog_option_metadata_format_js" 
                style="width: 80%; height: 100px;">{$metadataFormatJS}</textarea>
        <h3>Search Result Title Javascript</h3>
        <textarea 
                name="artmaps_blog_option_search_result_title_js" 
                style="width: 80%; height: 100px;">{$searchResultTitleJS}</textarea>
        <h3>Object Metadata Format Javascript</h3>
        <textarea 
                name="artmaps_blog_option_object_metadata_format_js" 
                style="width: 80%; height: 100px;">{$objectMetadataFormatJS}</textarea>
        <div class="submit">
        <input
                class="button-primary"
                type="submit"
                name="artmaps_blog_config_update"
                value="Save Changes" />
        </div>
    </form>
</div>