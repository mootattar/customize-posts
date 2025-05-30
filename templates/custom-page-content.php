<div class="wrap">
  <h1><?php _e('Data entry interface', 'customize_title_and_content_posts'); ?></h1>
  <style>
    .wrap h1 {
      color: #0073aa;
      font-size: 32px;
    }

    .wrap p {
      font-size: 18px;
    }
    .form-group {
      margin-bottom: 10px;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    input[type="text"], input[type="color"] {
      width: 50%;
      padding: 6px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      padding: 10px 15px;
      background-color: #0073aa;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    table {
      margin-top: 20px;
      width: 100%;
      border-collapse: collapse;
    }
    table, th, td {
      border: 1px solid #ccc;
    }
    th, td {
      padding: 10px;
      text-align: center;
    }
    .attar-post-title-style {
      position: relative;
      font-size: 2rem;
      font-weight: bold;
      }

    .attar-post-title-style::after {
      content: attr(data-label);
      display: block;
      padding: 4px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: bold;
      margin-bottom: 2px;
      position: absolute;
      top: -1.5em;
      right: -25px;
      color: var(--titleColor, #000);
      background-color: var(--titleBg, #fff);
      transition: all 0.3s ease;
    }
    html[dir="rtl"] .attar-post-title-style::after{
        content: attr(data-label);
        display: block;
        padding: 4px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 2px;
        position: absolute;
        top: -2em;
        left: -25px;
        right:auto;
        color: var(--titleColor, #000);
        background-color: var(--titleBg, #fff);
        transition: all 0.3s ease;
    }
    .attar-custom-post-box {
      transition: all 0.3s ease;
      border-radius: 8px;
      margin-bottom: 20px;
      padding:5px;
      border-left: 4px solid var(--descBorder);
      background-color: var(--descBg);
    }
    html[dir="rtl"] .attar-custom-post-box {
      transition: all 0.3s ease;
      border-radius: 8px;
      margin-bottom: 20px;
      padding:5px;
      border-right: 4px solid var(--descBorder);
      border-left:0;
      background-color: var(--descBg);
    }

    .attar-custom-post-box h2 {
      margin-bottom: 10px;
      font-size: 1.5rem;
    }
  </style>

  <div class="form-group">
    <label for="titleText"><?php _e('title text', 'customize_title_and_content_posts'); ?></label>
    <input type="text" id="titleText">
  </div>
  <div class="form-group">
    <label for="tag"><?php _e('tag', 'customize_title_and_content_posts'); ?></label>
    <input type="text" id="tag">
  </div>
  <div class="form-group">
    <label for="titleBgColor"><?php _e('title background color', 'customize_title_and_content_posts'); ?></label>
    <input type="color" id="titleBgColor">
    <button type="button" id="titleNoBgColor"><?php _e('without color', 'customize_title_and_content_posts'); ?></button>
  </div>
  <div class="form-group">
    <label for="descBgColor"><?php _e('content background color', 'customize_title_and_content_posts'); ?></label>
    <input type="color" id="descBgColor">
    <button type="button" id="descNoBgColor"><?php _e('without color', 'customize_title_and_content_posts'); ?></button>
  </div>
  <div class="form-group">
    <label for="titleTextColor"><?php _e('title text color', 'customize_title_and_content_posts'); ?></label>
    <input type="color" id="titleTextColor">
  </div>

  <button id="add_to_schedule"><?php _e('add to schedule', 'customize_title_and_content_posts'); ?></button>
  <div id="attar_post_preview" style=" border:1px solid black ; border-radius:10px ;padding:40px;width:50%;margin:50px;display:none;">			
    <h2 class="wp-block-post-title has-x-large-font-size"><span class="attar-post-title-style" data-label="" style="--titleColor:#ffffff; --titleBg:transparent;"><?php _e('The title', 'customize_title_and_content_posts'); ?></span></a></h2>
    <div class="entry-content alignfull wp-block-post-content has-medium-font-size has-global-padding is-layout-constrained wp-block-post-content-is-layout-constrained">
      <div class="attar-custom-post-box">
        <p><?php _e('the content is here!', 'customize_title_and_content_posts'); ?></p>
      </div>
    </div>
  </div>

  <table id="dataTable">
    <thead>
      <tr>
        <th><?php _e('title', 'customize_title_and_content_posts'); ?></th>
        <th><?php _e('content', 'customize_title_and_content_posts'); ?></th>
        <th><?php _e('tag', 'customize_title_and_content_posts'); ?></th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <script>
    const titleBg = document.getElementById('titleBgColor');
    const descBg = document.getElementById('descBgColor');
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    var scheduleDataFromPHP = <?php echo json_encode(get_option('attar_custom_posts_based_on_tags', [])); ?>;
    let useTitleBg = true;
    let useDescBg = true;
    
    descBg.addEventListener('change', function (e) {
      useDescBg = true;
    });
    titleBg.addEventListener('change', function (e) {
      useTitleBg = true;
    });
    function attar_lighten_hex_color(hex, percent) {
      hex = hex.replace(/^#/, '');

      if (hex.length === 3) {
        hex = hex.split('').map(c => c + c).join('');
      }

      let r = parseInt(hex.substring(0, 2), 16);
      let g = parseInt(hex.substring(2, 4), 16);
      let b = parseInt(hex.substring(4, 6), 16);

      r = Math.round(r + (255 - r) * percent);
      g = Math.round(g + (255 - g) * percent);
      b = Math.round(b + (255 - b) * percent);

      return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
    }

    document.getElementById("titleNoBgColor").addEventListener("click", () => {
      useTitleBg = false;
      document.getElementById("titleBgColor").value = "#ffffff";
    });
    
    document.getElementById("descNoBgColor").addEventListener("click", () => {
        useDescBg = false;
        document.getElementById("descBgColor").value = "#ffffff";
    });
    const add_to_schedule = document.getElementById("add_to_schedule");

    function deleteRowFromDatabase(tag) {
      const formData = new FormData();
      formData.append('action', 'attar_delete_schedula_data');
      formData.append('tag', tag);

      fetch(ajaxurl, {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(response => {
        if (response.success) {
          scheduleDataFromPHP = response.data.updatedData;
          fillTableWithData(scheduleDataFromPHP);
        } else {
          console.error("error!")
        }
      });
    }

    function attar_handle_preview_post(tag){
      current_tag = scheduleDataFromPHP[tag];
      const show_post = document.getElementById('attar_post_preview');
      show_post.style.display = 'block';
      
      const titleElement = show_post.querySelector('.attar-post-title-style');
      titleElement.dataset.label = current_tag.titleText;
      titleElement.style.setProperty('--titleColor', current_tag.titleColor || '#ffffff');
      titleElement.style.setProperty('--titleBg', current_tag.titleBg || '#000000');

      const contentElement = show_post.querySelector('.attar-custom-post-box');
      contentElement.style.setProperty('--descBorder',current_tag.descBg||'transparent')
      contentElement.style.setProperty('--descBg',attar_lighten_hex_color(current_tag.descBg,0.7) || 'transparent');
    }

    function fillTableWithData(data) {
      const tbody = document.querySelector('#dataTable tbody');
      tbody.innerHTML = "";
      Object.entries(data).forEach(([tag, entry]) => {
        const row = document.createElement('tr');

        const titleCell = document.createElement('td');
        titleCell.textContent = entry.titleText;
        titleCell.style.backgroundColor = entry.titleBg;
        titleCell.style.color = entry.titleColor;

        const descCell = document.createElement('td');
        descCell.textContent = '—';
        descCell.style.backgroundColor = entry.descBg;

        const tagCell = document.createElement('td');
        tagCell.textContent = tag;

        const previewCell = document.createElement('td');
        const previewButton = document.createElement('button');
        previewButton.textContent = "<?php echo esc_js(__('preview', 'customize_title_and_content_posts')); ?>";
        previewButton.className = 'preview-row';
        previewButton.dataset.tag = tag;
        previewCell.appendChild(previewButton);

        const deleteCell = document.createElement('td');
        const deleteButton = document.createElement('button');
        deleteButton.textContent = "<?php echo esc_js(__('delete', 'customize_title_and_content_posts')); ?>";
        deleteButton.className = 'delete-row';
        deleteButton.dataset.tag = tag;
        deleteCell.appendChild(deleteButton);

        row.appendChild(titleCell);
        row.appendChild(descCell);
        row.appendChild(tagCell);
        row.appendChild(deleteCell);
        row.appendChild(previewCell);

        tbody.appendChild(row);
      });

      document.querySelectorAll('.preview-row').forEach(btn=>{
        btn.addEventListener('click',function(){
          const tagToPreview = this.dataset.tag;
          console.log(tagToPreview);
          attar_handle_preview_post(tagToPreview)
        })
      })

      document.querySelectorAll('.delete-row').forEach(btn => {
        btn.addEventListener('click', function() {
          const tagToDelete = this.dataset.tag;
          deleteRowFromDatabase(tagToDelete);
        });
      });
    }

    function sendDataToServer(data) {
      const formData = new FormData();
      scheduleDataFromPHP[data.tag] = {
        titleText: data.titleText,
        titleBg: data.titleBg,
        descBg: data.descBg,
        titleColor: data.titleColor
      };

      formData.append('action', 'attar_save_schedula_data');
      formData.append('titleText', data.titleText);
      formData.append('tag', data.tag);
      formData.append('titleBg', data.titleBg);
      formData.append('descBg', data.descBg);
      formData.append('titleColor', data.titleColor);

      fetch(ajaxurl, {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(response => {
        if (response.success) {
          console.log('✅', response.data.message);
        } else {
          console.error('❌', response.data.message);
        }
      });
    }

    function addToTable() {
      const titleText = document.getElementById('titleText').value.trim();
      const tag = document.getElementById('tag').value.trim();
      const titleBg = useTitleBg ? document.getElementById('titleBgColor').value : 'transparent';
      const descBg = useDescBg ? document.getElementById('descBgColor').value : 'transparent';
      const titleColor = document.getElementById('titleTextColor').value;

      if (!titleText || !tag) {
        alert("<?php echo esc_js(__('please fill all Fields!', 'customize_title_and_content_posts')); ?>");
        return;
      }
      sendDataToServer({ titleText, tag, titleBg, descBg, titleColor }); // send data to admin-ajax.php
      
      const tbody = document.querySelector('#dataTable tbody');
      const row = document.createElement('tr');

      const titleCell = document.createElement('td');
      titleCell.textContent = titleText;
      titleCell.style.backgroundColor = useTitleBg ? titleBg: 'transparent';
      titleCell.style.color = titleColor;

      const descCell = document.createElement('td');
      descCell.textContent = '—'; // no content
      descCell.style.backgroundColor =useDescBg ? descBg: 'transparent';

      const tagCell = document.createElement('td');
      tagCell.textContent = tag;

      const previewCell = document.createElement('td');
      const previewButton = document.createElement('button');
      previewButton.textContent = "<?php echo esc_js(__('preview', 'customize_title_and_content_posts')); ?>";
      previewButton.className = 'preview-row';
      previewButton.dataset.tag = tag;
      previewCell.appendChild(previewButton);

      const deleteCell = document.createElement('td');
      const deleteButton = document.createElement('button');
      deleteButton.textContent = "<?php echo esc_js(__('delete', 'customize_title_and_content_posts')); ?>";
      deleteButton.className = 'delete-row';
      deleteButton.dataset.tag = tag;

      previewButton.addEventListener('click',function(){
        attar_handle_preview_post(tag);
      })

      deleteButton.addEventListener('click', function () {
        deleteRowFromDatabase(tag);
      });
      deleteCell.appendChild(deleteButton);

      row.appendChild(titleCell);
      row.appendChild(descCell);
      row.appendChild(tagCell);
      row.appendChild(deleteCell);
      row.appendChild(previewCell);

      tbody.appendChild(row);
    }

    add_to_schedule.addEventListener('click', addToTable);

    // Fill the table initially
    fillTableWithData(scheduleDataFromPHP);
  </script>
</div>