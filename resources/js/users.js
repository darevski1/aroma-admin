import Toastify from "toastify-js";

// Get All users from db in admin panel

export const getUsers = async () => {
    let url = "/get-users";

    try {
        let response = await fetch(url);
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }

        let data = await response.json();
        data.forEach((element) => {
            let html = `
            <tr id="user_${element.id}">
                <td>${element.id}</td>
                <td>${element.name}</td>
                <td>${element.email}</td>
                <td>${element.user_role}</td>
                <td>
                    <a href="/edit-user/${element.id}" class="btn btn-primary">Edit</a>
                    <button class="btn btn-danger" onclick="deleteUser(${element.id})">Delete</button>
                </td>
            </tr>`;
            document.getElementById("usersTable").innerHTML += html;
        });
    } catch (error) {}
};
window.getUsers = getUsers;

// Delete user function
export const deleteUser = async (id) => {
    let url = `/delete-user/${id}`;
    try {
        let response = await fetch(url, {
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
        // Assuming the response contains a success message
        const data = await response.json();
        // Check if the element with the ID exists
        const el = document.getElementById(`user_${id}`);
        // If the element exists, add a class to fade it out and then remove it
        if (el) {
            el.classList.add("opacity-1");
            setTimeout(() => el.remove(), 500);
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

window.deleteUser = deleteUser;
