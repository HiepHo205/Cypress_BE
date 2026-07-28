import { reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';

import useFooterBottomBar from './useFooterBottomBar';

export default function useFooterBottomBarManager() {
    const toast = useToast();

    const { getBottomBar, updateBottomBar } = useFooterBottomBar();

    const saving = ref(false);
    const editing = ref(false);
    const hasData = ref(false);

    const backup = ref(null);

    const form = reactive({
        copyright: '',
        legal_links: []
    });
    const setForm = (data) => {
        form.copyright = data?.copyright ?? '';

        form.legal_links = (data?.legal_links ?? []).map((link) => ({
            text: link.text,
            url: link.url,
            isNew: false
        }));
    };

    const saveBackup = () => {
        backup.value = JSON.parse(JSON.stringify(form));
    };

    const loadBottomBar = async () => {
        const data = await getBottomBar();

        if (data) {
            setForm(data);

            saveBackup();

            hasData.value = true;
        } else {
            hasData.value = false;
        }
    };

    const editBottomBar = () => {
        saveBackup();

        editing.value = true;
    };

    const cancelEdit = () => {
        if (backup.value) {
            form.copyright = backup.value.copyright;

            form.legal_links = JSON.parse(
                JSON.stringify(backup.value.legal_links)
            );
        }

        editing.value = false;
    };

    const addLegalLink = () => {
        form.legal_links.push({
            text: '',
            url: '',
            isNew: true
        });
    };

    const removeNewLegalLink = (index) => {
        form.legal_links.splice(index, 1);
    };

    const removeExistingLegalLink = async (index) => {
        form.legal_links.splice(index, 1);

        const toastId = toast.info('Removing legal link...', {
            timeout: false
        });

        try {
            await updateBottomBar({
                copyright: form.copyright,
                legal_links: form.legal_links.map((link) => ({
                    text: link.text,
                    url: link.url
                }))
            });

            toast.dismiss(toastId);
            toast.success('Legal link removed successfully');
        } catch (error) {
            toast.dismiss(toastId);

            console.log(error);

            toast.error('Remove failed');
        }
    };

    const saveBottomBar = async () => {
        saving.value = true;

        const toastId = toast.info('Updating footer bottom bar...', {
            timeout: false
        });

        try {
            await updateBottomBar({
                copyright: form.copyright,
                legal_links: form.legal_links.map((link) => ({
                    text: link.text.trim(),
                    url: link.url.trim()
                }))
            });

            form.legal_links = form.legal_links.map((link) => ({
                text: link.text,
                url: link.url,
                isNew: false
            }));

            toast.dismiss(toastId);

            saveBackup();

            editing.value = false;

            toast.success('Footer bottom bar updated successfully');
        } catch (error) {
            toast.dismiss(toastId);

            console.log(error);

            toast.error(error.message || 'Update failed');
        } finally {
            saving.value = false;
        }
    };
    return {
        form,
        saving,
        editing,
        hasData,
        loadBottomBar,
        editBottomBar,
        cancelEdit,
        addLegalLink,
        removeNewLegalLink,
        removeExistingLegalLink,
        saveBottomBar
    };
}
