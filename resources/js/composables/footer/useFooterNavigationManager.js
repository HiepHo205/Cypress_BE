import { reactive, ref } from 'vue';
import useFooterNavigation from './useFooterNavigation';

export default function useFooterNavigationManager() {
    const navigationGroups = reactive([]);
    const selectedGroup = ref(null);
    const saving = ref(false);
    const pageLoading = ref(true);
    const editing = ref(false);
    const isNewGroup = ref(false);

    const { getNavigation, saveNavigation } = useFooterNavigation();

    const loadNavigation = async () => {
        try {
            const response = await getNavigation();
            const items = response?.navigations ?? [];

            const groups = {};

            items.forEach((item) => {
                const groupName = item.group || 'Company';

                if (!groups[groupName]) {
                    groups[groupName] = {
                        id: item.id,
                        name: groupName,
                        items: []
                    };
                }

                groups[groupName].items.push({
                    id: item.id,
                    title: item.title ?? '',
                    link: item.link ?? '',
                    type: item.type ?? 'link'
                });
            });

            navigationGroups.splice(0);
            navigationGroups.push(...Object.values(groups));
        } finally {
            pageLoading.value = false;
        }
    };

    const addGroup = () => {
        selectedGroup.value = {
            id: null,
            name: 'New Group',
            items: [
                {
                    id: null,
                    title: '',
                    link: '',
                    type: 'link'
                }
            ]
        };

        editing.value = true;
        isNewGroup.value = true;
    };

    const openGroup = (group) => {
        selectedGroup.value = {
            id: group.id,
            name: group.name,
            items: group.items.map((item) => ({
                id: item.id,
                title: item.title,
                link: item.link,
                type: item.type
            }))
        };

        editing.value = false;
        isNewGroup.value = false;
    };

    const addItem = (group) => {
        group.items.push({
            id: null,
            title: '',
            link: '',
            type: 'link'
        });
    };

    const removeItem = (index) => {
        selectedGroup.value.items.splice(index, 1);
    };

    const removeGroup = async (group = selectedGroup.value) => {
        const groups = navigationGroups.filter((item) => item.id !== group.id);

        const payload = groups.flatMap((group) =>
            group.items.map((item) => ({
                id: item.id,
                group: group.name,
                title: item.title,
                link: item.link,
                type: item.type
            }))
        );

        await saveNavigation(payload);

        await loadNavigation();

        selectedGroup.value = null;
        editing.value = false;
        isNewGroup.value = false;
    };

    const validate = () => {
        if (!selectedGroup.value.name.trim()) {
            throw new Error('Please enter a group name.');
        }

        if (selectedGroup.value.name === 'New Group') {
            throw new Error('Please change the group name.');
        }

        if (!selectedGroup.value.items.length) {
            throw new Error('Please add at least one navigation.');
        }

        for (const item of selectedGroup.value.items) {
            if (!item.title.trim()) {
                throw new Error('Navigation title is required.');
            }

            if (!item.link.trim()) {
                throw new Error('Navigation link is required.');
            }
        }
    };

    const saveGroup = async () => {
        if (saving.value) return;

        validate();

        saving.value = true;

        const action = isNewGroup.value ? 'create' : 'update';

        try {
            const groups = navigationGroups.map((group) => ({
                id: group.id,
                name: group.name,
                items: group.items.map((item) => ({
                    id: item.id,
                    title: item.title,
                    link: item.link,
                    type: item.type
                }))
            }));

            if (action === 'create') {
                groups.push({
                    id: null,
                    name: selectedGroup.value.name,
                    items: selectedGroup.value.items.map((item) => ({
                        id: item.id,
                        title: item.title,
                        link: item.link,
                        type: item.type
                    }))
                });
            } else {
                const index = groups.findIndex(
                    (group) => group.id === selectedGroup.value.id
                );

                if (index !== -1) {
                    groups[index] = {
                        id: selectedGroup.value.id,
                        name: selectedGroup.value.name,
                        items: selectedGroup.value.items.map((item) => ({
                            id: item.id,
                            title: item.title,
                            link: item.link,
                            type: item.type
                        }))
                    };
                }
            }

            const payload = groups.flatMap((group) =>
                group.items.map((item) => ({
                    id: item.id,
                    group: group.name,
                    title: item.title,
                    link: item.link,
                    type: item.type
                }))
            );

            await saveNavigation(payload);

            await loadNavigation();

            selectedGroup.value = null;
            editing.value = false;
            isNewGroup.value = false;

            return action;
        } finally {
            saving.value = false;
        }
    };

    return {
        navigationGroups,
        selectedGroup,
        saving,
        pageLoading,
        editing,
        isNewGroup,
        loadNavigation,
        addGroup,
        openGroup,
        addItem,
        removeItem,
        removeGroup,
        saveGroup
    };
}
