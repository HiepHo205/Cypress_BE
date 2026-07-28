<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import { Edit } from 'lucide-vue-next';
import useFooterNewsletter from '@/composables/footer/useFooterNewsletter';

const emit = defineEmits(['loading']);
const toast = useToast();

const saving = ref(false);
const editing = ref(false);
const hasData = ref(false);
const iconFile = ref(null);
const iconPreview = ref(null);
const backup = ref(null);

const form = reactive({
    title: '',
    description: '',
    placeholder: '',
    button_icon: null
});

const {
    getNewsletter,
    updateNewsletter
} = useFooterNewsletter();

const setForm = (data) => {
    form.title = data?.title ?? '';
    form.description = data?.description ?? '';
    form.placeholder = data?.placeholder ?? '';
    form.button_icon = data?.button_icon ?? null;
    iconPreview.value = data?.button_icon ?? null;
};

const saveBackup = () => {
    backup.value = {
        title: form.title,
        description: form.description,
        placeholder: form.placeholder,
        button_icon: form.button_icon
    };
};
onMounted(async () => {
    emit('loading', true);

    try {
        const data = await getNewsletter();

        console.log(data);

        hasData.value = data !== null;

        if (data) {
            setForm(data);
            saveBackup();
        }
    } catch (error) {
        console.log(error);
        toast.error('Failed to load newsletter');
        hasData.value = false;
    } finally {
        emit('loading', false);
    }
});

const editNewsletter = () => {
    saveBackup();
    editing.value = true;
};


const cancelEdit = () => {

    if (backup.value) {

        form.title = backup.value.title;
        form.description = backup.value.description;
        form.placeholder = backup.value.placeholder;
        form.button_icon = backup.value.button_icon;

        iconPreview.value = backup.value.button_icon;

    }

    iconFile.value = null;
    editing.value = false;

};


const handleIconChange = (event) => {

    const file = event.target.files[0];

    if (!file) return;

    iconFile.value = file;
    iconPreview.value = URL.createObjectURL(file);

};


const saveNewsletter = async () => {

    saving.value = true;

    const toastId = toast.info('Updating newsletter...', {
        timeout: false
    });

    try {

        const payload = {
            title: form.title,
            description: form.description,
            placeholder: form.placeholder
        };
        if (iconFile.value instanceof File) {
            payload.button_icon = iconFile.value;
        }
        await updateNewsletter(payload);
        hasData.value = true;
        toast.dismiss(toastId);
        saveBackup();
        iconFile.value = null;
        editing.value = false;

        toast.success('Newsletter updated successfully');

    } catch (error) {

        toast.dismiss(toastId);

        console.log('UPDATE ERROR:', error);

        toast.error(
            error.message || 'Update failed'
        );

    } finally {

        saving.value = false;

    }

};
</script>
<template>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Newsletter
                </h2>
                <p class="mt-2 text-sm text-gray-500">
                    Configure newsletter footer section.
                </p>
            </div>
            <button v-if="!editing" @click="editNewsletter"
                class="flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-white hover:bg-blue-700">
                <Edit :size="16" />
                Update
            </button>
        </div>

        <div class="rounded-xl bg-white p-3 border border-gray-100">
            <div v-if="!hasData && !editing" class="flex items-center justify-center py-16">
                <p class="text-gray-400 text-lg">
                    No data
                </p>
            </div>

            <div v-else class="space-y-6">
                <div>
                    <label class="block mb-1 text-sm font-medium">
                        Section Title
                    </label>

                    <input v-model="form.title" :disabled="!editing" placeholder="Enter newsletter title"
                        class="w-full rounded-lg border border-gray-100 px-2 py-1 disabled:bg-gray-100" />
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">
                        Description
                    </label>

                    <textarea v-model="form.description" :disabled="!editing" rows="4"
                        placeholder="Enter newsletter description"
                        class="w-full rounded-lg border border-gray-100 px-2 py-1 disabled:bg-gray-100" />
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">
                        Email Placeholder
                    </label>

                    <input v-model="form.placeholder" :disabled="!editing" placeholder="example@email.com"
                        class="w-full rounded-lg border border-gray-100 px-2 py-1 disabled:bg-gray-100" />
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">
                        Button Icon
                    </label>

                    <label
                        class="h-16 w-16 rounded-lg border border-gray-100 flex items-center justify-center overflow-hidden cursor-pointer">
                        <img v-if="iconPreview" :src="iconPreview" class="h-full w-full object-contain" />

                        <span v-else class="text-gray-400 text-sm">
                            None
                        </span>

                        <input v-if="editing" hidden type="file" accept="image/png,image/jpeg,image/svg+xml"
                            @change="handleIconChange" />
                    </label>
                </div>

                <div v-if="editing" class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button @click="cancelEdit" class="rounded-lg bg-gray-500 px-6 py-3 text-white">
                        Cancel
                    </button>

                    <button @click="saveNewsletter" :disabled="saving"
                        class="rounded-lg bg-blue-600 px-6 py-3 text-white disabled:opacity-50">
                        {{ saving ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>