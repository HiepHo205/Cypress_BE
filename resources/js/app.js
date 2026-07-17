import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { provideApolloClient } from './apollo'

const app = createApp(App)

provideApolloClient(app)

app.use(router)

app.mount('#app')