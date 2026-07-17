document.addEventListener('DOMContentLoaded', () => {
    const page = document.getElementById('users-page');

    if (!page) {
        return;
    }

    const endpoint = page.dataset.graphqlEndpoint || '/graphql/graphql';
    const form = document.getElementById('user-form');
    const feedback = document.getElementById('users-feedback');
    const tbody = document.getElementById('users-body');
    const submitButton = form?.querySelector('button[type="submit"]');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    let editingUserId = null;

    async function gql(query, variables = {}) {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({ query, variables }),
        });

        const payload = await response.json();

        if (payload.errors) {
            throw new Error(payload.errors[0]?.message || 'GraphQL request failed.');
        }

        return payload.data;
    }

    function setFeedback(message, type = 'info') {
        if (!feedback) {
            return;
        }

        feedback.textContent = message;
        feedback.style.color = type === 'error' ? '#b91c1c' : '#2563eb';
    }

    function resetForm() {
        if (form) {
            form.reset();
        }

        editingUserId = null;

        if (submitButton) {
            submitButton.textContent = 'Create User';
        }
    }

    async function loadUsers() {
        if (!tbody) {
            return;
        }

        tbody.innerHTML = '<tr><td colspan="4" class="muted">Loading users...</td></tr>';
        setFeedback('Loading users...');

        try {
            const data = await gql(`query { users { data { id name email status } paginatorInfo { total currentPage lastPage } } }`);
            const rows = data.users?.data || [];

            if (!rows.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="muted">No users found.</td></tr>';
                setFeedback('No users available yet.');
                return;
            }

            tbody.innerHTML = rows
                .map(
                    (user) => `
                        <tr>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td><span style="color:${user.status === 'unactive' ? '#b91c1c' : '#166534'};">${user.status || 'active'}</span></td>
                            <td>
                                <button type="button" class="btn btn-secondary" data-action="edit" data-id="${user.id}" data-name="${user.name}" data-email="${user.email}">Edit</button>
                                <button type="button" class="btn" data-action="deactivate" data-id="${user.id}">Lock</button>
                                <button type="button" class="btn btn-secondary" data-action="delete" data-id="${user.id}">Delete</button>
                            </td>
                        </tr>
                    `,
                )
                .join('');

            setFeedback(`Loaded ${rows.length} user(s).`);
        } catch (error) {
            console.error(error);
            tbody.innerHTML = '<tr><td colspan="4" class="muted">Unable to load users. Please check the GraphQL endpoint.</td></tr>';
            setFeedback(error.message || 'Unable to load users.', 'error');
        }
    }

    if (form) {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const name = nameInput?.value.trim();
            const email = emailInput?.value.trim();
            const password = passwordInput?.value || '';

            if (!name || !email) {
                setFeedback('Name and email are required.', 'error');
                return;
            }

            if (!editingUserId && !password) {
                setFeedback('Password is required when creating a user.', 'error');
                return;
            }

            try {
                if (editingUserId) {
                    const payload = {};

                    if (name) {
                        payload.name = name;
                    }

                    if (email) {
                        payload.email = email;
                    }

                    if (password) {
                        payload.password = password;
                    }

                    await gql(`mutation UpdateUser($id: ID!, $input: UpdateUserInput!) { updateUser(id: $id, input: $input) { user { id name email status } } }`, {
                        id: editingUserId,
                        input: payload,
                    });

                    setFeedback('User updated successfully.');
                } else {
                    await gql(`mutation CreateUser($input: CreateUserInput!) { createUser(input: $input) { user { id name email status } } }`, {
                        input: { name, email, password },
                    });

                    setFeedback('User created successfully.');
                }

                resetForm();
                await loadUsers();
            } catch (error) {
                console.error(error);
                setFeedback(error.message || (editingUserId ? 'Unable to update user.' : 'Unable to create user.'), 'error');
            }
        });
    }

    page.addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-action]');

        if (!button) {
            return;
        }

        const action = button.dataset.action;
        const id = button.dataset.id;

        if (action === 'delete') {
            try {
                await gql(`mutation DeleteUser($id: ID!) { deleteUser(id: $id) { user { id } } }`, { id });
                setFeedback('User deleted successfully.');
                await loadUsers();
            } catch (error) {
                setFeedback(error.message || 'Unable to delete user.', 'error');
            }
            return;
        }

        if (action === 'deactivate') {
            try {
                await gql(`mutation DeactivateUser($id: ID!) { deactivateUser(id: $id) { user { id status } } }`, { id });
                setFeedback('User deactivated successfully.');
                await loadUsers();
            } catch (error) {
                setFeedback(error.message || 'Unable to deactivate user.', 'error');
            }
            return;
        }

        if (action === 'edit') {
            const name = button.dataset.name;
            const email = button.dataset.email;
            editingUserId = id;
            nameInput.value = name;
            emailInput.value = email;
            passwordInput.value = '';
            submitButton.textContent = 'Update User';
            setFeedback(`Editing ${name}. Enter a new password if needed.`);
        }
    });

    loadUsers();
});
