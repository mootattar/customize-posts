<div class="wrap">
  <h1><?php _e('Data entry interface', 'post_label_styler'); ?></h1>

  <div class="form-group">
    <label for="titleText"><?php _e('title text', 'post_label_styler'); ?></label>
    <input type="text" id="titleText">
  </div>
  <div class="form-group">
    <label for="tag"><?php _e('tag', 'post_label_styler'); ?></label>
    <input type="text" id="tag">
  </div>
  <div class="form-group">
    <label for="titleBgColor"><?php _e('title background color', 'post_label_styler'); ?></label>
    <input type="color" id="titleBgColor">
    <button type="button" id="titleNoBgColor"><?php _e('without color', 'post_label_styler'); ?></button>
  </div>
  <div class="form-group">
    <label for="descBgColor"><?php _e('content background color', 'post_label_styler'); ?></label>
    <input type="color" id="descBgColor">
    <button type="button" id="descNoBgColor"><?php _e('without color', 'post_label_styler'); ?></button>
  </div>
  <div class="form-group">
    <label for="titleTextColor"><?php _e('title text color', 'post_label_styler'); ?></label>
    <input type="color" id="titleTextColor">
  </div>

  <button id="add_to_schedule"><?php _e('add to schedule', 'post_label_styler'); ?></button>
  <div id="attar_post_preview" style=" border:1px solid black ; border-radius:10px ;padding:40px;width:50%;margin:50px;display:none;">			
    <h2 class="wp-block-post-title has-x-large-font-size"><span class="attar-post-title-style" data-label="" style="--titleColor:#ffffff; --titleBg:transparent;"><?php _e('The title', 'post_label_styler'); ?></span></a></h2>
    <div class="entry-content alignfull wp-block-post-content has-medium-font-size has-global-padding is-layout-constrained wp-block-post-content-is-layout-constrained">
      <div class="attar-custom-post-box">
        <p><?php _e('the content is here!', 'post_label_styler'); ?></p>
      </div>
    </div>
  </div>

  <table id="dataTable">
    <thead>
      <tr>
        <th><?php _e('title', 'post_label_styler'); ?></th>
        <th><?php _e('content', 'post_label_styler'); ?></th>
        <th><?php _e('tag', 'post_label_styler'); ?></th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  
</div>