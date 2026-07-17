document.addEventListener('DOMContentLoaded', () => {
    const page = document.getElementById('users-page');

    if (!page) {
        return;
    }

    const endpoint = page.dataset.graphqlEndpoint || '/graphql/graphql';
    const form = document.getElementById('user-form');
    const feedback = document.getElementById('users-feedback');
    const tbody = document.getElementById('users-body');

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

    async function loadUsers() {
        if (!tbody) {
            return;
        }

        tbody.innerHTML = '<tr><td colspan="3" class="muted">Loading users...</td></tr>';
        setFeedback('Loading users...');

        try {
            const data = await gql(`query { users { data { id name email } paginatorInfo { total currentPage lastPage } } }`);
            const rows = data.users?.data || [];

            if (!rows.length) {
                tbody.innerHTML = '<tr><td colspan="3" class="muted">No users found.</td></tr>';
                setFeedback('No users available yet.');
                return;
            }

            tbody.innerHTML = rows
                .map(
                    (user) => `
                        <tr>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td><span style="color:#166534;">Active</span></td>
                        </tr>
                    `,
                )
                .join('');

            setFeedback(`Loaded ${rows.length} user(s).`);
        } catch (error) {
            console.error(error);
            tbody.innerHTML = '<tr><td colspan="3" class="muted">Unable to load users. Please check the GraphQL endpoint.</td></tr>';
            setFeedback(error.message || 'Unable to load users.', 'error');
        }
    }

    if (form) {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            if (!name || !email || !password) {
                setFeedback('Please fill in all fields.', 'error');
                return;
            }

            try {
                await gql(`mutation CreateUser($input: CreateUserInput!) { createUser(input: $input) { user { id name email } } }`, {
                    input: { name, email, password },
                });

                form.reset();
                setFeedback('User created successfully.');
                await loadUsers();
            } catch (error) {
                console.error(error);
                setFeedback(error.message || 'Unable to create user.', 'error');
            }
        });
    }

    loadUsers();
});
