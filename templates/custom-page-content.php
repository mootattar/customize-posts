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
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    var scheduleDataFromPHP = <?php echo json_encode(get_option('custom_schedule_data', [])); ?>;
    let useTitleBg = true;
    let useDescBg = true;

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

        tbody.appendChild(row);
      });

      document.querySelectorAll('.delete-row').forEach(btn => {
        btn.addEventListener('click', function () {
          const tagToDelete = this.dataset.tag;
          deleteRowFromDatabase(tagToDelete);
        });
      });
    }

    function sendDataToServer(data) {
      const formData = new FormData();
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
      const titleBg = document.getElementById('titleBgColor').value;
      const descBg = document.getElementById('descBgColor').value;
      const titleColor = document.getElementById('titleTextColor').value;

      if (!titleText || !tag) {
        alert("<?php echo esc_js(__('يرجى تعبئة جميع الحقول.', 'customize_title_and_content_posts')); ?>");
        return;
      }
      sendDataToServer({ titleText, tag, titleBg, descBg, titleColor }); // send data to admin-ajax.php
      const tbody = document.querySelector('#dataTable tbody');
      const row = document.createElement('tr');

      const titleCell = document.createElement('td');
      titleCell.textContent = titleText;
      titleCell.style.backgroundColor = titleBg;
      titleCell.style.color = titleColor;

      const descCell = document.createElement('td');
      descCell.textContent = '—'; // no content
      descCell.style.backgroundColor = descBg;

      const tagCell = document.createElement('td');
      tagCell.textContent = tag;

      const deleteCell = document.createElement('td');
      const deleteButton = document.createElement('button');
      deleteButton.textContent = "<?php echo esc_js(__('delete', 'customize_title_and_content_posts')); ?>";
      deleteButton.className = 'delete-row';
      deleteButton.dataset.tag = tag;
      deleteButton.addEventListener('click', function () {
        deleteRowFromDatabase(tag);
      });
      deleteCell.appendChild(deleteButton);

      row.appendChild(titleCell);
      row.appendChild(descCell);
      row.appendChild(tagCell);
      row.appendChild(deleteCell);

      tbody.appendChild(row);
    }

    add_to_schedule.addEventListener('click', addToTable);

    // Fill the table initially
    fillTableWithData(scheduleDataFromPHP);
  </script>
</div>