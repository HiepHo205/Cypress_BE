<script setup>
import { ref } from 'vue'
import { Mail, Lock } from "lucide-vue-next";
const email = ref('')
const errorMessage = ref('')
const successMessage = ref('')
const password = ref('')
const loading = ref(false)

const handleLogin = async () => {
    errorMessage.value = ''
    successMessage.value = ''
    if (!email.value || !password.value) {
        errorMessage.value = 'Please enter your email and password.';
        return
    }

    loading.value = true

    const query = `
    mutation Login($email: String!, $password: String!) {
        login(email: $email, password: $password) {
            status
            message
            access_token
            token_type
            expires_in
            user {
                id
                full_name
                email
                status
                roles {
                    id
                    name
                }
            }
        }
    }`

    try {
        const res = await fetch('/graphql', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json'
            },
            body: JSON.stringify({
                query,
                variables: {
                    email: email.value,
                    password: password.value
                }
            })
        })

        const result = await res.json()

        if (result.errors && result.errors.length > 0) {
            errorMessage.value = result.errors[0].message || 'Invalid email or password.';
            return
        }

        const login = result.data.login

        if (!login.status) {
            errorMessage.value = login.message;
            return
        }

        const isAdmin = login.user.roles.some(r => r.name === 'admin')

        if (!isAdmin) {
            errorMessage.value = 'Access denied! Only Admin can login.';
            return
        }
        localStorage.setItem('token', login.access_token)
        localStorage.setItem('user', JSON.stringify(login.user))
        alert('Login successful! Redirecting to dashboard...');
        window.location.href = '/dashboard'

    } catch (err) {
        console.error('Login error:', err)
        errorMessage.value = 'Something went wrong. Please try again.';
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-blue-50">

        <div class="w-[800px] h-[550px] bg-white rounded-2xl shadow-xl flex overflow-hidden">

            <div
                class="w-1/2 bg-gradient-to-br from-indigo-500 to-blue-400 text-white p-10 relative flex flex-col justify-center">

                <div class="absolute top-6 left-6 flex items-center gap-2">
                    <div
                        class="w-12 h-12 bg-white text-indigo-500 flex items-center justify-center rounded-xl font-bold text-xl">
                        C
                    </div>
                    <h2 class="font-semibold">Cypress Hub</h2>
                </div>

                <h1 class="text-4xl font-bold leading-tight mb-6">
                    Adventure <br /> starts here
                </h1>

                <p class="text-sm opacity-90">
                    Manage your CMS platform, content and administration in one place.
                </p>

            </div>

            <div class="w-1/2 p-8 flex flex-col justify-center">

                <h2 class="text-2xl font-semibold text-center mb-2">
                    Hello! Welcome back
                </h2>

                <p class="text-center text-gray-500 mb-6 text-sm">
                    Login to access Admin Portal
                </p>

                <div v-if="errorMessage"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ errorMessage }}</span>
                </div>

                <div v-if="successMessage"
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ successMessage }}</span>
                </div>
                <form @submit.prevent="handleLogin" class="space-y-4">

                    <div>
                        <label class="text-sm text-black-100 font-bold">Email</label>

                        <div class="relative mt-1">
                            <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />

                            <input v-model="email" type="email"
                                class="w-full h-11 pl-10 pr-3 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                placeholder="Enter your email" />
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-black-100 font-bold">Password</label>

                        <div class="relative mt-1">
                            <Lock class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />

                            <input v-model="password" type="password"
                                class="w-full h-11 pl-10 pr-3 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                placeholder="••••••••" />
                        </div>
                    </div>

                    <div class="flex justify-between text-xs">
                        <label class="flex items-center gap-1">

                            <input type="checkbox" /> Remember me
                        </label>

                        <a href="#" class="text-indigo-500">Forgot password?</a>
                    </div>

                    <button type="submit" :disabled="loading"
                        class="w-full h-11 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition">
                        {{ loading ? 'Logging in...' : 'Login' }}
                    </button>

                </form>

                <div class="flex items-center my-6">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="px-2 text-xs text-gray-400">or</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <div class="flex justify-center gap-3">
                    <button class="w-10 h-10 border border-gray-300 rounded-lg flex items-center justify-center">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" />
                    </button>

                    <button class="w-10 h-10 border border-gray-300 rounded-lg flex items-center justify-center">
                        <img src="https://www.svgrepo.com/show/475647/facebook-color.svg" class="w-5 h-5" />
                    </button>

                    <button class="w-10 h-10 border border-gray-300 rounded-lg flex items-center justify-center">
                        <img src="https://images.openai.com/static-rsc-4/K7mkXKpTc7vTHCPB2ZgBxq57kq5k8Qgmfw8GxMJ4lrf43bonSfcg3rSGVOC8qtAxKtzzsP9n78lX9m1LPxZuq2a8uCuKsnc8w5GJfpXxrDjN6RA8xdtA7ZPUI7UYzThX0tvCTrxO5Ll5asgOWvyJp40SEibKZewKFZdypoEgVhc?purpose=inline"
                            class="w-5 h-5" />
                    </button>
                </div>

            </div>

        </div>

    </div>
</template>