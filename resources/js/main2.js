// This file contains JavaScript code to handle dynamic menu and submenu rendering
export const getSubmenu = async (id) => {
    let html = "";
    let url = `/get-submenu/${id}`;

    let response = await fetch(url);
    if (!response.ok) {
        console.error("Error fetching submenu:", response.statusText);
        return;
    }

    let data = await response.json();
    data.forEach((element) => {
        html += `
        <div class="submenu-item mb-2">
          <span class="text-muted d-inline-block me-2 card p-3">
            <strong>${element.sub_name}</strong>
          </span>
          <span class="text-muted d-inline-block me-2 p-3"> 
            <select class="form-select" id="get_navigation_${element.id}">
              <option selected disabled>${element.sub_name}</option>
            </select>
          </span>
        </div>
        `;

        getNavigation(element.id);
    });

    const target = document.getElementById("submenu_" + id);
    if (target) {
        target.innerHTML = html;

        // 🔁 After rendering HTML, attach listeners to each select
        data.forEach((element) => {
            const selectEl = document.getElementById(
                "get_navigation_" + element.id
            );
            if (selectEl) {
                selectEl.addEventListener("change", () => {
                    changeMenu(element.id); // calls changeMenu with the right sub_id
                });
            }
        });
    }
};

window.getSubmenu = getSubmenu;
// Function to fetch navigation options and populate the select element - dropdown
export const getNavigation = async (id) => {
    let url = `/get-nav`;
    let html = " ";

    let response = await fetch(url);
    if (!response.ok) {
        console.error("Error fetching navigation:", response.statusText);
        return;
    }

    let data = await response.json();
    data.forEach((element) => {
        html += `<option value="${element.id}" >${element.name}</option>`;
    });

    const target = document.getElementById("get_navigation_" + id);
    if (!target) {
        console.error("Target element not found for ID:", id);
        return;
    }
    target.innerHTML = html;
};
window.getNavigation = getNavigation;

// changeMenu function to update the submenu with the selected navigation ID
export const changeMenu = async (subId) => {
    const select = document.getElementById("get_navigation_" + subId);
    if (!select) {
        console.error("Select not found for submenu ID:", subId);
        return;
    }

    const selectedValue = select.value;
    if (!selectedValue) {
        console.warn("No value selected for submenu ID:", subId);
        return;
    }

    try {
        const response = await fetch(`/change-submenu/${subId}`, {
            method: "PATCH",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({ navigation_id: selectedValue }),
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            console.error(
                "Error changing submenu:",
                response.statusText,
                errorData
            );
            return;
        }

        // 🔁 Re-fetch the submenu after successful update
        await getSubmenu(subId);
    } catch (err) {
        console.error("Network or server error:", err);
    }
};

export const getMenu = async () => {
    const url = `/navigation/get`;
    let html = "";

    try {
        const response = await fetch(url);
        if (!response.ok) {
            console.error("Error fetching menu:", response.statusText);
            return;
        }

        const data = await response.json();
        data.forEach((element) => {
            html += `
                <ul class="list-group">
                    <li class="list-group-item border-0 p-4 mb-2 bg-gray-100 border-radius-lg">
                        <div class="d-flex flex-column">
                            <div class="mb-2">
                                <strong>${element.name}</strong>
                            </div>

                            <div id="submenu_${element.id}" class="ps-3">
                                <!-- Submenus will be appended here -->
                            </div>
                        </div>
                    </li>
                </ul>

            `; // add line break or wrap in HTML for display
        });

        const target = document.getElementById("nav");
        if (!target) {
            console.error("Target element not found for ID: nav");
            return;
        }

        target.innerHTML = html;
    } catch (err) {
        console.error("Network or server error:", err);
    }
};

window.getMenu = getMenu;
