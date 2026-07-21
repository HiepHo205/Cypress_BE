<template>
    <aside class="w-64 min-h-screen bg-slate-900 text-white">
        <div class="p-6 border-b border-slate-800">
            <h1 class="text-2xl font-bold">
                Cypress
            </h1>
        </div>

        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <router-link
                        to="/admin"
                        class="flex items-center px-4 py-3 rounded-lg transition hover:bg-slate-800"
                    >
                        Dashboard
                    </router-link>
                </li>

                <li>
                    <router-link
                        to="/admin/users"
                        class="flex items-center px-4 py-3 rounded-lg transition hover:bg-slate-800"
                    >
                        Users
                    </router-link>
                </li>
            </ul>
        </nav>
    </aside>
</template>