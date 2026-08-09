import { ref } from 'vue';
import { useToast } from 'vue-toastification';

export function useRoleTable(emit) {
    const toast = useToast();

    const creating = ref(false);

    const newRole = ref({
        role_name: '',
        description: ''
    });

    const editingRoleId = ref(null);
    const editedName = ref('');
    const editedDescription = ref('');

    function addRole() {
        if (creating.value) {
            toast.info(
                'Please complete the current role before adding a new one.'
            );
            return;
        }

        creating.value = true;

        newRole.value = {
            role_name: '',
            description: ''
        };
    }

    function saveCreate() {
        if (
            !newRole.value.role_name.trim() ||
            !newRole.value.description.trim()
        ) {
            toast.warning('Please enter full role name and description.');
            return;
        }

        emit('create', {
            role_name: newRole.value.role_name,
            description: newRole.value.description
        });

        cancelCreate();
    }

    function cancelCreate() {
        creating.value = false;

        newRole.value = {
            role_name: '',
            description: ''
        };
    }

    function edit(role) {
        if (role.role_name.toLowerCase() === 'admin') {
            toast.info('The Admin role cannot be modified.');
            return;
        }

        editingRoleId.value = role.id;
        editedName.value = role.role_name;
        editedDescription.value = role.description ?? '';
    }

    function save(role) {
        emit('update', {
            id: role.id,
            role_name: editedName.value,
            description: editedDescription.value
        });

        cancel();
    }

    function cancel() {
        editingRoleId.value = null;
        editedName.value = '';
        editedDescription.value = '';
    }

    function handleDelete(role) {
        if (role.role_name.toLowerCase() === 'admin') {
            toast.info('The Admin role cannot be deleted.');
            return;
        }

        if (confirm(`Are you sure you want to delete "${role.role_name}"?`)) {
            emit('delete', role);
        }
    }

    return {
        creating,
        newRole,
        editingRoleId,
        editedName,
        editedDescription,
        addRole,
        saveCreate,
        cancelCreate,
        edit,
        save,
        cancel,
        handleDelete
    };
}
