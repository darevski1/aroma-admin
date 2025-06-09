import { AsyncCompiler } from "sass";
import Toastify from "toastify-js";

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
                          
                            <ul id="submenu_${element.id}" class="ps-3">
                                <!-- Submenus will be appended here -->
                                <li class="text-muted small">Loading...</li>
                            </ul>
                        </div>
                    </li>
                </ul>

            `;
            getSubmenu(element.id); // Fetch submenu for each main menu item
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
          <li id="submenus_${
              element.id
          }" class="submenu-item mb-2 animate__animated animate__fadeIn">
          <span class="text-muted d-inline-block me-2   p-3">
            <strong>${element.sub_name}</strong>
          </span>
          <span class="text-muted d-inline-block me-2 p-3"> 
            <select class="form-control" id="get_navigation_${element.id}">
              <option selected disabled>${element.sub_name}</option>
            </select>
          </span>
          <span class="text-muted d-inline-block me-2 p-3">
            <a class="btn btn-danger" onclick="deleteSubMenu(${element.id})">
              <i class="fas fa-trash"></i>
            </a>
            </span>
            <span class="text-muted d-inline-block me-2 p-3">
            <div class="form-check">
           <input class="form-check-input" type="checkbox" value="3244"
  ${element.published == true ? "checked" : ""}
  onchange="updateSubNavigation(${element.id}, this.checked ? 1 : 0)">
                <label class="form-check-label">
                   ${element.published == true ? "published" : "unpublish"} 
                </label>
            </div>
            </span>
        </li>
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

export const getNavigation = async (id) => {
    let url = `/get-nav`;
    let html = "<option selected disabled>Choose a menu</option>";

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

    console.log("Submitting PATCH for subId:", subId, "value:", selectedValue);

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

        const data = await response.json();
        console.log("Success:", data);

        Toastify({
            text: data.message || "Successfully updated",
            backgroundColor: "green",
            duration: 3000,
        }).showToast();

        getMenu();
    } catch (err) {
        console.error("Network error:", err);
    }
};

window.changeMenu = changeMenu;
export const getSubmenus = (subId) => {
    alert("Submenus updated for ID: " + subId);
};

window.changeMenu = changeMenu;

export const deleteSubMenu = async (id) => {
    if (!confirm("Are you sure you want to delete this submenu?" + id)) {
        return;
    }
    console.log("Deleting submenu with ID:", id);
    try {
        const response = await fetch(`/navigation/submenu/delete/${id}`, {
            method: "DELETE",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            console.error(
                "Error deleting submenu:",
                response.statusText,
                errorData
            );
            return;
        }

        const data = await response.json();
        console.log("Submenu deleted successfully:", data);

        const el = document.getElementById(`submenus_${id}`);

        if (el) {
            el.classList.add("opacity-0");
            setTimeout(() => el.remove(), 300);
        }

        Toastify({
            text: data.message || "Submenu deleted successfully",
            backgroundColor: "green",
            duration: 3000,
        }).showToast();
    } catch (err) {
        console.error("Network error:", err);
    }
};

window.deleteSubMenu = deleteSubMenu;
// update status of submenu
export const updateSubNavigation = async (id, status) => {
    if (!confirm("Are you sure you want to update this submenu?")) {
        return;
    }

    let url = `/navigation/submenu/update-status/${id}`;

    const response = await fetch(`/navigation/submenu/update-status/${id}`, {
        method: "PATCH",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({ published: status }),
    });
    if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        console.error(
            "Error updating submenu status:",
            response.statusText,
            errorData
        );
        return;
    }

    const data = await response.json();
};
window.updateSubNavigation = updateSubNavigation;
// Initialize the menu when the script loads
