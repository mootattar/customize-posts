const titleBg = document.getElementById("titleBgColor");
const descBg = document.getElementById("descBgColor");
var ajaxurl = attardata.ajaxurl;
var scheduleDataFromPHP = attardata.scheduleDataFromPHP;
let useTitleBg = true;
let useDescBg = true;

descBg.addEventListener("change", function (e) {
  useDescBg = true;
});
titleBg.addEventListener("change", function (e) {
  useTitleBg = true;
});
function attar_lighten_hex_color(hex, percent) {
  hex = hex.replace(/^#/, "");

  if (hex.length === 3) {
    hex = hex
      .split("")
      .map((c) => c + c)
      .join("");
  }

  let r = parseInt(hex.substring(0, 2), 16);
  let g = parseInt(hex.substring(2, 4), 16);
  let b = parseInt(hex.substring(4, 6), 16);

  r = Math.round(r + (255 - r) * percent);
  g = Math.round(g + (255 - g) * percent);
  b = Math.round(b + (255 - b) * percent);

  return `#${r.toString(16).padStart(2, "0")}${g
    .toString(16)
    .padStart(2, "0")}${b.toString(16).padStart(2, "0")}`;
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
  formData.append("action", "attar_delete_schedula_data");
  formData.append("tag", tag);
  formData.append("_wpnonce", attardata.nonce);

  fetch(ajaxurl, {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((response) => {
      if (response.success) {
        scheduleDataFromPHP = response.data.updatedData;
        fillTableWithData(scheduleDataFromPHP);
      } else {
        console.error("error!");
      }
    });
}

function attar_handle_preview_post(tag) {
  current_tag = scheduleDataFromPHP[tag];
  const show_post = document.getElementById("attar_post_preview");
  show_post.style.display = "block";

  const titleElement = show_post.querySelector(".attar-post-title-style");
  titleElement.dataset.label = current_tag.titleText;
  titleElement.style.setProperty(
    "--titleColor",
    current_tag.titleColor || "#ffffff"
  );
  titleElement.style.setProperty("--titleBg", current_tag.titleBg || "#000000");

  const contentElement = show_post.querySelector(".attar-custom-post-box");
  contentElement.style.setProperty(
    "--descBorder",
    current_tag.descBg || "transparent"
  );
  contentElement.style.setProperty(
    "--descBg",
    attar_lighten_hex_color(current_tag.descBg, 0.7) || "transparent"
  );
}

function fillTableWithData(data) {
  const tbody = document.querySelector("#dataTable tbody");
  tbody.innerHTML = "";
  Object.entries(data).forEach(([tag, entry]) => {
    const row = document.createElement("tr");

    const titleCell = document.createElement("td");
    titleCell.textContent = entry.titleText;
    titleCell.style.backgroundColor = entry.titleBg;
    titleCell.style.color = entry.titleColor;

    const descCell = document.createElement("td");
    descCell.textContent = "—";
    descCell.style.backgroundColor = entry.descBg;

    const tagCell = document.createElement("td");
    tagCell.textContent = tag;

    const previewCell = document.createElement("td");
    const previewButton = document.createElement("button");
    previewButton.textContent = attardata.preview;
    previewButton.className = "preview-row";
    previewButton.dataset.tag = tag;
    previewCell.appendChild(previewButton);

    const deleteCell = document.createElement("td");
    const deleteButton = document.createElement("button");
    deleteButton.textContent = attardata.delete;
    deleteButton.className = "delete-row";
    deleteButton.dataset.tag = tag;
    deleteCell.appendChild(deleteButton);

    row.appendChild(titleCell);
    row.appendChild(descCell);
    row.appendChild(tagCell);
    row.appendChild(deleteCell);
    row.appendChild(previewCell);

    tbody.appendChild(row);
  });

  document.querySelectorAll(".preview-row").forEach((btn) => {
    btn.addEventListener("click", function () {
      const tagToPreview = this.dataset.tag;
      console.log(tagToPreview);
      attar_handle_preview_post(tagToPreview);
    });
  });

  document.querySelectorAll(".delete-row").forEach((btn) => {
    btn.addEventListener("click", function () {
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
    titleColor: data.titleColor,
  };

  formData.append("action", "attar_save_schedula_data");
  formData.append("titleText", data.titleText);
  formData.append("tag", data.tag);
  formData.append("titleBg", data.titleBg);
  formData.append("descBg", data.descBg);
  formData.append("titleColor", data.titleColor);
  formData.append("_wpnonce", attardata.nonce);

  fetch(ajaxurl, {
    method: "POST",
    body: formData,
  });
}

function addToTable() {
  const titleText = document.getElementById("titleText").value.trim();
  const tag = document.getElementById("tag").value.trim();
  const titleBg = useTitleBg
    ? document.getElementById("titleBgColor").value
    : "transparent";
  const descBg = useDescBg
    ? document.getElementById("descBgColor").value
    : "transparent";
  const titleColor = document.getElementById("titleTextColor").value;

  if (!titleText || !tag) {
    alert(attardata.alert);
    return;
  }
  sendDataToServer({ titleText, tag, titleBg, descBg, titleColor });

  const tbody = document.querySelector("#dataTable tbody");
  const row = document.createElement("tr");

  const titleCell = document.createElement("td");
  titleCell.textContent = titleText;
  titleCell.style.backgroundColor = useTitleBg ? titleBg : "transparent";
  titleCell.style.color = titleColor;

  const descCell = document.createElement("td");
  descCell.textContent = "—";
  descCell.style.backgroundColor = useDescBg ? descBg : "transparent";

  const tagCell = document.createElement("td");
  tagCell.textContent = tag;

  const previewCell = document.createElement("td");
  const previewButton = document.createElement("button");
  previewButton.textContent = attardata.preview;
  previewButton.className = "preview-row";
  previewButton.dataset.tag = tag;
  previewCell.appendChild(previewButton);

  const deleteCell = document.createElement("td");
  const deleteButton = document.createElement("button");
  deleteButton.textContent = attardata.delete;
  deleteButton.className = "delete-row";
  deleteButton.dataset.tag = tag;

  previewButton.addEventListener("click", function () {
    attar_handle_preview_post(tag);
  });

  deleteButton.addEventListener("click", function () {
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

add_to_schedule.addEventListener("click", addToTable);

fillTableWithData(scheduleDataFromPHP);
