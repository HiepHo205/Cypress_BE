<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { Mail, Lock, Eye, EyeOff } from 'lucide-vue-next';
import { useToast } from 'vue-toastification';

const toast = useToast();
const router = useRouter();

const email = ref('');
const password = ref('');
const loading = ref(false);
const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const handleLogin = async () => {
    if (!email.value || !password.value) {
        toast.error('Please enter your email and password.');
        return;
    }

    loading.value = true;

    const query = `
    mutation Login($input: LoginInput!) {
        login(input: $input) {
            status
            message
            access_token
            token_type
            expires_in
            user {
                id
                name
                full_name
                email
                status

                role {
                    id
                    role_name
                }

                permissions {
                    id
                    code
                    description
                }
            }
        }
    }
`;

    try {
        const response = await fetch('/graphql', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                query,
                variables: {
                    input: {
                        email: email.value,
                        password: password.value,
                    },
                },
            }),
        });

        const result = await response.json();

        console.log('GraphQL response:', result);

        if (result.errors?.length) {
            toast.error(result.errors[0].message);
            return;
        }

        const login = result?.data?.login;

        if (!login) {
            toast.error('Invalid response from server.');
            return;
        }

        if (!login.status) {
            toast.error(login.message);
            return;
        }

        const isAdmin = login.user?.role?.role_name === 'admin';

        if (!isAdmin) {
            toast.warning('Only admin accounts can access this page.');
            return;
        }

        localStorage.setItem('token', login.access_token);
        localStorage.setItem('user', JSON.stringify(login.user));

        toast.success(login.message || 'Login successful!');

        setTimeout(() => {
            router.push('/admin');
        }, 1000);
    } catch (error) {
        console.error(error);
        toast.error('Something went wrong. Please try again.');
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-blue-50">
        <div class="w-[800px] h-[550px] bg-white rounded-2xl shadow-xl flex overflow-hidden">

            <div
                class="w-1/2 bg-gradient-to-br from-indigo-500 to-blue-400 text-white p-10 flex flex-col justify-center">

                <div class="mb-10">
                    <div
                        class="w-12 h-12 bg-white text-indigo-500 rounded-xl flex items-center justify-center font-bold text-xl mb-4">
                        C
                    </div>

                    <h2 class="font-semibold text-xl">
                        Cypress Hub
                    </h2>
                </div>

                <h1 class="text-4xl font-bold leading-tight mb-6">
                    Adventure
                    <br />
                    starts here
                </h1>

                <p class="text-sm opacity-90">
                    Manage your Cypress Hub easily.
                </p>

            </div>

            <div class="w-1/2 p-8 flex flex-col justify-center">

                <h2 class="text-2xl font-semibold text-center mb-2">
                    Welcome back
                </h2>

                <p class="text-center text-gray-500 mb-6 text-sm">
                    Access your admin portal
                </p>

                <form @submit.prevent="handleLogin" class="space-y-4">

                    <div>
                        <label class="text-sm font-bold">
                            Email
                        </label>

                        <div class="relative mt-1">
                            <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />

                            <input v-model="email" type="email" placeholder="Enter your email"
                                class="w-full h-11 pl-10 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-indigo-400" />
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-bold">
                            Password
                        </label>

                        <div class="relative mt-1">
                            <Lock class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 z-10" />

                            <input v-model="password" :type="showPassword ? 'text' : 'password'"
                                placeholder="Enter your password"
                                class="w-full h-11 pl-10 pr-10 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-indigo-400" />

                            <Eye v-if="!showPassword" @click="togglePasswordVisibility"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 cursor-pointer z-10" />

                            <EyeOff v-else @click="togglePasswordVisibility"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 cursor-pointer z-10" />
                        </div>
                    </div>

                    <button type="submit" :disabled="loading"
                        class="w-full h-11 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg">
                        {{ loading ? 'Logging in...' : 'Login' }}
                    </button>

                </form>

            </div>

        </div>
    </div>
</template>