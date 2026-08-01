import { ref } from 'vue';
import { useToast } from 'vue-toastification';
import useFooterSocial from './useFooterSocial';

const loaded = ref(false);

export default function useSocialManager(socials) {
    const toast = useToast();
    const saving = ref(false);
    const showDeleteModal = ref(false);
    const deleteIndex = ref(null);

    const { getSocial, saveSocials } = useFooterSocial();

    const loadSocial = async (force = false) => {
        if (loaded.value && !force) return;

        const data = await getSocial();

        socials.splice(
            0,
            socials.length,
            ...data.map((item) => ({
                id: item.id,
                name: item.name,
                url: item.url,
                icon: null,
                iconPreview: item.icon,
                original: {
                    name: item.name,
                    url: item.url,
                    iconPreview: item.icon
                },
                editing: false,
                isNew: false,
                mode: 'update'
            }))
        );

        loaded.value = true;
    };

    const saveSocial = async (item) => {
        if (saving.value) return;

        saving.value = true;

        const action = item.mode === 'create' ? 'creating' : 'updating';

        const toastId = toast.info(
            action === 'creating' ? 'Creating social...' : 'Updating social...',
            {
                timeout: false
            }
        );

        try {
            await saveSocials(socials);

            await loadSocial(true);

            toast.dismiss(toastId);

            toast.success(
                action === 'creating'
                    ? 'Social created successfully'
                    : 'Social updated successfully'
            );
        } catch (error) {
            console.log(error);

            toast.dismiss(toastId);

            toast.error(
                action === 'creating'
                    ? 'Failed to create social'
                    : 'Failed to update social'
            );
        } finally {
            saving.value = false;
        }
    };

    const confirmDelete = (index) => {
        deleteIndex.value = index;
        showDeleteModal.value = true;
    };

    const deleteSocial = async () => {
        const index = deleteIndex.value;

        if (index === null) return;

        const toastId = toast.info('Deleting social...', {
            timeout: false
        });

        socials.splice(index, 1);

        showDeleteModal.value = false;

        try {
            await saveSocials(socials);

            toast.dismiss(toastId);

            toast.success('Social deleted successfully');
        } catch (error) {
            console.log(error);

            toast.dismiss(toastId);

            toast.error('Failed to delete social');
        }

        deleteIndex.value = null;
    };
    const cancelDelete = () => {
        showDeleteModal.value = false;
        deleteIndex.value = null;
    };

    return {
        saving,
        showDeleteModal,
        loadSocial,
        saveSocial,
        confirmDelete,
        deleteSocial,
        cancelDelete
    };
}
